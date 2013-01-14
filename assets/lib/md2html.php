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
 * File containing all functions used to generate docs pages from markdown
 */

// load the markdown library
require 'markdown.php';

/**
 * Find the markdown page to load
 */
function whichpage($root)
{
	// path the the docs pages
	$root .= '/'.DOCROOT;

	// do we have a page passed to us? if not, load the homepage
	empty($_GET['page']) and $_GET['page'] = '/';

	// where we passed a directory? if so, go back to the intro page
	if (substr($_GET['page'], -1) == '/')
	{
		header("HTTP/1.1 301 Moved Permanenty");
		header("Location: ".BASEURL.dirname(INTROPAGE).'/'.basename(INTROPAGE, '.md'));
		exit;
	}

	// extract the directory and the filename
	$dir = realpath($root.ltrim(dirname($_GET['page']), '/'));
	$page = strpos($_GET['page'], '/') == false ? $_GET['page'] : ltrim(strrchr($_GET['page'], '/'), '/');

	// if the directory was invalid our outside the docroot, default to the homepage
	if ( ! $dir or strpos( $dir, rtrim($root, '/')) !== 0)
	{
		header("HTTP/1.1 301 Moved Permanenty");
		header("Location: ".BASEURL.dirname(INTROPAGE).'/'.basename(INTROPAGE, '.md'));
		exit;
	}

	$dir .= '/';

	// check if we can find the file using different case formats
	$path = false;
	foreach (array($page, strtolower($page), strtoupper($page), ucfirst($page)) as $page)
	{
		foreach (array($page, $page.'.md') as $page)
		{
			if (file_exists($path = $dir.$page))
			{
				break 2;
			}
		}
	}

	// return with a final verification
	return ($path and file_exists($path)) ? $path : false;
}

/**
 * load the file passed
 */
function getfile($file)
{
	// check if the file exists
	if ( ! file_exists($file))
	{
		die('<h2>Can not locate required file: '.$file.'</h2><br /><p>Click here to return to the <a href="index.php">homepage</a>.</p>');
	}

	return file_get_contents($file);
}

/**
 * generate the navigation
 */
function getindex()
{
	// load the directory tree
	$tree = read_dir(DOCROOT);

	// closure to generate the page tree
	$generate = function($tree, $path = '') use(&$generate)
	{
		// make sure the path is terminated with a slash
		empty($path) or $path = rtrim($path, '/').'/';

		// storage for the results
		$html = '';

		// loop over the tree
		foreach ($tree as $index => $node)
		{
			if (is_numeric($index))
			{
				$html .= "\t\t<li id=\"page_".md5($path.$node)."\"".((empty($path)or(strpos(BASEURL.$_GET['page'], $path)===0))?'':' style="display:none;"')."><div".($path.$node==BASEURL.$_GET['page'].'.md'?' class="current"':'')."><a href=\"".$path.makelink($node)."\">".maketitle($node)."</a></div></li>\n";
			}
			else
			{
				// new submenu
				$html .= "\t\t<li id=\"page_".md5($path.$index)."\" class=\"plus\"><div><a href=\"#\">".maketitle($index)."</a></div>\n\t<ul>\n".$generate($node, $path.$index)."\t</ul>\n";
			}
		}

		// return the results
		return $html;
	};

	// generate the index
	$html = <<<HTML
<br />
	<ul>
HTML;
	// add the root pages before the folder structure
	foreach ($tree as $index => $node)
	{
		if (is_numeric($index))
		{
			$html .= '<li><div><a href="'.BASEURL.makelink($node).'">'.maketitle($node).'</a></div></li>'."\n";
			unset($tree[$index]);
		}
	}

	// and add the rest of the tree
	$html .= $generate($tree, BASEURL);

	$html .= <<<HTML
	</ul>
HTML;

	// return the generated html
	return $html;
}

/**
 * generate a relative link from the markdown file found
 */
function makelink($link)
{
	$link = basename($link, '.md');
	return REWRITE ? $link : '?page='.$link;
}

/**
 * generate a link title from the markdown file found
 */
function maketitle($link)
{
	strpos($link, '-') !== false and $link = substr($link, strpos($link, '-')+1);
	return str_replace('_', ' ', basename($link, '.md'));
}

/**
 * Generate the HTML, using the defined page template
 */
function generate($page)
{
	// process the variable for the page
	$data = array(
		'{{YEAR}}' => date('Y'),
		'{{VERSION}}' => VERSION,
		'{{BASEURL}}' => BASEURL,
		'{{INDEX}}' => getindex(),
		'{{CONTENT}}' => markdown(getfile($page)),
	);

	// generate defined page template
	$template = str_replace(array_keys($data), array_values($data), getfile(TEMPLATE));

	// and return it
	return $template;
}

/**
 * Read directory
 *
 * @param   string      directory to read
 * @param   int         depth to recurse directory, 1 is only current and 0 or smaller is unlimited
 * @param   Array|null  array of partial regexes or non-array for default
 * @return  array  directory contents in an array
 */
function read_dir($path, $depth = 0, $filter = null)
{
	$path = realpath($path).'/';

	if ( ! is_dir($path))
	{
		throw new \InvalidPathException('Invalid path, directory cannot be read.');
	}

	if ( ! $fp = @opendir($path))
	{
		throw new \FileAccessException('Could not open directory for reading.');
	}

	// use default when not set
	is_array($filter) or $filter = array('!^\.');

	$files      = array();
	$dirs       = array();
	$new_depth  = $depth - 1;

	while (false !== ($file = readdir($fp)))
	{
		// Remove '.', '..'
		if (in_array($file, array('.', '..')))
		{
			continue;
		}
		// use filters when given
		elseif ( ! empty($filter))
		{
			$continue = false;  // whether or not to continue
			$matched  = false;  // whether any positive pattern matched
			$positive = false;  // whether positive filters are present
			foreach($filter as $f => $type)
			{
				if (is_numeric($f))
				{
					// generic rule
					$f = $type;
				}
				else
				{
					// type specific rule
					$is_file = is_file($path.$file);
					if (($type === 'file' and ! $is_file) or ($type !== 'file' and $is_file))
					{
						continue;
					}
				}

				$not = substr($f, 0, 1) == '!';  // whether it's a negative condition
				$f = $not ? substr($f, 1) : $f;
				// on negative condition a match leads to a continue
				if (($match = preg_match('/'.$f.'/uiD', $file) > 0) and $not)
				{
					$continue = true;
				}

				$positive = $positive ?: ! $not;  // whether a positive condition was encountered
				$matched  = $matched ?: ($match and ! $not);  // whether one of the filters has matched
			}

			// continue when negative matched or when positive filters and nothing matched
			if ($continue or $positive and ! $matched)
			{
				continue;
			}
		}

		if (@is_dir($path.$file))
		{
			// Use recursion when depth not depleted or not limited...
			if ($depth < 1 or $new_depth > 0)
			{
				$dirs[$file.'/'] = read_dir($path.$file.'/', $new_depth, $filter);
			}
			// ... or set dir to false when not read
			else
			{
				$dirs[$file.'/'] = false;
			}
		}
		else
		{
			$files[] = $file;
		}
	}

	closedir($fp);

	// sort dirs & files naturally and return array with dirs on top and files
	uksort($dirs, 'strnatcasecmp');
	natcasesort($files);
	return array_merge($dirs, $files);
}
