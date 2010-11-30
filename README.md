# Silent Boss
Silent Boss is a light-weight and powerful PHP library focused on providing flexible
functionality with minimal code.

## Version 0.2
Among various improvements to the MVC system, Silent Boss now provides a(n):

- URL routing mechanism
- Active record database class
- Library to manage PHP sessions
- Form helper and accompanying validator to streamline the creation of HTML
  forms
- Loader class to manage the usage of views, models, libraries, and helpers

Rather than being a full-fledged CMS, Silent Boss has now changed direction and
will be a PHP library.

## Version 0.1
As of version 0.1, Silent Boss uses the [Model-View-Controller](http://en.wikipedia.org/wiki/Model%E2%80%93View%E2%80%93Controller) 
architecture to make it easy to add, remove, and update pages. Controllers,
views, and models may be found in the boss directory.

Other additions include:

- .htaccess to map all requests to index.php
- A config.php file in the root to set basic configuration values
- A default, minimalist theme

Planned future updates are:

- An administrator dashboard to streamline the addition of pages via markdown.
- A form builder to help create and validate forms without the normal hassle.
- More features that have yet to be decided upon

Documentation for silent boss is currently not available. It will be present
after a significant amount of development has been completed. Even so, the 
system acts in a predictable MVC manner. Simply add in a controller with 
methods to define new pages. To load views, models, and helpers, use the 
`loadView`, `loadModel`, and `loadHelper` functions. Feel free to look around
the code for an in-depth introduction. And, of course, contributions are always
welcome (see todo.txt).

