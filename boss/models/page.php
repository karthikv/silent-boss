<?php

   class Page extends Model {

      private $table;
      
      public function Page() {
         parent::Model(); 
         $this->table = 'pages';
      }

      public function get( $property, $value ) {
         $this->db->where( $property, $value );
         return $this->db->get( $this->table );
      }

      public function insert( $data ) {
         return $this->db->insert( $this->table, $data );
      }

      public function update( $data, $property, $value ) {
         $this->db->where( $property, $value );
         return $this->db->update( $this->table, $data ); 
      }

      public function delete( $property, $value ) {
         $this->db->where( $property, $value );
         return $this->db->delete( $this->table );
      }

   }

