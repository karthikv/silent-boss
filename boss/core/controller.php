<?php

   class Controller {
      
      public function Controller() {

      }

      // load a view with the specified data that will be extracted to the 
      // global namespace
      protected function loadView( $view, $data = array() ) {
         $this->load( 'views', $view, $data );
      }

      // load a model
      protected function loadModel( $model ) {
         $this->load( 'models', $model );
         $this->$model = new $model();
      }

      // load a helper
      protected function loadHelper( $helper ) {
         $this->load( 'helpers', $helper );
      }

      // helper function to load a file in a given directory
      private function load( $directory, $file, $data = array() ) {
         global $config;
         $path = $config[ 'php_root' ] . "/boss/$directory/$file";

         // append .php if necessary
         $path = Util::addTrailing( '.php', $path );

         if( file_exists( $path ) ) {
            // make sure this doesn't override global configuration options 
            extract( $data, EXTR_SKIP ); 
            require_once $path;
         }
      }

   }

