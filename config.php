<?php

   $config = array();

   $config[ 'url_root' ] = 'http://localhost:8888/silent-boss';
   $config[ 'php_root' ] = dirname( __FILE__ );

   // do not edit past here
   $config[ 'url_root' ] = Util::removeTrailing( '/', $config[ 'url_root' ] );

