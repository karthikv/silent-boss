<?php

   // used for relative includes
   $dir = dirname( __FILE__ );

   $coreDir = $dir . '/boss/core';
   $handle = opendir( $coreDir );

   // core include files
   while( ( $file = readdir( $handle ) ) !== false ) {
      if( strrpos( $file, '.php' ) === strlen( $file ) - 4 )
         require_once $coreDir . '/' . $file;
   } 

   // basic config and controller that are used for all requests
   require_once $dir . '/config.php';
   require_once $dir . '/boss/controller.php';
   
   // conserve the namespace
   unset( $dir, $coreDir, $handle, $file );

   $controller = new Controller();
   $request = isset( $_GET[ 'request' ] ) ? $_GET[ 'request' ] : 'index';

   if( strpos( $request, '_' ) !== 0 && is_callable( array( $controller, $request ) ) ) {
      // store request in case it needs to be accessed
      $config[ 'request' ] = $request;
      $controller->{$request}();
   }
   else
      // underscores represent special requests not directly accessible 
      $controller->_unknown( $request );

