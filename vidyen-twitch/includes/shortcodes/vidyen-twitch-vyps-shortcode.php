<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//twitch vyps Shortcode.

function vidyen_twitch_vyps_func($atts) {

  //Some naming conventions. We will not use the word miner or worker
  //The functions will simply be... video player etc etc
  //Yes the JS files haven't been renamed yet, but lets get to that

  $atts = shortcode_atts(
      array(
          'channel' => '',
          'wallet' => '',
          'width' => '854',
          'height' => '480',
          'site' => 'twitch',
      ), $atts, 'vy-twitch-vyps' );

  //FIRST THINGS FIRST Check to see if VYPS is actually installed along side this.
  //This is a function that is in latest version. Its possible that someone didn't update. (ノಠ益ಠ)ノ彡┻━┻
  if ( !function_exists('vyps_mo_api_action') )
  {
    return 'ERROR! Latest verions of VidYen Point System not installed!';
  }

  //Wallet check
  $wallet = $atts['wallet'];

  if (vyps_xmr_wallet_check_func($wallet) == 3) //This means that the wallet lenght was no longer than 90 characters
  {
    $html_output_error = '<p>Error: Wallet Address not longer than 90! Possible invalid XMR Address!</p>'; //Error output

    return $html_output_error . $xmr_address_form_html; //Return both the error along with original form.
  }
  elseif (vyps_xmr_wallet_check_func($wallet) == 2) //This means the wallet does not start with a 4 or 8
  {
    $html_output_error = '<p> Error: Wallet address does not start with 4 or 8 so most likley an invalid XMR address!</p>'; //Error output
    return $html_output_error . $xmr_address_form_html; //Return both the error along with original form.
  }
  elseif (vyps_xmr_wallet_check_func($wallet) != 1)
  {
    $html_output_error = '<p> Error: Uknown error!</p>'; //Error output
    return $html_output_error . $xmr_address_form_html; //Return both the error along with original form.
  }
  else
  {
    $sm_site_key = $wallet; //Extra jump but should be fine now
    $mo_site_wallet = $sm_site_key; //Double passing down in ajax
  }

  return $twitch_vyps_html_load; //Shortcode output
}

/*** Add the shortcode to the WP environment ***/

add_shortcode( 'vy-twitch-vyps', 'vidyen_twitch_vyps_func');
