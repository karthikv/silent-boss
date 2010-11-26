<?php

   class Model {

      protected $db;
         
      public function Model() {
         global $config;
         $this->db = new DB( $config[ 'mysql_host' ], $config[ 'mysql_username' ], $config[ 'mysql_password' ], $config[ 'mysql_database' ] ); 
      }

   }

