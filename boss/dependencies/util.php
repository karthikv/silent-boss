<?php

   class Util {
      
      private function Util() {

      }

      public static function isLoggedIn() {
         global $controller;
         return $controller->session->hasFlash( 'id' );
      }

      public static function getConfig( $key ) {
         global $config;

         // if the key exists in the array, return the value
         if( isset( $config[ $key ] ) )
            return $config[ $key ];
         return false;
      }

      // set isRelativeToRoot to true if the redirect is relative to the 
      // root of the silent boss installation
      public static function redirect( $location, $isRelativeToRoot ) {
         global $config;

         if( $isRelativeToRoot === true ) {
            $location = Util::removeBeginning( '/', $location );
            $location = $config[ 'url_root' ] . '/' . $location; 
         }

         header( "Location: $location\n" );
         exit( 0 ); // don't do anything after redirecting
      }

      public static function simplifyRequest( $request ) {
         global $config, $routes;
         $request = strtolower( $request );

         // find the controller
         $controller = $request;
         $pos = strpos( $request, '/' );

         if( $pos !== false )
            $controller = substr( $request, 0, $pos );
         else
            // used for stripping the controller from the URL if necessary
            $pos = strlen( $request );

         // strip the controller if it's the same as the default one
         if( $controller == $config[ 'default_controller' ] )
            $request = substr( $request, $pos + 1 );
         
         // remove trailing strings that are unnecessary characters
         $trailings = array( '/', 'index', '/' );
         foreach( $trailings as $trailing )
            $request = Util::removeTrailing( $trailing, $request );

         return $request;
      }

      public static function applyRoutes( $request ) {
         global $routes;

         foreach( $routes as $regex => $replacement )
            $request = preg_replace( '~' . $regex . '~', $replacement, $request );
         return $request;
      }

      public static function navigationLink( $to, $text, $desc ) {
         global $config;

         // make the simplest to request possible
         $to = Util::simplifyRequest( $to );

         $url = $config[ 'url_root' ] . '/' . $to;
         $request = $config[ 'request' ];

         // $request has the routes applied to it, so $to needs them for 
         // comparison
         $to = Util::applyRoutes( $to );

         // assess if $to is the current page
         $link = '<li><a ';
         if( $to == $request )
            $link .= 'class="curPage"';

         // $desc is wrapped in a span tag due to styling
         $link .= "href=\"$url\">$text<br /><span>$desc</span></a></li>";
         return $link;
      }

      public static function hasTrailing( $substr, $str ) {
         return strrpos( $str, $substr ) === strlen( $str ) - strlen( $substr );
      }

      public static function hasBeginning( $substr, $str ) {
         return strpos( $str, $substr ) === 0;
      }

      public static function addTrailing( $substr, $str ) {
         // if the trailing $substr doesn't exist, add it in
         if( !Util::hasTrailing( $substr, $str ) )
            $str .= $substr;
         return $str;
      }

      public static function removeTrailing( $substr, $str ) {
         // remove trailing $substr if it exists
         if( Util::hasTrailing( $substr, $str ) )
            $str = substr( $str, 0, strlen( $str ) - strlen( $substr ) );
         return $str;
      }

      public static function addBeginning( $substr, $str ) {
         if( !Util::hasBeginning( $substr, $str ) )
            $str = $substr . $str;
         return $str;
      }

      public static function removeBeginning( $substr, $str ) {
         if( Util::hasBeginning( $substr, $str ) )
            $str = substr( $str, strlen( $substr ) );
         return $str;
      }

      public static function stripAllSlashes( $data ) {
         if( get_magic_quotes_gpc() ) {
            foreach( $data as $key => $value ) {
               if( is_string( $value ) )
                  $data[ $key ] = stripslashes( $data[ $key ] ); 
            }
         }

         return $data;
      }

   }

