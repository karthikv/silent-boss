<?php
   
   class Controller extends ControllerBase {

      public function Controller() {
         parent::ControllerBase();
      }

      public function index() {
         $data = array(
            "title" => "Home",
            "page" => "home"
         );

         $this->loadView( 'index', $data );
      }

      // will be called if the user's request is invalid in the context of this 
      // controller
      public function _unknown( $request ) {
         echo "Not found!";
      }

   }

