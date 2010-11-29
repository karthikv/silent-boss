<?php

   class Controller {
 
      // may be used outside of the class
      public $load;

      // dynamic class properties
      private $data;

      public function Controller() {
         $this->load = new Loader( $this );
      }

      public function __get( $key ) {
         if( isset( $this->data[ $key ] ) )
            return $this->data[ $key ];
         return false;
      }

      public function __set( $key, $value ) {
         $this->data[ $key ] = $value;
      }

   }

