<?php

   class Util {
      
      private function Util() {

      }

      public static function navigationLink( $to, $text, $desc ) {
         global $config;

         $url = $config[ 'url_root' ] . '/' . $to;
         $request = $config[ 'request' ];

         // assess if $to is the current page
         $link = '<li><a ';
         if( $to == $request )
            $link .= 'class="curPage"';

         // $desc is wrapped in a span tag due to styling
         $link .= "href=\"$url\">$text<br /><span>$desc</span></a></li>";
         return $link;
      }

      public static function addTrailing( $substr, $str ) {
         // if the trailing $substr doesn't exist, add it in
         if( strrpos( $str, $substr ) !== strlen( $str ) - strlen( $substr ) )
            $str .= $substr;
         return $str;
      }

      public static function removeTrailing( $substr, $str ) {
         $strLen = strlen( $str );
         $substrLen = strlen( $substr );

         // remove trailing $substr if it exists
         if( strrpos( $str, $substr ) === $strLen - strlen( $substr ) )
            $str = substr( $str, 0, $strLen - $substrLen );
         return $str;
      }

   }

