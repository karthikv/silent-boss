<?php

   class Session {

      // singleton
      private static $instance;

      // flash data
      private $flash;

      private function Session() {
         $this->start();

         $this->flash = array();
         foreach( $_SESSION as $key => $value ) {
            // if the key begins with flash, it is flash data
            if( strpos( $key, 'flash-' ) === 0 ) {
               $this->flash[ substr( $key, 6 ) ] = $value;
               $this->remove( $key );
            }
         }
      }

      public static function getInstance() {
         if( !isset( self::$instance ) )
            self::$instance = new Session();
        return self::$instance; 
      }

      public function set( $key, $value ) {
         $_SESSION[ $key ] = $value;
      }

      public function setFlash( $key, $value ) {
         $_SESSION[ "flash-$key" ] = $value;

         // if the user wants to access the data during this request
         $this->flash[ $key ] = $value;
      }

      public function remove( $key ) {
         unset( $_SESSION[ $key ] );
      }

      public function removeFlash( $key ) {
         unset( $this->flash[ $key ] );
      }

      public function get( $key ) {
         if( $this->has( $key ) )
            return $_SESSION[ $key ];
         return false;
      }

      public function getFlash( $key ) {
         if( $this->hasFlash( $key ) )
            return $this->flash[ $key ];
         return false;
      }

      public function has( $key ) {
         return isset( $_SESSION[ $key ] );
      }

      public function hasFlash( $key ) {
         return isset( $this->flash[ $key ] );
      }

      public function regenerate() {
         if( isset( $_SESSION[ 'isObsolete' ] ) && $_SESSION[ 'isObsolete' ] === true )
            return;

         $_SESSION[ 'isObsolete' ] = true;
         $_SESSION[ 'expireTime' ] = time() + 10; // 10 seconds from now

         // keep old session intact
         session_regenerate_id( false );
         $id = session_id();

         // close both sessions
         session_write_close();

         session_id( $id );
         session_start();

         unset( $_SESSION[ 'isObselete' ] );
         unset( $_SESSION[ 'expireTime' ] );
      }

      public function destroy() {
         $_SESSION = array();
         session_destroy();
      }

      private function start() {
         $domain = $_SERVER[ 'SERVER_NAME' ];
         $secure = isset( $_SERVER[ 'HTTPS' ] );

         session_set_cookie_params( 0, '/', $domain, $secure, true );
         session_start();

         if( !$this->isInvalid() ) {
            if( $this->shouldReset() ) {
               $_SESSION = array();
               $_SESSION[ 'userAgent' ] = $_SERVER[ 'HTTP_USER_AGENT' ];
               $_SESSION[ 'ip' ] = $_SERVER[ 'REMOTE_ADDR' ]; 
            }
            else if( rand( 1, 20 ) === 1 )
               $this->regenerate();
         }
         else {
            $this->destroy();
            session_start(); // restart
         }
      }

      private function shouldReset() {
         return !isset( $_SESSION[ 'userAgent' ] ) || !isset( $_SESSION[ 'ip' ] )
            || $_SESSION[ 'userAgent' ] !== $_SERVER[ 'HTTP_USER_AGENT' ]
            || $_SESSION[ 'ip' ] !== $_SERVER[ 'REMOTE_ADDR' ];
      }

      private function isInvalid() {
         return isset( $_SESSION[ 'isObsolete' ] ) && $_SESSION[ 'isObsolete' ] === true
            && ( !isset( $_SESSION[ 'expireTime' ] ) || $_SESSION[ 'expireTime' ] <= time() );
      }

   }

