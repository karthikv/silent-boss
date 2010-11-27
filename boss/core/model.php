<?php

   class Model {

      // database object this model uses
      protected $db;
         
      public function Model() {
         global $config;

         // instantiate the db object
         $this->db = new DB( $config[ 'mysql_host' ], $config[ 'mysql_username' ], 
            $config[ 'mysql_password' ], $config[ 'mysql_database' ] ); 
      }

   }

