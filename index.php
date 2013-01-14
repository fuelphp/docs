<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * One file documenation system using Markdown files,
 * containing FFM (Fuel Flavoured Markdown)
 */

// FuelPHP version of these docs
define('VERSION', '1.5-dev');

// documentation page template to use
define('TEMPLATE', 'assets/html/v1template.html');

// whether or not we have to generate rewrite compatible URL's
define('REWRITE', isset($_GET['rewrite']) and $_GET['rewrite'] == 1);

// url to the API docs for this version
define('APIROOT', 'http://dev-api.fuelphp.com/classes/');

// relative path to the markdown doc files
define('DOCROOT', 'docs/');

// page to load when the site root is requested
define('INTROPAGE', '01-Introduction/01-Welcome.md');

// base url for this script
define('BASEURL', dirname($_SERVER['PHP_SELF']).'/');

// load our markdown-2-html library
require __DIR__.'/assets/lib/md2html.php';

// find the file to show
$page = whichpage(__DIR__) or $page = DOCROOT.'../NOTFOUND.md';

// and generate the HTML
echo generate($page);
