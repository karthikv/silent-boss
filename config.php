<?php

   $config = array();

   $config[ 'url_root' ] = 'http://localhost:8888/silent-boss';
   $config[ 'php_root' ] = dirname( __FILE__ );

   // do not edit past here
   if( strrpos( $config[ 'url_root' ], '/' ) === strlen( $config[ 'url_root' ] ) - 1 )
      $config[ 'url_root' ] = substr( $config[ 'url_root' ], 0, strlen( $config[ 'url_root' ] ) - 1 ); 

