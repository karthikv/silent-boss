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

      public function add_post() {
         $data = array(
            "title" => "Add Post",
            "page" => "admin/index"
         );

         $this->loadView( 'index', $data );
      }

      // will be called if the user's request is invalid in the context of this 
      // controller
      public function _unknown( $request ) {
         echo "Not found!";
      }

   }


