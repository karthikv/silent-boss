<?php

   class Form {

      // form fields
      private $fields;
      private $validFields;

      // delimiters
      private $fieldStartDelim;
      private $fieldEndDelim;
      private $descStartDelim;
      private $descEndDelim;

      public function Form( $fields = array() ) {
         global $controller;
         $controller->load->helper( 'formkey' );

         $this->fields = $fields;
         $this->validFields = array( 'text', 'password', 'hidden', 'select', 
            'radio', 'radio-group', 'checkbox', 'checkbox-group', 'textarea', 
            'date', 'time' );

         $this->setFieldStartDelim( '<p>' );
         $this->setFieldEndDelim( '</p>' );

         $this->setDescStartDelim( '<small>' );
         $this->setDescEndDelim( '</small>' );
      }

      public function setFieldStartDelim( $delim ) {
         $this->fieldStartDelim = $delim;
      }

      public function setFieldEndDelim( $delim ) {
         $this->fieldEndDelim = $delim;
      }

      public function setDescStartDelim( $delim ) {
         $this->descStartDelim = $delim;
      }

      public function setDescEndDelim( $delim ) {
         $this->descEndDelim = $delim;
      }

      public function addField( $name, $options ) {
         $this->fields[ $name ] = $options;
      }

      public function addFieldBefore( $before, $name, $options ) {
         $this->addFieldRelative( true, $before, $name, $options );
      }

      public function addFieldAfter( $after, $name, $options ) {
         $this->addFieldRelative( false, $after, $name, $options );
      }

      private function addFieldRelative( $isBefore, $which, $name, $options ) {
         $newFields = array();

         foreach( $this->fields as $fieldName => $value ) {
            // add before
            if( $isBefore && $fieldName === $which )
               $newFields[ $name ] = $options;

            $newFields[ $fieldName ] = $value;

            // add after
            if( !$isBefore && $fieldName === $which )
               $newFields[ $name ] = $options;
         }

         $this->fields = $newFields;
      }

      public function removeField( $name ) {
         // unset won't do anything if $name isn't a valid key
         unset( $this->fields[ $name ] );
      }

      public function display( $formConfig, $submitOptions, $includeKey = true ) {
         global $controller;

         if( $controller->session->hasFlash( 'errorMessage' ) )
            echo $controller->session->getFlash( 'errorMessage' );

         if( $controller->session->hasFlash( 'successMessage' ) )
            echo $controller->session->getFlash( 'successMessage' );

         if( $controller->session->hasFlash( 'infoMessage' ) )
            echo $controller->session->getFlash( 'infoMessage' );

         $this->fillWithInputtedData();
         echo "<form {$this->attrString( $formConfig )}>";

         foreach( $this->fields as $name => $options ) {
            if( $options[ 'type' ] !== 'hidden' )
               echo $this->fieldStartDelim;

            if( in_array( $options[ 'type' ], $this->validFields ) ) {
               // for radio group and checkbox group
               $method = str_replace( '-g', 'G', $options[ 'type' ] );
               $this->{$method}( $name, $options );
            }

            if( $options[ 'type' ] !== 'hidden' )
               echo $this->fieldEndDelim;
         }

         $this->defaultArrayValue( $submitOptions, 'class', '' );
         $submitOptions[ 'class' ] .= ' submit-field';

         echo $this->input( 'submit', '', $submitOptions );

         if( $includeKey !== false )
            echo $this->formKey(); 

         echo '</form>';
      }

      public function text( $name, $options ) {
         echo $this->input( 'text', $name, $options );
      }

      public function password( $name, $options ) {
         echo $this->input( 'password', $name, $options );
      }

      public function hidden( $name, $options ) {
         echo $this->input( 'hidden', $name, $options );
      }

      // the second parameter is not named options because it will have an 
      // extracted key of the same name
      public function select( $name, $config ) {
         extract( $config, EXTR_SKIP );

         if( !isset( $attrs ) )
            $attrs = array();

         $attrs[ 'name' ] = $name;

         $this->defaultArrayValue( $attrs, 'class', '' );
         $attrs[ 'class' ] .= ' select-field';
         
         $field = '';
         if( isset( $label ) )
            $field = $this->label( $name, $label );

         if( !isset( $selected ) )
            $selected = false;

         $field .= "<select {$this->attrString( $attrs )}>";
         foreach( $options as $value => $display ) {
            $field .= "<option value=\"$value\"";

            if( $value === $selected )
               $field .= " selected=\"1\"";   

            $field .= ">$display</option>";
         }
         $field .= '</select>';

         if( isset( $desc ) )
            $field .= $this->description( $desc );

         echo $field;
      }

      public function radio( $name, $options ) {
         $this->addCheckedAttr( $options );
         echo $this->input( 'radio', $name, $options );
      }

      public function radioGroup( $name, $options ) {
         $this->group( 'radio', 'radios', $name, $options );
      }

      public function checkbox( $name, $options ) {
         $this->addCheckedAttr( $options );
         echo $this->input( 'checkbox', $name, $options );
      }

      public function checkboxGroup( $name, $options ) {
         $this->group( 'checkbox', 'checkboxes', $name, $options );
      }

      public function textarea( $name, $options ) {
         extract( $options, EXTR_SKIP );

         $this->defaultArrayValue( $attrs, 'rows', '' );
         $this->defaultArrayValue( $attrs, 'cols', '' );

         if( !isset( $attrs ) )
            $attrs = array();

         $attrs[ 'name' ] = $name;

         if( !isset( $value ) ) {
            if( isset( $attrs[ 'value' ] ) ) {
               $value = $attrs[ 'value' ];
               unset( $attrs[ 'value' ] );
            }
            else
               $value = '';
         }

         $this->defaultArrayValue( $attrs, 'class', '' );
         $attrs[ 'class' ] .= ' textarea-field';

         $field = '';
         if( isset( $label ) )
            $field = $this->label( $name, $label );

         echo $field . "<textarea {$this->attrString( $attrs )}>$value</textarea>";
      }

      public function date( $name, $options ) {
         $this->addNumberClass( $options );

         // description should only be outputted after all date fields
         if( isset( $options[ 'desc' ] ) ) {
            $desc = $options[ 'desc' ];
            unset( $options[ 'desc' ] );
         }

         if( isset( $options[ 'value' ] ) ) { 
            if( preg_match( 
               '~^\s*(\d{1,2})\s*(/|-)\s*(\d{1,2})\s*(/|-)\s*(\d{1,2}|\d{4})\s*$~', 
               $options[ 'value' ], $matches ) )
               $options[ 'value' ] = sprintf( "%'02d", (int) $matches[1] );
            else
               unset( $options[ 'value' ] );
         }

         echo $this->input( 'text', $name . '-month', $options ) . ' / ';
         unset( $options[ 'label' ] );

         if( !empty( $matches ) )
            $options[ 'value' ] =  sprintf( "%'02d", (int) $matches[3] );

         echo $this->input( 'text', $name . '-day', $options ) . ' / ';

         if( !empty( $matches ) ) {
            if( strlen( $matches[5] ) === 4 )
               $matches[5] = substr( $matches[5], 2 );
            $options[ 'value' ] =  sprintf( "%'02d", (int) $matches[5] );
         }

         if( isset( $desc ) )
            $options[ 'desc' ] = $desc;

         echo $this->input( 'text', $name . '-year', $options );
      }

      public function time( $name, $options ) {
         $this->addNumberClass( $options );

         // description should only be outputted after all time fields
         if( isset( $options[ 'desc' ] ) ) {
            $desc = $options[ 'desc' ];
            unset( $options[ 'desc' ] );
         }

         if( isset( $options[ 'value' ] ) ) { 
            if( preg_match( 
               '~^\s*(\d{1,2})\s*:\s*(\d{1,2})\s*:\s*(\d{1,2})\s*(am|pm)?\s*$~i', 
               $options[ 'value' ], $matches ) )
               $options[ 'value' ] = sprintf( "%'02d", (int) $matches[1] );
            else
               unset( $options[ 'value' ] );
         }

         echo $this->input( 'text', $name . '-hours', $options ) . ' : ';
         unset( $options[ 'label' ] );

         if( !empty( $matches ) )
            $options[ 'value' ] =  sprintf( "%'02d", (int) $matches[2] );

         echo $this->input( 'text', $name . '-minutes', $options ) . ' : ';

         if( !empty( $matches ) )
            $options[ 'value' ] =  sprintf( "%'02d", (int) $matches[3] );

         echo $this->input( 'text', $name . '-seconds', $options ) . ' '; 

         if( !empty( $matches ) && isset( $matches[4] ) )
            $options[ 'selected' ] = strtolower( $matches[4] );

         $options[ 'options' ] = array( 'am' => 'AM', 'pm' => 'PM' );
         $options[ 'attrs' ][ 'class' ] = str_replace( 'number-field', 'ampm', 
            $options[ 'attrs' ][ 'class' ] );

         if( isset( $desc ) )
            $options[ 'desc' ] = $desc;

         echo $this->select( $name . '-ampm', $options );
      }

      public function formKey() {
         $formKey = new FormKey();
         echo $formKey->outputKey();
      }

      private function fillWithInputtedData() {
         global $controller;
         $formData = $controller->session->getFlash( 'formData' );

         if( is_array( $formData ) ) {
            $processing = array();
      
            foreach( $formData as $name => $value ) {
               // make this work with dates and times 
               if( !isset( $this->fields[ $name ] ) ) {
                  $dash = strrpos( $name, '-' );

                  if( $dash !== false ) {
                     $category = substr( $name, $dash + 1 );
                     $name = substr( $name, 0, $dash );
                  }

                  // still not a valid field
                  if( !isset( $this->fields[ $name ] ) )
                     continue;
               }

               $options = &$this->fields[ $name ];
               $type = $options[ 'type' ];

               if( $type === 'radio' || $type === 'radio-group' || 
                     $type === 'checkbox' || $type === 'checkbox-group' )
                  $options[ 'checked' ] = $value;
               else if( $type === 'select' )
                  $options[ 'selected' ] = $value;
               else if( $type === 'date' || $type === 'time' ) {
                  if( !isset( $processing[ $name ] ) )
                     $processing[ $name ] = array();
                  $processing[ $name ][ $category ] = $value; 
               }
               else
                  $options[ 'value' ] = $value;
            }

            foreach( $processing as $name => $data ) {
               if( !isset( $this->fields[ $name ] ) )
                  continue;

               $options = &$this->fields[ $name ];
               $type = $options[ 'type' ];

               if( $type === 'date' )
                  $options[ 'value' ] = "{$data[ 'month' ]}/{$data[ 'day' ]}/{$data[ 'year' ]}";
               if( $type === 'time' )
                  $options[ 'value' ] = "{$data[ 'hours' ]}:{$data[ 'minutes' ]}:{$data[ 'seconds' ]} {$data[ 'ampm' ]}";
            }
         }
      }

      private function label( $name, $label ) {
         return "<label for=\"$name\">$label</label>";
      }

      private function input( $type, $name, $options ) {
         extract( $options, EXTR_SKIP );

         if( !isset( $attrs ) )
            $attrs = array();

         $attrs[ 'type' ] = $type;
         $attrs[ 'name' ] = $name;

         if( isset( $value ) )
            $attrs[ 'value' ] = $value;

         $this->defaultArrayValue( $attrs, 'class', '' );
         $attrs[ 'class' ] .= " $type-field";

         $field = '';
         if( isset( $label ) )
            $field = $this->label( $name, $label );

         $field .= "<input {$this->attrString( $attrs )}></input>";

         if( isset( $desc ) )
            $field .= $this->description( $desc );

         return $field;
      }

      private function description( $desc ) {
         return"{$this->descStartDelim}$desc{$this->descEndDelim}";
      }

      private function attrString( $attrs ) {
         $attrString = '';

         foreach( $attrs as $attr => $value ) {
            $attr = trim( $attr );
            $value = trim( $value );

            $attrString .= " $attr=\"$value\"";
         }
         
         return substr( $attrString, 1 );
      }

      private function addNumberClass( &$options ) {
         $this->defaultArrayValue( $options, 'attrs', array() );
         $this->defaultArrayValue( $options[ 'attrs' ], 'class', '' );
         $options[ 'attrs' ][ 'class' ] .= ' number-field';
      }

      private function addCheckedAttr( &$options ) {
         if( isset( $options[ 'checked' ] ) ) {
            $this->defaultArrayValue( $options, 'attrs', array() );
            $options[ 'attrs' ][ 'checked' ] = '1';
         }
      }

      private function group( $group, $index, $name, $options ) {
         $checked = false;
         if( isset( $options[ 'checked' ] ) )
            $checked = $options[ 'checked' ];

         foreach( $options[ $index ] as $value => $display ) {
            $options[ 'value' ] = $value;
            
            if( $value === $checked )
               $options[ 'checked' ] = 1;
            else
               unset( $options[ 'checked' ] );

            $this->{$group}( $name, $options );
            echo " $display ";

            unset( $options[ 'label' ] );
         }
      }

      private function defaultArrayValue( &$array, $index, $value ) {
         if( !isset( $array[ $index ] ) )
            $array[ $index ] = $value;
      }

   }

