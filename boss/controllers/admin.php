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
         $this->loadModel( 'page' );
         $this->loadHelper( 'markdown' );

         $data = Util::stripAllSlashes( $_POST );
         $data[ 'text' ] = markdown( $data[ 'text' ] );

         echo $data[ 'text' ];
         $this->page->insert( $data );
      }

      // will be called if the user's request is invalid in the context of this 
      // controller
      public function _unknown( $request ) {
         echo "Not found!";
      }

   }


