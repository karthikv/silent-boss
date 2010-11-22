<?php

   class DB {

      // mysqli object
      private $mysqli;

      // stmt object
      private $stmt;
      private $paramString;
      private $whereString;

      // data
      private $query;
      private $where;
      private $orderBy;
      private $limit;

      public function DB( $host, $username, $password, $db ) {
         $this->mysqli = new mysqli( $host, $username, $password, $db ); 

         $this->where = array();
         $this->orderBy = array();
      }

      public function query( $query, $getResults = true ) {
         $this->query = $this->clean( $query ); 
         $this->prepare(); 
         return $this->run( $getResults );
      }

      public function get( $table ) {
         $this->query = "SELECT * FROM {$this->clean( $table )}";
         $this->compile( 'select' );
         return $this->run( true );
      }

      public function insert( $table, $data ) {
         $this->query = "INSERT INTO {$this->clean( $table )}";
         $this->compile( 'insert', $data ); 
         return $this->run( false );
      }

      public function update( $table, $data ) {
         $this->query = "UPDATE {$this->clean( $table )}";
         $this->compile( 'update', $data );
         return $this->run( false );
      }

      public function delete( $table ) {
         $this->query = "DELETE FROM {$this->clean( $table )}";
         $this->compile( 'delete' );
         return $this->run( false );
      }

      public function where( $property, $value ) {
         $this->where[ $this->clean( $property ) ] = $this->clean( $value );
      }

      public function orderBy( $field, $asc = true ) {
         $value = 'ASC';
         if( $asc == false )
            $value = 'DESC';

         $this->orderBy[ $this->clean( $field ) ] = $value;
      }

      public function limit( $num ) {
         $this->limit = (int) $num;
      }

      private function run( $getResults ) {
         $this->stmt->execute();

         if( $getResults == false )
            return $this->stmt->affected_rows > 0;
         else {
            $meta = $this->stmt->result_metadata();
            $fields = $meta->fetch_fields();

            $paramArray = array();
            $fetchedData = array();

            foreach( $fields as $field )
               // can't use &$field because it's just a copy of the value in 
               // $fields
               $paramArray[] = &$fetchedData[ $field->name ];

            call_user_func_array( array( $this->stmt, 'bind_result' ), $paramArray );
            $results = array(); 

            while( $this->stmt->fetch() ) {
               $copy = array();
               foreach( $fetchedData as $key => $value )
                  $copy[ $key ] = $value;

               $results[] = $copy;
            }

            return $results;
         }
      }

      private function compile( $type, $data = array() ) {
         $this->paramString = '';
         $where = $this->getWhereClause();

         switch( $type ) {
         case 'select':
         case 'delete':
            $this->query .= $where;
            break;
         case 'insert':
            $this->query .= $this->getInsertClause( $data );
            break;
         case 'update':
            $this->query .= $this->getUpdateClause( $data );
            $this->query .= $where;
            break;
         }

         if( $type === 'select' )
            $this->query .= $this->getOrderByClause();

         if( $type === 'select' || $type === 'update' || $type === 'delete' )
            $this->query .= $this->getLimitClause(); 

         $this->prepare();
         $processWhere = false;

         if( $this->whereString !== '' && ( $type === 'select' || $type === 'update' 
               || $type === 'delete' ) ) {
            $this->paramString .= $this->whereString;
            $processWhere = true;
         }

         if( count( $data ) > 0 || $processWhere ) {
            $paramArray = array( $this->paramString );

            foreach( $data as $property => $value )
               // can't use &$value because it's just a copy of the value in 
               // $data
               $paramArray[] = &$data[ $property ];

            if( $processWhere === true ) {
               foreach( $this->where as $property => $value )
                  // can't use &$value because it's just a copy of the value in 
                  // $this->where
                  $paramArray[] = &$this->where[ $property ];
            } 

            call_user_func_array( array( $this->stmt, 'bind_param' ), $paramArray );
         }

         $this->where = array();
         $this->orderBy = array();
         unset( $this->limit );
      }

      private function prepare() {
         $this->stmt = $this->mysqli->prepare( $this->query ); 
      }

      private function getInsertClause( $data ) {
         $insert = '';

         if( count( $data ) > 0 ) {
            $keys = array_keys( $data );
            foreach( $keys as $key => $value )
               $keys[ $key ] = $this->clean( $value );

            $insert .= ' ( ' . implode( ', ', $keys ) . ' ) VALUES ( ';
            foreach( $data as $key => $value ) {
               $insert .= '?, ';
               $this->paramString .= $this->getParamType( $value );
            }
            
            $insert = substr( $insert, 0, strlen( $insert ) - 2 );
            $insert .= ' )';
         }

         return $insert;
      }

      private function getUpdateClause( $data ) {
         $update = '';

         if( count( $data ) > 0 ) {
            $update = ' SET ';

            foreach( $data as $property => $value ) {
               $property = $this->clean( $property ); 
               $update .= "$property = ?, ";
               $this->paramString .= $this->getParamType( $value );
            }

            $update = substr( $update, 0, strlen( $update ) - 2 );
         }
                    
         return $update;
      }

      private function getWhereClause() {
         $this->whereString = '';
         $where = '';

         if( count( $this->where ) > 0 ) {
            $where = ' WHERE ';

            foreach( $this->where as $property => $value ) {
               $where .= "$property = ? AND "; 
               $this->whereString .= $this->getParamType( $value );
            }

            $where = substr( $where, 0, strlen( $where ) - 5 );
         }

         return $where;
      }

      private function getOrderByClause() {
         $orderBy = '';

         if( count( $this->orderBy ) > 0 ) {
            $orderBy = ' ORDER BY ';
            foreach( $this->orderBy as $field => $type )
               $orderBy .= "$field $type, ";

            $orderBy = substr( $orderBy, 0, strlen( $orderBy ) - 2 );
         }

         return $orderBy;
      }

      private function getLimitClause() {
         $limit = '';

         if( isset( $this->limit ) )
            $limit = " LIMIT {$this->limit}";

         return $limit;
      }

      private function getParamType( $param ) {
         $type = gettype( $param );
         switch( $type ) {
         case 'string':
            return 's';
         case 'integer':
            return 'i';
         case 'double':
            return 'd';
         }
      }

      private function clean( $data ) {
         if( is_string( $data ) )
            $data = $this->mysqli->real_escape_string( $data );
         return $data;
      }

   }

