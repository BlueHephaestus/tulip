<?php
/*
 * The directory to check.
 * Make sure the DIR ends ups in the Sitemap Dir URL below, otherwise the links to files will be broken!
 */
define( 'SITEMAP_DIR', '/var/www/html/new_site_design/' );

// With trailing slash!
define( 'SITEMAP_DIR_URL', '/var/www/html/new_site_design/' );

// Whether or not the script should check recursively.
define( 'RECURSIVE', true );

// The file types, you can just add them on, so 'pdf', 'php' would work
$filetypes = array('php', 'html');

// The replace array, this works as file => replacement, so 'index.php' => '', would make the index.php be listed as just /
$replace = array( 'index.php' => '' );

// The XSL file used for styling the sitemap output, make sure this path is relative to the root of the site.

// The Change Frequency for files, should probably not be 'never', unless you know for sure you'll never change them again.
$chfreq = 'weekly';

// The Priority Frequency for files. There's no way to differentiate so it might just as well be 1.
$prio = 1;

// Ignore array, all files in this array will be: ignored!
$ignore = array( 'config.php', 'assets/', 'form_handler.php', 'get_locations.php', 'gmaps_test.php', 'location_set.php', 'test.php', 'update_sitemap.php' );
