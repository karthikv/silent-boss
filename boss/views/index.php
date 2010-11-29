<?php

   // theme directory corresponds to template
   $themeRoot = $config[ 'php_root' ] . '/boss/theme';
   require_once $themeRoot . '/header.php';

   if( isset( $page ) )
      // $this is a Loader object
      $this->view( $page );

   require_once $themeRoot . '/sidebar.php';
   require_once $themeRoot . '/footer.php';

