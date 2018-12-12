<?php
 /*
Plugin Name:  VidYen VidHash
Description:  Have users mine crypto currency while watching your embedded videos
Version:      0.0.16
Author:       VidYen, LLC
Author URI:   https://vidyen.com/
License:      GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, version 2 of the License
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* See <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



register_activation_hook(__FILE__, 'vy_vidhash_install');

//Install the SQL tables for VY VidHash
function vy_vidhash_install()
{
	//Actually, I can't think of a need for the SQL just yet.
}

//Adding the menu function
add_action('admin_menu', 'vy_vidhash_menu');

function vy_vidhash_menu()
{
	//Only need to install the one menu to explain shortcode usage
  $parent_page_title = "VidYen VidHash";
  $parent_menu_title = 'VY VidHash';
  $capability = 'manage_options';
  $parent_menu_slug = 'vy_vidhash';
  $parent_function = 'vy_vidhash_parent_menu_page';
  add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);

}

//The actual page... I should throw this on its own include. Down the road maybe.
function vy_vidhash_parent_menu_page() {

	//It's possible we don't use the VYPS logo since no points.
	echo '<br><br><img src="' . plugins_url( '../vidyen-point-system-vyps/images/logo.png', __FILE__ ) . '" > ';

	//Static text for the base plugin
	echo
	"<h1>VidYen Point System Base Plugin</h1>
	<p>VYPS allows you to gamify monetization by giving your users a reason to turn off adblockers in return for rewards and recognition.</p>
	<p>This is a multipart system - similar to WooCommerce - which allows WordPress administrators to track points for rewards in monetization systems.</p>
	<p>To prevent catastrophic data loss, uninstalling this plugin will no longer automatically delete the VYPS user data. To drop your VYPS tables from the WPDB, use the VYPS Uninstall plugin to do a clean install.</p>
	<br>
	<h2>Base Plugin Instructions</h2>
	<p>Add points by navigating to the Add Points menu.</p>
	<p>To modify or see a user’s current point balance, go to the Users panel and use the context menu by &quot;Edit User Information&quot; under &quot;Edit Points&quot;.</p>
	<p>To see a log of all user transactions, go to &quot;Point Log&quot; in the VidYen Points menu.</p>
	<p><b>See the shortcode menus on how to integrate on your WordPress site.</b></p>
	";

}

/*** BEGIN SHORTCODE INCLUDES ***/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-vidhash-shortcode.php'); //For now just the actual SC [vy-vidhash]
