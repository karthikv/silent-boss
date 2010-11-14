<?php
   require_once dirname( __FILE__ ) . '/config.php';
   require_once $config[ 'php_root' ] . '/boss/controller.php';

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

