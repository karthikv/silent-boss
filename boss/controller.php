<?php
   
   class Controller {

      public function Controller() {

      }

      public function index() {
         $data = array(
            "title" => "Home",
            "page" => "home"
         );

         $this->loadView( 'index', $data );
      }

      // will be called if the user's request is invalid in the context of this 
      // controller
      public function _unknown( $request ) {
         echo "Not found!";
      }

      // load a view with the specified data that will be extracted to the 
      // global namespace
      protected function loadView( $view, $data = array() ) {
         $this->load( 'views', $view, $data );
      }

      // load a model
      protected function loadModel( $model ) {
         $this->load( 'models', $model );
      }

      // helper function to load a file in a given directory
      private function load( $directory, $file, $data = array() ) {
         global $config;
         $path = $config[ 'php_root' ] . "/boss/$directory/$file";

         // append .php if necessary
         if( strpos( $file, '.php' ) !== strlen( $file ) - 4 )
            $path .= '.php';

         if( file_exists( $path ) ) {
            // make sure this doesn't override global configuration options 
            extract( $data, EXTR_SKIP ); 
            require_once $path;
         }
      }

   }

