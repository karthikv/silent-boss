<?php
   
   $routes = array();

   // define routes here; the key is the request and the value is the route
   // routes are applied in the order they are given
   $routes[ '^admin/(.*?)-(.*)$' ] = 'admin/$1_$2';

