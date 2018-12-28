<?php
 /*
Plugin Name:  Vidyen Twitch Monero Miner
Plugin URI: https://wordpress.org/plugins/vidyen-vidhash/
Description:  Have users mine Monero crypto currency while watching your Twitch stream
Version:      1.0.0
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
function vy_vidhash_parent_menu_page()
{
	//It's possible we don't use the VYPS logo since no points.
  $vy_logo_url = plugins_url( 'images/vy_logo.png', __FILE__ );
  $vy256_worker_url = plugins_url( 'images/vyworker_001.gif', __FILE__ );
  $vidhash_icon_url = plugins_url( 'images/icon-256x256.png', __FILE__ );

  //The HTML output.
	echo '<br><br><img src="' . $vidhash_icon_url . '" > ';

	//Static text for the base plugin
	echo
	"<h1>Vidyen Twitch Monero Miner</h1>
	<p>The plugin uses the VidYen Monero miner to mine while an embedded Twitch stream is playing. It ties into the Twitch JS API and only mines while videos are being played.</p>
	<p>Does not use the VidYen Point System rewards, but at same time does not require you user to log in to mien for you. Just a cookie consent via an AJAX post.</p>
	<h2>Shortcode Instructions</h2>
	<p>Required:<b>[vy-vidhash wallet=(your XMR Wallet) url=(the code at the end of the video you want to embed)]</b></p>
	<p>Optional for languages other than English:<b>[vy-vidhash disclaimer=\"Your message about cookies and resources\" button=\"the button text\"]</b></p>
	<p>Again this uses Monero Ocean for the backup like the VidYen point system.</p>
	<p>To see your progress towards payout, vist the <a href=\"https://moneroocean.stream/#/dashboard\" target=\"_blank\">dashboard</a> and add your XMR wallet where it says Enter Payment Address at bottom of page. There you can see total hashes, current hash rate, and account option if you wish to change payout rate.</p>
	<p>Keep in mind, unlike Coinhive, you can use this in conjunction with GPU miners to the same pool.</p>
	<p>Working Example: <b>[vy-vidhash wallet=8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb url=https://youtu.be/G02wKufX3nw]</b>
  <p><b>NOTE:</b> This only works with youtu.be links or the last part of the address (i.e. G02wKufX3nw from https://youtu.be/G02wKufX3nw)</p>
	<p>Since this is running on our servers and we expanded the code, VidYen, LLC is the one handling the support. Please go to our <a href=\"https://www.vidyen.com/contact/\" target=\"_blank\">contact page</a> or if you need assistance immediatly, join the <a href=\"https://discord.gg/6svN5sS\" target=\"_blank\">VidYen Discord</a> and PM Felty. (It will ping my phone, so do not abuse. -Felty)</p></p>
  <h2>Getting a Monero wallet</h2>
  <p>If you are completely new to Monero and need a wallet address, you can quickly get one at <a href=\"https://mymonero.com/\" target=\"_blank\">My Monero</a> or if you want a more technical or secure wallet visit <a href=\"https://ww.getmonero.org/\" target=\"_blank\">Get Monero</a> on how to create an enanched wallet.</p>
	";

	echo '<br><br><img src="' . $vy256_worker_url . '" > ';
}

/*** BEGIN SHORTCODE INCLUDES ***/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-twitch-shortcode.php'); //For now just the actual SC [vy-vidhash]
