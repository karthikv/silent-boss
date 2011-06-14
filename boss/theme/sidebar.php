   
   </div>
	
   <div id="sidebar">
      <h3>Pages</h3>
      <ul>
         <?php
            if( Util::isLoggedIn() )
            {

            }
            else
            {
               echo Util::navigationLink( '', 'Home', 'Home, Sweet Home!' );
               echo Util::navigationLink( 'log-in', 'Log In', 'Let\'s get it started!' );
            }
         ?>
      </ul>
   </div>
	
