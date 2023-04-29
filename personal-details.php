<?php
/**
 * @package Personal details
 */
/*
Plugin Name: Personal details plugin
Description: A plugin which lets you manage personal details
Version: 1.0.0
Author: Armin Dajic
License: GPLv2 or later
Text Domain: personal-details-plugin
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2019 Automattic, Inc.
*/

if (!function_exists('add_action')) {
	print "Something is not right. Make sure Wordpress is installed.";
	exit;
}

// Errors on activation event will be stored in error.html
add_action('activated_plugin','check_database');
add_action('activated_plugin','save_error');

function save_error(){
  file_put_contents(ABSPATH. 'wp-content/plugins/personal-details/error.html', ob_get_contents());
}

function check_database() {
	// Will check if table exists, if not then it should be created here
	require 'database.php';
	$result = $mydb->get_results("SELECT COUNT(*) as num_of_tables FROM information_schema.tables WHERE table_schema='".$db_name."' AND table_name='".$db_table."';");
	$num_of_tables = (int)$result[0]->num_of_tables;
	if ($num_of_tables<=0) {
		// We need to create the table
		$mydb->query( 
			"
				CREATE TABLE ".$db_table." (
				  id INT NOT NULL AUTO_INCREMENT,
				  first_name  TEXT NOT NULL,
				  last_name  TEXT NOT NULL,
				  email  TEXT NOT NULL,
				  address  TEXT NOT NULL,
				  mobile  TEXT NOT NULL,
				  PRIMARY KEY (id)
				);
			"
		);		
	}
}

add_action('admin_menu', 'plugin_admin_add_page');

function plugin_admin_add_page() {
  //http://codex.wordpress.org/Function_Reference/add_menu_page
  add_menu_page( 'Personal details management', 'Personal details', 'manage_options', 'personal-details/adminpage.php');
}

function my_enqueue($hook) {
  //only for our special plugin admin page
  if( 'personal-details/adminpage.php' != $hook )
  return;
 
  wp_register_style('plugin_css', plugins_url('personal-details/pluginpage.css'), $deps = array(), $ver = "5.2");
  wp_enqueue_style('plugin_css');
 
  wp_enqueue_script('plugin_js', plugins_url('pluginpage.js', __FILE__ ), $deps = array('jquery'), $ver = "5.2");
}
 
add_action( 'admin_enqueue_scripts', 'my_enqueue' );
