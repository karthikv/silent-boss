<?php

   // theme directory corresponds to template
   $themeRoot = $config[ 'php_root' ] . '/boss/theme';
   require_once $themeRoot . '/header.php';

   if( isset( $page ) )
      // $this is a Loader object
      // extract global variables to allow easy accessibility
      $this->view( $page, $GLOBALS );

   require_once $themeRoot . '/sidebar.php';
   require_once $themeRoot . '/footer.php';

