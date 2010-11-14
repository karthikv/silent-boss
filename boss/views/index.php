<?php
   $themeRoot = $config[ 'php_root' ] . '/boss/theme';
   $pageRoot = $config[ 'php_root' ] . '/boss/views';

   require_once $themeRoot . '/header.php';
      
   if( isset( $page ) )
      $this->loadView( $page );

   require_once $themeRoot . '/sidebar.php';
   require_once $themeRoot . '/footer.php';

