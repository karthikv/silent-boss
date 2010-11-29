<h1>Admin Dashboard</h1>
<p>Welcome to the administrator dashboard.</p>

<?php
   $fields = array(  
      "name" => array(
         "type" => "text",
         "label" => "Name",
         "desc" => "so I can address you correctly!"
      ),

      "password" => array(
         "type" => "password",
         "label" => "Password",
      ),

      "radio" => array(
         "type" => "radio",
         "label" => "Radio",
         "value" => "radio-value",
      ),

      "radio-group" => array(
         "type" => "radio-group",
         "label" => "Radio Group",
         "radios" => array(
            "value1" => "Label 1",
            "value2" => "Label 2"
         )
      ),

      "checkbox" => array(
         "type" => "checkbox",
         "label" => "Checkbox",
         "value" => "checkbox-value",
      ),

      "checkbox-group" => array(
         "type" => "checkbox-group",
         "label" => "Checkbox Group",
         "checkboxes" => array(
            "value1" => "Label 1",
            "value2" => "Label 2"
         )
      ),

      "date" => array(
         "type" => "date",
         "label" => "Date",
         "value" => "10/1/2033",
         "desc" => "start date"
      ),

      "time" => array(
         "type" => "time",
         "label" => "Time",
         "value" => "1:5:5AM",
         "desc" => "start time"
      ),

      "choice" => array(
         "type" => "select",
         "label" => "Choice",
         "options" => array(
            "lorem" => "Lorem",
            "ipsum" => "Ipsum",
            "dolor-sit" => "Dolor Sit",
            "amet" => "Amet",
            "consectetur" => "Consectetur"
         )
      ),

      "message" => array(
         "type" => "textarea",
         "label" => "Message"
      ),

      "hidden" => array(
         "type" => "hidden",
         "value" => "Hidden value"
      )
   );

   $form = new Form( $fields );

   $submit = array(
      "value" => "Submit!"
   );

   $form->display( array(
      "action" => Util::getConfig( 'url_root' ) . "/admin/form_handler",
      "method" => "post"
   ), $submit );

