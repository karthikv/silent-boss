<?php

   // used for relative includes
   $dir = dirname( __FILE__ );
   $includeDirs = array( 'boss/dependencies', 'boss/core', 'boss/controllers' );

   // include all files from each include directory
   foreach( $includeDirs as $includeDir ) {
      $curDir = $dir . '/' . $includeDir;
      $handle = opendir( $curDir );

      // core include files
      while( ( $file = readdir( $handle ) ) !== false ) {
         if( strrpos( $file, '.php' ) === strlen( $file ) - 4 )
            require_once $curDir . '/' . $file;
      } 
   }

   // basic config and routes that are used for all requests
   require_once $dir . '/config.php';
   require_once $dir . '/routes.php';
   
   // conserve the namespace
   unset( $dir, $coreDir, $handle, $file );

   if( isset( $_GET[ 'request' ] ) ) {
      $request = $_GET[ 'request' ];
      $config[ 'request' ] = $request;

      // simplify the request if possible
      $simpleReq = Util::simplifyRequest( $request );
      if( $simpleReq !== $request )
         Util::redirect( $simpleReq, true );
   }
   else {
      $request = 'index';
      $config[ 'request' ] = ''; // home page in simplest form
   }

   foreach( $routes as $regex => $replacement )
      $request = preg_replace( '~' . $regex . '~', $replacement, $request );

   // find the correct controller and method (request) to use
   $controllerName = $config[ 'default_controller' ];
   if( ( $pos = strpos( $request, '/' ) ) !== false ) {
      $controllerName = substr( $request, 0, $pos );
      $request = substr( $request, $pos + 1 );
   }
   else if( class_exists( $request ) ) {
      $controllerName = $request; 
      $request = 'index'; // default to the index method
   }

   // construct the controller if it's a valid class
   $controller = NULL;
   if( class_exists( $controllerName ) )
      $controller = new $controllerName(); 
   
   // store controller and method in case it needs to be accessed
   $config[ 'controller' ] = $controllerName;
   $config[ 'method' ] = $request;

   unset( $simpleReq, $controllerName, $pos );

   if( strpos( $request, '_' ) !== 0 && is_callable( array( $controller, $request ) ) )
      // initiate the request
      $controller->{$request}();
   else {
      // construct the controller if it has not already been created
      if( $controller == NULL )
         $controller = new $config[ 'default_controller' ];

      // underscores represent special requests not directly accessible 
      $controller->_unknown( $request );
   }

