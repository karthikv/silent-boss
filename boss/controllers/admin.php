<?php
   
   class Admin extends Controller {

      public function Admin() {
         parent::Controller();

         $this->load->library( 'session' );
         $this->load->helper( 'form' );
         $this->load->helper( 'validator' );
      }

      public function index() {
         $data = array(
            'title' => 'Home',
            'page' => 'admin/index'
         );

         $this->load->view( 'index', $data );
      }

      public function form_handler() {
         $validator = new Validator( $_POST );
         $validator->setRules( 'name', 'Name', 'minLength[4]|maxLength[8]' );
         $validator->setRules( 'date', 'Date', 'dateRange[ 12/27/10, 12/28/10 ]' );

         if( $validator->validate() ) {
            $validator->storeSuccessMessage( 'Success!' );
         }

         Util::redirect( 'admin', true );
      }

      // will be called if the user's request is invalid in the context of this 
      // controller
      public function _unknown( $request ) {
         $data = array(
            'title' => 'Not found',
            'page' => 'pages/not-found',
         );
         
         $this->load->view( 'index', $data );
      }

   }


