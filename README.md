# Silent Boss
Silent Boss is a light-weight and powerful CMS focused on providing flexible
functionality with minimal code.

## Version 0.1
As of version 0.1, Silent Boss uses the [Model-View-Controller](http://en.wikipedia.org/wiki/Model%E2%80%93View%E2%80%93Controller) 
architecture to make it easy to add, remove, and update pages. Controllers,
views, and models may be found in the boss directory.

Other additions include:

- .htaccess to map all requests to index.php
- Config.php file in the root to set basic configuration values
- Routing of requests
- Database helper class
- Default, minimalist theme

Planned future updates are:

- Form helper to create and validate forms without the normal hassle.
- More features that have yet to be decided upon

Documentation for silent boss is currently not available. It will be present
after a significant amount of development has been completed. Even so, the 
system acts in a predictable MVC manner. Simply add in a controller with 
methods to define new pages. To load views, models, and helpers, use the 
`loadView`, `loadModel`, and `loadHelper` functions. Feel free to look around
the code for an in-depth introduction. And, of course, contributions are always
welcome (see todo.txt).

