<?php
   
   class Pages extends Controller {

      public function Pages() {
         parent::Controller();
      }

      public function index() {
         $data = array(
            "title" => "Home",
            "page" => "pages/index"
         );

         $this->loadView( 'index', $data );
      }

      // will be called if the user's request is invalid in the context of this 
      // controller
      public function _unknown( $request ) {
         echo "Not found!";
      }

   }

