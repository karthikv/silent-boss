   
   </div>
	
   <div id="sidebar">
      <h3>Pages</h3>
      <ul>
         <li><a href=<?= curPage( '' ) ?>>Home<br /><span>Home, Sweet Home!</span></a></li>
         <li><a href=<?= curPage( 'add-event' ) ?>>Add Event<br /><span>Add an event that people can sign up for!</span></a></li>
         <li><a href=<?= curPage( 'list' ) ?>>Member List<br /><span>Need a list of members?</a></li>
         <li><a href=<?= curPage( 'alias-manager' ) ?>>Alias Manager<br /><span>Add e-mails to aliases.</span></a></li>
         <li><a href=<?= curPage( 'view-events' ) ?>>View Events<br /><span>View a list of all robotics events and sign up (or view who has signed up) for them!</span></a></li>
         <li><a href=<?= curPage( 'manage-officers' ) ?>>Manage Officers<br /><span>Edit who is on the officer team.</span></a></li>
         <li><a href=<?= curPage( 'edit-info' ) ?>>Edit Personal Information<br /><span>Did your e-mail change? What about your address? Change your information here.</span></a></li>
         <li><a href=<?= curPage( 'log-out' ) ?>>Log Out<br /><span>Thanks for stopping by!</span></a></li>
         <li><a href=<?= curPage( '' ) ?>>Log in to view these pages!<br /><span>You're just one step away.</span></a></li>
         <li><a href=<?= curPage( 'register' ) ?>>Register<br /><span>Don't have an account? Get one.</span></a></li>
         <li><a href=<?= curPage( 'search-members' ) ?>>Search Members<br /><span>Looking for someone?</span></a></li>
      </ul>
   </div>
	
	<?php
		function curPage( $url, $compare = false ) {
			$str = "\"/members/$url\"";
			$compare = preg_replace( '~^(.*?)(\?.*)?$~i', '$1', $compare );
			
			if( $url === $compare )
				$str .= " class=\"curPage\"";
			
			return $str;
		}
	?>

