<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//twitch vyps Shortcode.

function vidyen_twitch_vyps_func($atts) {

  //Some naming conventions. We will not use the word miner or worker
  //The functions will simply be... video player etc etc
  //Yes the JS files haven't been renamed yet, but lets get to that

  //I have decided if user is not logged in, that shouldn't waste cpu power
  //Then anything else comes through.
  if ( ! is_user_logged_in() )
  {
    return;
  }

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

    return $html_output_error; //Return both the error along with original form.
  }
  elseif (vyps_xmr_wallet_check_func($wallet) == 2) //This means the wallet does not start with a 4 or 8
  {
    $html_output_error = '<p> Error: Wallet address does not start with 4 or 8 so most likley an invalid XMR address!</p>'; //Error output
    return $html_output_error; //Return both the error along with original form.
  }
  elseif (vyps_xmr_wallet_check_func($wallet) != 1)
  {
    $html_output_error = '<p> Error: Uknown error!</p>'; //Error output
    return $html_output_error; //Return both the error along with original form.
  }
  else
  {
    $mo_site_wallet = $wallet; //Double passing down in ajax
  }

  $mo_site_worker = '.' . get_current_user_id();

  //OK we have to do this using MO in the shortcode itself rather than ajax since we need the shortcode info for PI etc. Not sure why I didn't think of that before.

  /*** MoneroOcean Gets***/
  //Site get
  $site_url = 'https://api.moneroocean.stream/miner/' . $mo_site_wallet . '/stats/' . $mo_site_worker;
  $site_mo_response = wp_remote_get( $site_url );
  if ( is_array( $site_mo_response ) )
  {
    $site_mo_response = $site_mo_response['body']; // use the content
    $site_mo_response = json_decode($site_mo_response, TRUE);
    if (array_key_exists('totalHash', $site_mo_response))
    {
        $site_total_hashes = number_format(floatval($site_mo_response['totalHash']));
        $site_hash_per_second = number_format(intval($site_mo_response['hash']));
    }
    else
    {
      $site_total_hashes = 0;
      $site_hash_per_second = 0;
    }
  }

  //I'm going to make this happen every 60 seconds not to hammer MO's api server
  $mo_ajax_html_output = "
    <script>
      function pull_mo_stats()
      {
        jQuery(document).ready(function($) {
         var data = {
           'action': 'vyps_mo_api_action',
           'site_wallet': '$mo_site_wallet',
           'site_worker': '$mo_site_worker',
         };
         // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
         jQuery.post(ajaxurl, data, function(response) {
           output_response = JSON.parse(response);
           document.getElementById('site_hashes').innerHTML = output_response.site_hashes;
           document.getElementById('site_hash_per_second').innerHTML = output_response.site_hash_per_second + ' H/s';
         });
        });
      }

      //Refresh the MO
      function moAjaxTimerPrimus()
      {
        //Should call ajax every 60 seconds
        var ajaxTime = 1;
        var id = setInterval(moAjaxTimeFrame, 1000); //1000 is 1 second
        function moAjaxTimeFrame() {
          if (ajaxTime >= 60) {
            pull_mo_stats();
            console.log('Ping MoneroOcean');
            clearInterval(id);
            moAjaxTimerSecondus()
          } else {
            ajaxTime++;
          }
        }
      }

      //Refresh the MO
      function moAjaxTimerSecondus()
      {
        //Should call ajax every 60 seconds
        var ajaxTime = 1;
        var id = setInterval(moAjaxTimeFrame, 1000);
        function moAjaxTimeFrame() {
          if (ajaxTime >= 60) {
            pull_mo_stats();
            console.log('Ping MoneroOcean');
            clearInterval(id);
            moAjaxTimerPrimus();
          } else {
            ajaxTime++;
          }
        }
      }
      </script>";

      $mo_site_html_output = "<tr>
        <td><div><a href=\"https://moneroocean.stream/#/dashboard?addr=$mo_site_wallet\" target=\"_blank\">Site Info</a></div></td>
        <td><div id=\"client_info\">Worker: $mo_site_worker</div></td>
      </tr>
      <tr>
        <td><div>Average Speed:</div></td>
        <td><div id=\"site_hash_per_second\">(Please Wait)</div></td>
      </tr>
      <tr>
        <td><div>Total Hashes:</div></td>
        <td><div id=\"site_hashes\">(Please Wait)</div></td>
      </tr>";

  return $twitch_vyps_html_load; //Shortcode output
}

/*** Add the shortcode to the WP environment ***/

add_shortcode( 'vy-twitch-vyps', 'vidyen_twitch_vyps_func');
