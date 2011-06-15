<?php

   $config = array();

   /* GENERAL CONFIGURATION */

   // title of the website
   $config[ 'site_title' ] = 'SD Team Manager';

   // name of the actual controller class
   $config[ 'default_controller' ] = 'Authentication';

   // root url
   $config[ 'url_root' ] = 'http://localhost:8888/sd-team-manager';

   // php root url - you generally don't need to change this
   $config[ 'php_root' ] = dirname( __FILE__ );

   /* DATABASE CONFIGURATION */

   // mysql host for the database connection
   $config[ 'mysql_host' ] = 'localhost';

   // username and password
   $config[ 'mysql_username' ] = 'root';
   $config[ 'mysql_password' ] = 'root';

   // name of database that will be used
   $config[ 'mysql_database' ] = 'sd_team_manager';

   /* DO NOT EDIT PAST HERE */

   $config[ 'default_controller' ] = strtolower( $config[ 'default_controller' ] );
   $config[ 'url_root' ] = Util::removeTrailing( '/', $config[ 'url_root' ] );

