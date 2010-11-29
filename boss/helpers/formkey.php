<?php

   class FormKey
   {
      private $formKey;
      private $storedFormKey;

      public function FormKey()
      {	
         global $controller;

   	   // store generated form key
   		$this->storedFormKey = $controller->session->get( 'formKey' );
      }

      public function outputKey()
      {
         global $controller;

   	   $this->formKey = $this->generateKey();
         $controller->session->set( 'formKey', $this->formKey );

   	   return '<input type="hidden" name="formKey" id="formKey" value="' . $this->formKey . '"></input>';
      }

      public function isValid()
      {
   	   // check the given key and stored key
   	   return isset( $_REQUEST[ 'formKey' ] ) && $_REQUEST[ 'formKey' ] === $this->storedFormKey;
      }
   
      private function generateKey()
      {
   	   $ip = $_SERVER[ 'REMOTE_ADDR' ];

   	   // long, random string
   	   $uniqID = uniqid( mt_rand(), true );
   	   return md5( $ip . $uniqID );
      }
   }

