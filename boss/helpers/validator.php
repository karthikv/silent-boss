<?php

   class Validator {

      // validation and display data
      private $data;
      private $display;
      private $rules;
      private $errorMessage;

      // delimiters
      private $beforeErrorMessages;
      private $afterErrorMessages;

      private $errorMessageStartDelim;
      private $errorMessageEndDelim;

      private $beforeSuccessMessage;
      private $afterSuccessMessage;

      private $beforeInfoMessage;
      private $afterInfoMessage;
      
      public function Validator( &$data ) {
         global $controller;
         $controller->load->helper( 'formkey' );
         
         $this->data = $data;
         foreach( $this->data as $key => $value )
            $this->data[ $key ] = trim( $value );

         $this->rules = array();
         $this->errorMessage = '';

         $this->setBeforeErrorMessages( '<div class="error-container error"><p>Please correct the' 
            . ' following errors:</p><ul>' );
         $this->setAfterErrorMessages( '</ul></div>' );

         $this->setErrorMessageStartDelim( '<li>' );
         $this->setErrorMessageEndDelim( '</li>' );

         $this->setBeforeSuccessMessage( '<p class="success">' );
         $this->setAfterSuccessMessage( '</p>' );

         $this->setBeforeInfoMessage( '<p class="info">' );
         $this->setAfterInfoMessage( '</p>' );
      }

      public function setBeforeErrorMessages( $str ) {
         $this->beforeErrorMessages = $str;
      }

      public function setAfterErrorMessages( $str ) {
         $this->afterErrorMessages = $str;
      }

      public function setErrorMessageStartDelim( $delim ) {
         $this->errorMessageStartDelim = $delim;
      }

      public function setErrorMessageEndDelim( $delim ) {
         $this->errorMessageEndDelim = $delim;
      }

      public function setBeforeSuccessMessage( $str ) {
         $this->beforeSuccessMessage = $str; 
      }

      public function setAfterSuccessMessage( $str ) {
         $this->afterSuccessMessage = $str;
      }

      public function setBeforeInfoMessage( $str ) {
         $this->beforeInfoMessage = $str;
      }

      public function setAfterInfoMessage( $str ) {
         $this->afterInfoMessage = $str;
      }

      public function setRules( $name, $display, $rules ) {
         $this->rules[ $name ] = $rules;
         $this->display[ $name ] = $display;
      }

      public function removeRules( $name ) {
         unset( $this->rules[ $name ] );
      }

      public function validate( $storeDataInSession = true ) {
         $isValid = true;
         $this->errorMessage = $this->beforeErrorMessages;

         // automatically validate form key
         $formKey = new FormKey();
         if( isset( $this->data[ 'formKey' ] ) && !$formKey->isValid() ) {
            $this->addErrorMessage( 'Form Key', 'was missing or incorrect. Please try again' );
            $isValid = false;
         }
         else {
            // process all rules
            foreach( $this->rules as $name => $rules ) {
               $rules = explode( '|', $rules );

               $display = $name;
               if( isset( $this->display[ $name ] ) )
                  $display = $this->display[ $name ];

               $value = '';
               if( isset( $this->data[ $name ] ) )
                  $value = $this->data[ $name ];

               if( ( $key = array_search( 'optional', $rules ) ) !== false ) {
                  if( $value === '' )
                     // this field is optional and it has no value, so the 
                     // validation is complete
                     continue;

                  unset( $rules[ $key ] ); // no need to check optional again
               }

               $isFieldValid = true;
            
               foreach( $rules as $rule ) {
                  $parameters = array();

                  if( ( $left = strpos( $rule, '[' ) ) !== false ) {
                     $right = strrpos( $rule, ']' ); 
                     $parameters = trim( substr( $rule, $left + 1, $right - $left - 1 ) );

                     // range rules require two parameters
                     if( strpos( strtolower( $rule ), 'range' ) !== false )
                        $parameters = explode( ',', $parameters );
                     else
                        $parameters = array( $parameters );

                     foreach( $parameters as $key => $parameter )
                        $parameters[ $key ] = trim( $parameter );
                  
                     $rule = substr( $rule, 0, $left );
                  }

                  $parameters[] = $display;
                  $parameters[] = $value;

                  $method = "{$rule}Callback";
                  if( call_user_func_array( array( $this, $method ), $parameters ) === false )
                     $isFieldValid = false;
               }

               $isValid = $isValid && $isFieldValid;
            }
         }

         $this->errorMessage .= $this->afterErrorMessages;

         if( !$isValid && $storeDataInSession === true ) {
            $this->storeFormData( $this->data );
            $this->storeErrorMessage( $this->errorMessage );
         }

         return $isValid;
      }

      public function storeFormData( $data ) {
         global $controller;
         $controller->session->setFlash( 'formData', $data );
      }

      public function storeErrorMessage( $message ) {
         global $controller;
         $controller->session->setFlash( 'errorMessage', $message );
      }

      public function storeSuccessMessage( $message ) {
         global $controller;
         $message = "{$this->beforeSuccessMessage}$message{$this->afterSuccessMessage}";
         $controller->session->setFlash( 'successMessage', $message );
      }

      public function storeInfoMessage( $message ) {
         global $controller;
         $message = "{$this->beforeInfoMessage}$message{$this->afterInfoMessage}";
         $controller->session->setFlash( 'infoMessage', $message );
      }

      public function getErrorMessage() {
         return $this->errorMessage;
      }

      private function requiredCallback( $name, $value ) {
         if( $value === '' ) {
            $this->addErrorMessage( $name, 'must be filled in' );
            return false;
         }

         return true;
      }

      private function minLengthCallback( $length, $name, $value ) {
         if( strlen( $value ) < $length ) {
            $this->addErrorMessage( $name, "must have at least $length characters" );
            return false;
         }

         return true;
      }

      private function maxLengthCallback( $length, $name, $value ) {
         if( strlen( $value ) > $length ) {
            $this->addErrorMessage( $name, "must have at most $length characters" );
            return false;
         }

         return true;
      }

      private function alphaCallback( $name, $value ) {
         if( !ctype_alpha( $value ) ) {
            $this->addErrorMessage( $name, 'must have only alphabetic characters' );
            return false;
         }

         return true;
      }

      private function numericCallback( $name, $value ) {
         if( !is_numeric( $value ) ) {
            $this->addErrorMessage( $name, 'must have only numeric characters' );
            return false;
         }

         return true;
      }

      private function alphanumericCallback( $name, $value ) {
         if( !ctype_alnum( $value ) ) {
            $this->addErrorMessage( $name, 'must have only alphabetic or numeric ' .
               'characters' );
            return false;
         }

         return true;
      }

      private function matchesCallback( $other, $name, $value ) {
         if( !isset( $this->data[ $other ] ) || $this->data[ $other ] != $value ) {
            if( isset( $this->display[ $other ] ) )
               $other = $this->display[ $other ];

            $this->addErrorMessage( $name, "must match the \"$other\" field" ); 
            return false;
         }

         return true;
      } 

      private function emailCallback( $name, $value ) {
         if( !preg_match( '~^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$~i', $value ) ) {
           $this->addErrorMessage( $name, 'must be a valid e-mail' ); 
           return false;
         }

         return true;
      }

      private function formatCallback( $format, $name, $value ) {
         if( !preg_match( $format, $value ) ) {
            $this->addErrorMessage( $name, 'must be in the correct format' );
            return false;
         }

         return true;
      }

      private function rangeCallback( $min, $max, $name, $value ) {
         if( !$this->numericCallback( $name, $value ) )
            return false; // don't show any other error messages

         if( $value < $min || $value > $max ) {
            $this->addErrorMessage( $name, "must be within the range $min to $max" );
            return false;
         }

         return true;
      }

      private function timeCallback( $name, $value ) {
         return $this->timeRangeCallback( '12:00:00 AM', '11:59:59 PM', $name, $value ); 
      }

      private function dateCallback( $name, $value ) {
         // max date using seconds from the epoch
         return $this->dateRangeCallback( '1/1/00', '1/18/38', $name, $value );
      }

      private function timeRangeCallback( $min, $max, $name, $value ) {
         $isFieldValid = true;

         $field = array_search( $name, $this->display );

         $hours = $this->arrayValue( $this->data, "$field-hours", '' );
         $minutes = $this->arrayValue( $this->data, "$field-minutes", '' );
         $seconds = $this->arrayValue( $this->data, "$field-seconds", '' );
         $amPm = $this->arrayValue( $this->data, "$field-am-pm", '' );

         $isFieldValid = $isFieldValid && $this->numericCallback( "$name (hours)", $hours );
         $isFieldValid = $isFieldValid && $this->numericCallback( "$name (minutes)", $minutes );
         $isFieldValid = $isFieldValid && $this->numericCallback( "$name (seconds)", $seconds );

         if( $amPm !== 'am' && $amPm !== 'pm' ) {
            $this->addErrorMessage( $name, 'must be either AM or PM' );
            $isFieldValid = false;
         }

         // if fields aren't in the correct format, return false here and don't 
         // display any further error messages
         if( !$isFieldValid )
            return false;

         $isFieldValid = $isFieldValid && $this->rangeCallback( 1, 12, "$name (hours)", $hours );
         $isFieldValid = $isFieldValid && $this->rangeCallback( 0, 60, "$name (minutes)", $minutes );
         $isFieldValid = $isFieldValid && $this->rangeCallback( 0, 60, "$name (seconds)", $seconds );

         // if the time itself is not valid, return false here and don't 
         // display any further error messages
         if( !$isFieldValid )
            return false;

         $hours = sprintf( "%'02d", (int) $hours );  
         $minutes = sprintf( "%'02d", (int) $minutes );
         $seconds = sprintf( "%'02d", (int) $seconds );

         $time = strtotime( "$hours:$minutes:$seconds $amPm", 0 );
         // ready to be formatted via the date function
         $this->data[ $name ] = $time; 

         if( $time < strtotime( $min, 0 ) || $time > strtotime( $max, 0 ) ) {
            $this->addErrorMessage( $name, "must be within the range $min to $max" );
            return false;
         }

         return true;
      }

      private function dateRangeCallback( $min, $max, $name, $value ) {
         $isFieldValid = true;

         $field = array_search( $name, $this->display );

         $month = $this->arrayValue( $this->data, "$field-month", '' );
         $day = $this->arrayValue( $this->data, "$field-day", '' );
         $year = $this->arrayValue( $this->data, "$field-year", '' );

         $isFieldValid = $isFieldValid && $this->numericCallback( "$name (month)", $month );
         $isFieldValid = $isFieldValid && $this->numericCallback( "$name (day)", $day );
         $isFieldValid = $isFieldValid && $this->numericCallback( "$name (year)", $year );

         // if fields aren't in the correct format, return false here and don't 
         // display any further error messages
         if( !$isFieldValid )
            return false;

         $isFieldValid = $isFieldValid && $this->rangeCallback( 1, 12, "$name (month)", $month );
         $isFieldValid = $isFieldValid && $this->rangeCallback( 1, 31, "$name (day)", $day );
         $isFieldValid = $isFieldValid && $this->rangeCallback( 0, 99, "$name (year)", $year );

         // if the time itself is not valid, return false here and don't 
         // display any further error messages
         if( !$isFieldValid )
            return false;

         $month = sprintf( "%'02d", (int) $month );  
         $day = sprintf( "%'02d", (int) $day );
         $year = sprintf( "%'02d", (int) $year );

         $time = strtotime( "$month/$day/$year" );
         // ready to be formatted via the date function
         $this->data[ $name ] = $time; 

         if( $time < strtotime( $min ) || $time > strtotime( $max ) ) {
            $this->addErrorMessage( $name, "must be within the range $min to $max" );
            return false;
         }

         return true;
      }

      private function addErrorMessage( $name, $message ) {
         $this->errorMessage .= "{$this->errorMessageStartDelim}The \"$name\" field $message." .
            $this->errorMessageEndDelim;
      }

      private function arrayValue( $array, $key, $default ) {
         if( isset( $array[ $key ] ) )
            return $array[ $key ];
         return $default;
      }

   }

