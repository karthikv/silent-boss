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
      private $table;
      private $select;
      private $join;
      private $where;
      private $orderBy;
      private $limit;

      // helpers
      private $joinTypes;

      public function DB( $host, $username, $password, $db ) {
         $this->mysqli = new mysqli( $host, $username, $password, $db ); 

         $this->joinTypes = array( 'INNER', 'OUTER', 'LEFT', 'RIGHT', 
            'LEFT OUTER', 'RIGHT OUTER' );
         $this->join = array();

         $this->where = array();
         $this->orderBy = array();
      }

      public function __destruct() {
         $this->mysqli->close();
      }

      public function query( $query, $getResults = true ) {
         $this->query = $this->clean( $query ); 
         $this->prepare(); 
         return $this->run( $getResults );
      }

      public function from( $table ) {
         $this->table( $table );
      }

      public function join( $table, $on, $type = 'inner' ) {
         $type = strtoupper( trim( $type ) );
         if( !in_array( $type, $this->joinTypes ) )
            return; 
         
         $table = $this->clean( $table );
         $this->join[ $table ] = array(
            'type' => $type . ' JOIN',
            'on' => $on
         );
      }

      public function get( $table = false ) {
         if( !isset( $this->select ) )
            $this->select( '*' );

         $this->from( $table );
         $this->compile( 'select' );
         return $this->run( true );
      }

      public function select( $select ) {
         $this->select = $this->clean( $select );
      }

      public function table( $table ) {
         if( $table !== false )
            $this->table = $this->clean( $table );
      }

      public function insert( $data, $table = false ) {
         $this->table( $table );
         $this->compile( 'insert', $data ); 
         return $this->run( false );
      }

      public function update( $data, $table = false ) {
         $this->table( $table );
         $this->compile( 'update', $data );
         return $this->run( false );
      }

      public function delete( $table = false ) {
         $this->from( $table );
         $this->compile( 'delete' );
         return $this->run( false );
      }

      public function where( $property, $value, $operator = '=' ) {
         $this->processWhere( 'AND', $property, $operator, $value );
      }

      public function orWhere( $property, $value, $operator = '=' ) {
         $this->processWhere( 'OR', $property, $operator, $value );
      }
      
      private function processWhere( $type, $property, $operator, $value ) {
         $this->where[ $this->clean( $property ) ] = array(
            'type' => $type,
            'operator' => $this->clean( $operator ),
            'value' => $this->clean( $value )
         );
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
            $this->query = "SELECT {$this->select} FROM {$this->table}";
            $this->query .= $this->getJoinClause();

            $this->query .= $where;
            $this->query .= $this->getOrderByClause();
            break;
         case 'insert':
            $this->query = "INSERT INTO {$this->table}";
            $this->query .= $this->getInsertClause( $data );
            break;
         case 'update':
            $this->query = "UPDATE {$this->table}";
            $this->query .= $this->getUpdateClause( $data );
            $this->query .= $where;
            break;
         case 'delete':
            $this->query = "DELETE FROM {$this->table}";
            $this->query .= $where;
            break;
         }

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
                  $paramArray[] = &$this->where[ $property ][ 'value' ];
            } 

            call_user_func_array( array( $this->stmt, 'bind_param' ), $paramArray );
         }

         $this->join = array();
         $this->where = array();
         $this->orderBy = array();

         unset( $this->select );
         unset( $this->table );
         unset( $this->limit );
      }

      private function prepare() {
         if( ( $this->stmt = $this->mysqli->prepare( $this->query ) ) === false )
            trigger_error( "Statement preparation failed: {$this->mysqli->error}" ); 
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

      private function getJoinClause() {
         $join = '';

         if( count( $this->join ) > 0 ) {
            foreach( $this->join as $table => $data )
               $join .= " {$data[ 'type' ]} $table ON {$data[ 'on' ]}";
         }

         return $join;
      }

      private function getWhereClause() {
         $this->whereString = '';
         $where = '';

         if( count( $this->where ) > 0 ) {
            $where = ' WHERE ';
            $count = 0;

            foreach( $this->where as $property => $data ) {
               if( $count !== 0 )
                  $where .= "{$data[ 'type' ]} ";

               $where .= "$property {$data[ 'operator' ]} ? "; 
               $this->whereString .= $this->getParamType( $data[ 'value' ] );

               $count++;
            }

            $where = substr( $where, 0, -1 );
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

