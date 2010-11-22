<?php
   
   class Admin extends Controller {

      public function Admin() {
         parent::Controller();
      }

      public function index() {
         $data = array(
            "title" => "Home",
            "page" => "admin/index"
         );

         $this->loadView( 'index', $data );
      }

      public function add_page() {
         $data = array(
            "title" => "Add Post",
            "page" => "admin/add-page"
         );

         $this->loadView( 'index', $data );
      }

      public function add_page_handler() {
         $this->loadHelper( 'markdown' );
         $data = $_POST;

         foreach( $_POST as $key => $value )
            $_POST[ $key ] = stripslashes( $value );

         echo markdown( $_POST[ 'text' ] );
      }

      // will be called if the user's request is invalid in the context of this 
      // controller
      public function _unknown( $request ) {
         echo "Not found!";
      }

   }


