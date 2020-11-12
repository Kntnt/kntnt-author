<?php

/**
 * Plugin main file.
 *
 * @wordpress-plugin
 * Plugin Name:       Kntnt Author
 * Plugin URI:        https://www.kntnt.com/
 * GitHub Plugin URI: https://github.com/Kntnt/kntnt-post-import
 * Description:       Allows multiple authors and improves user edit form.
 * Version:           1.2.0
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Requires PHP:      7.3
 */


namespace Kntnt\Author;

// Uncomment following line to debug this plugin except the Importer class.
// define( 'KNTNT_AUTHOR_DEBUG', true );

require 'autoload.php';

defined( 'WPINC' ) && new Plugin;