<?php
 /*
Plugin Name:  VidYen Point System
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  VidYen Point System [VYPS] allows you to create a rewards site using video ads or browser mining.
Version:      1.9.1
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

//Ok. I'm adding a custom fuction to the VYPS plugin. It's put on pages where, you just want to straight kick people out if they aren't admins.
//Similar to the login check, but the admins. This will put put on all pages that only admins should be able to see but not the shortcodes results.

function VYPS_check_if_true_admin(){

	//I'm going to be a little lenient and if you can edit users maybe you should be able to edit their point since you can just
	//Change roles at that point. May reconsider.
	if( current_user_can('install_plugin') OR current_user_can('edit_users') ){

		//echo "You good!"; //Debugging
		return;

	} else {

		echo "<br><br>You need true administrator rights to see this page!"; //Debugging
		exit; //Might be a better solution to iform before exit like an echo before hand, but well....
	}

}

register_activation_hook(__FILE__, 'vyps_points_install');

//Install the SQL tables for VYPS.
function vyps_points_install() {

    global $wpdb;

		//I have no clue why this is needed. I should learn, but I wasn't the original author. -Felty
		$charset_collate = $wpdb->get_charset_collate();

		//NOTE: I have the mind to make mediumint to int, but I wonder if you get 8 million log transactios that you should consider another solution than VYPS.

		//vyps_points table creation
    $table_name_points = $wpdb->prefix . 'vyps_points';

    $sql = "CREATE TABLE {$table_name_points} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		icon text NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

		//vyps_points_log. Notice how I loath th keep variable names the same in recycled code.
		//Visualization people. It's better for code to be ineffecient but readable than efficient and unreadable.
    $table_name_points_log = $wpdb->prefix . 'vyps_points_log';

    $sql .= "CREATE TABLE {$table_name_points_log} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
                reason varchar(128) NOT NULL,
                user_id mediumint(9) NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		point_id varchar(11) NOT NULL,
                points_amount double(64, 0) NOT NULL,
                adjustment varchar(100) NOT NULL,
								vyps_meta_id varchar(64) NOT NULL,
								vyps_meta_data varchar(128) NOT NULL,
								vyps_meta_amount double(64,0) NOT NULL,
								vyps_meta_subid1 mediumint(9) NOT NULL,
								vyps_meta_subid2 mediumint(9) NOT NULL,
								vyps_meta_subid3 mediumint(9) NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I am concerned that this used ABSPATH rather than the normie WP methods

    dbDelta($sql);
}

//adding menues
add_action('admin_menu', 'vyps_points_menu');

function vyps_points_menu() {

    $parent_page_title = "VidYen Point System";
    $parent_menu_title = 'VYPS';
    $capability = 'manage_options';
    $parent_menu_slug = 'vyps_points';
    $parent_function = 'vyps_points_parent_menu_page';
    add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);

    $page_title = "Manage Points";
    $menu_title = 'Point List';
    $menu_slug = 'vyps_point_list';
    $function = 'vyps_points_sub_menu_page';
    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

    $page_title = "Add Point";
    $menu_title = 'Add Point';
    $menu_slug = 'vyps_points_add';
    $function = 'vyps_points_add_sub_menu_page';
    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

    $page_title = "Point Log";
    $menu_title = 'Point Log';
    $menu_slug = 'admin_log';
    $function = 'vyps_admin_log';
    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}

/*** Menu Includes ***/
include( plugin_dir_path( __FILE__ ) . 'includes/menus/core_shortcodes_menu.php'); //Core shortcodes. Will be just deemed VYPS Shortcodes for menu's sake. Order 360
include( plugin_dir_path( __FILE__ ) . 'includes/menus/as_menu.php'); //Adscend menu 400 order
include( plugin_dir_path( __FILE__ ) . 'includes/menus/ch_menu.php'); //CH menu 430 order
include( plugin_dir_path( __FILE__ ) . 'includes/menus/vy256_menu.php'); //CH menu 440 order
include( plugin_dir_path( __FILE__ ) . 'includes/menus/wannads-menu.php'); //CH menu 420 order

/*** End of Menu Includes ***/

//Updated on 11.14.2018
function vyps_admin_log() {

	//Shortcode hard coding for the admin log. Yes, it is missing the actual user name (has the UID though) this should suffice
	$atts = array(
		'pid' => '0',
		'reason' => '0',
		'rows' => 50,
		'bootstrap' => 'no',
		'userid' => '0',
		'uid' => TRUE,
		'admin' => TRUE,
	);

	//Echo and not return due to the nature of this not being a shortcode and a page.
	echo vyps_public_log_func( $atts );

}

/* Main page informational page. Includes shortcodes, advertistments etc */

function vyps_points_parent_menu_page() {

	//Logo from base. If a plugin is installed not on the menu they can't see it not showing.
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

	include( plugin_dir_path( __FILE__ ) . '../vidyen-point-system-vyps/includes/credits.php');

}

function vyps_points_sub_menu_page() {
    global $wpdb;
    require plugin_dir_path(__FILE__) . 'manage_points.php';
}

function vyps_points_add_sub_menu_page() {
    global $wpdb;
    require plugin_dir_path(__FILE__) . 'add_point.php';
}

//add_action('show_user_profile', 'custom_user_profile_fields_points'); //NOTE: Actually here as well if you are looking at own profile. Should be on user list or edit points per user option.
//add_action('edit_user_profile', 'custom_user_profile_fields_points'); //NOTE: I do not think having on edit users is correct.
//add_action("user_new_form", "custom_user_profile_fields_points"); //NOTE: Nor here either... Will be removing.

//start add new column points in user table
//BTW I prefixed the next two functions with vyps_ as I have a feeling that might be used by other plugins
//Since it was generic

function vyps_register_custom_user_column($columns) {
    $columns['points'] = 'Points';
    return $columns;
}

/* The next function is important to show the points in the user table */

function vyps_register_custom_user_column_view($value, $column_name, $user_id) {
    $user_info = get_userdata($user_id);
    global $wpdb;
    $query_row = "select *, sum(points_amount) as sum from {$wpdb->prefix}vyps_points_log group by point_id, user_id having user_id = '{$user_id}'";
    $row_data = $wpdb->get_results($query_row);

		//I need to update this eventually. I realized I didn't fix this, but its only calling non-user input data from the WPDB. I still don't like the -> in fact I hate -> calls
    $points = '';
    if (!empty($row_data)) {
        foreach($row_data as $type){
            $query_for_name = "select * from {$wpdb->prefix}vyps_points where id= '{$type->point_id}'";
            $row_data2 = $wpdb->get_row($query_for_name);
            $points .= '<b>' . $type->sum . '</b> ' . $row_data2->name. '<br>';
        }
    } else {
        $points = '';
    }

    if ($column_name == 'points')
        return $points;
    return $value;
}

add_action('manage_users_columns', 'vyps_register_custom_user_column');
add_action('manage_users_custom_column', 'vyps_register_custom_user_column_view', 10, 3);

//BTW this was all original from orion (Are they ever getting the daily login). I have no clue what cgc_ub_action_links stands for but I know what it does. I'll call it something more informative.
function vyps_user_menu_action_links($actions, $user_object) {

		//Ok. The nonce.
		$vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );
    $actions['edit_points'] = "<a class='cgc_ub_edit_badges' href='" . admin_url("admin.php?page=vyps_point_list&edituserpoints=$user_object->ID&_wpnonce=$vyps_nonce_check") . "'>" . __('Edit Points') . "</a>";
    return $actions;
}

add_filter('user_row_actions', 'vyps_user_menu_action_links', 10, 2);

/*** SHORTCODE INCLUDES IN BASE ***/

//It has dawned on me that the ../vidyen-point-etc may not be needed actually?

//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/debug.php'); //We got so complicated needed to help users troubleshoot server errors. Left off when not needed but will make more detailed later.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspl.php'); //Point Log
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsbc.php'); //Balance shortcode
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsbc_ww.php'); //Balance for woowallet as the built in one annoys me with refresh update
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt.php'); //Point Transfer shorcode raw format. Maybe should rename to vypspt_raw.php
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt_tbl.php'); //Point Transfer Table code. One day. I'm goign to retire PT, but admins might need it.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt_2in.php'); //Point Transfer with two inputs.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspe.php'); //Point Exchange is going to depreciate all earlier versions of Point Transfer
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt_ww.php'); //WW point transfer bridge Shortcode table
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypslg.php'); //You are not logged in blank shortcode.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsch.php'); //Rolling the CH in.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-adscend.php'); //Rolling the Adscend in. I hate ads but I'm being pragmatic
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wannads.php'); //Adding Wannads support. Not the naming convention change.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wannads-postback.php'); //Wannads post back
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypstr.php'); //Threshold Raffle shortcode. This is going to be cool
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypstr_cl.php'); //Current game log so you can see progress. Need to work on a game history log.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspb.php'); //Point balances for public viewing (and maybe some leaderboard stuff)
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps256.php'); //VYPS WMP shortcode
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps256_dev.php'); //Developement version
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps256_debug.php'); //Debug version that shows output. Does not throttle btw
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsxmr_wallet.php'); //Let's user add XMR wallet to usermeta table in WP
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsws_pick.php'); //Shareholder pick. Is shortcode but used elsewhere
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps_refer.php'); //Referal shortcode display shortcode.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps_refer_balance.php'); //Referal balance shortcdoe. I really need to functionize this.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-quads.php'); //QUADS the game. Moving to a new tomorrow!

/*** End of Shortcode Includes ***/

/*** FUNCTION INCLUDES***/

/*** CORE ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_procheck_func.php'); //Pro checking.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_wannads_pro_func.php'); //Pro checking.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_func.php'); //Functionalized point name and icon calls (FINALLY! -Felty)
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_balance_func.php'); //Functionalized balance (FINALLY! -Felty)
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_public_log_func.php'); //Functionalized public log (This should have been months ago! -Felty)
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_add_func.php'); //Functionalized adds to the log
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_deduct_func.php'); //Functionalized deducts to the log
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_meta_check_func.php'); //Meta checking. See if there is a duplicate transaction.
/*** REFER ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/refer/vyps_current_refer_func.php'); //General function to check if current user has a refer set, is valid, and returns it.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/refer/vyps_create_refer_func.php'); //Function to create encode based off user id.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/refer/vyps_is_refer_func.php'); //Function to make sure it is really a refer.

/*** WALLET ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/wallet/vyps_dashed_slug_bal_check_func.php'); //Tie in to Dashed Slug's wallet to check if transfer balance is possible
include( plugin_dir_path( __FILE__ ) . 'includes/functions/wallet/vyps_dashed_slug_move_func.php'); //Function to transfer points between users if points allow.

/*** WW ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ww/vyps_woowallet_credit_func.php'); //Function to credit the WooWallet.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ww/vyps_woowallet_debit_func.php'); //Function to debit the WooWallet.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ww/vyps_woowallet_bal_func.php'); //Function to check bal the WooWallet.

/*** End of Function Includes ***/
