<?php

   class Loader {

      // controller to set values
      private $controller;

      // track what has already been loaded
      private $views;
      private $models;
      private $libraries;
      private $helpers;

      public function Loader( $controller ) {
         $this->controller = $controller;

         $this->views = array();
         $this->models = array();
         $this->libraries = array();
         $this->helpers = array();
      }

      // load a view with the specified data that will be extracted to the 
      // global namespace
      public function view( $view, $data = array() ) {
         $view = strtolower( $view );
         $this->load( 'views', $view, $data );
         $this->views[ $view ] = true;
      }

      // for views that only need to be loaded once
      public function viewOnce( $view, $data = array() ) {
         $view = strtolower( $view );

         if( $this->hasLoadedView( $view ) ) {
            $this->load( 'views', $view, $data );
            $this->views[ $view ] = true;
         }
      }

      public function hasLoadedView( $view ) {
         return isset( $this->views[ $view ] ) && $this->views[ $view ] === true;
      }

      // load a model
      public function model( $model ) {
         $model = strtolower( $model );

         if( !$this->hasLoadedModel( $model ) ) {
            $this->load( 'models', $model );

            $this->controller->$model = new $model();
            $this->models[ $model ] = true;
         }
      }

      public function hasLoadedModel( $model ) {
         return isset( $this->models[ $model ] ) && $this->models[ $model ] === true;
      }

      public function library( $library ) {
         $library = strtolower( $library );

         if( !$this->hasLoadedLibrary( $library ) ) {
            $this->load( 'libraries', $library );

            $this->controller->$library = call_user_func( array( $library, 'getInstance' ) );
            $this->libraries[ $library ] = true;
         }
      }

      public function hasLoadedLibrary( $library ) {
         return isset( $this->libraries[ $library ] ) && $this->libraries[ $library ] === true;
      }

      // load a helper
      public function helper( $helper ) {
         $helper = strtolower( $helper );

         if( !$this->hasLoadedHelper( $helper ) ) {
            $this->load( 'helpers', $helper );
            $this->helpers[ $helper ] = true;
         }
      }

      public function hasLoadedHelper( $helper ) {
         return isset( $this->helpers[ $helper ] ) && $this->helpers[ $helper ] === true;
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

