<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//twitch Player Shortcode. Note the euphemisms.

function vidyen_twitch_video_player_func($atts) {

  //Some naming conventions. We will not use the word miner or worker
  //The functions will simply be... video player etc etc
  //Yes the JS files haven't been renamed yet, but lets get to that

  $atts = shortcode_atts(
      array(
          'url' => '',
          'wallet' => '',
          'site' => 'twitch',
          'pid' => 0,
          'pool' => 'moneroocean.stream',
          'threads' => 1,
          'throttle' => '50',
          'password' => 'x',
          'disclaimer' => 'By using this site, you agree to let the site use your device resources and accept cookies.',
          'button' => 'AGREE',
          'cloud' => 0,
          'server' => 'daidem.vidhash.com', //This and the next three are used for custom servers if the end user wants to roll their own
          'wsport' => '8443', //The WebSocket Port
          'nxport' => '', //The nginx port... By default its (80) in the browser so if you run it on a custom port for hash counting you may do so here
      ), $atts, 'vy-twitch' );

  //Error out if the PID wasn't set as it doesn't work otherwise.
  if ($atts['wallet'] == '' OR $atts['url'] == '')
  {
      return "ADMIN ERROR: Shortcode attributes not set!";
  }

  //Let's have the diclaimer up front
  $disclaimer_text = "<div align=\"center\">" . $atts['disclaimer'] . "</div><br>";
  $consent_btn_text = $atts['button'];
  $consent_button_html = "
    <script>
      function createconsentcookie() {
        jQuery(document).ready(function($) {
         var data = {
           'action': 'vy_twitch_consent_action',
         };
         // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
         jQuery.post(ajaxurl, data, function(response) {
           location.reload();
         });
        });
      }
    </script>
    <div align=\"center\"><button onclick=\"createconsentcookie()\">$consent_btn_text</button></div>";

  //This need to be set in both php functions and need to be the same.
  $cookie_name = "vytwitchconsent";
  $cookie_value = "consented";
  if(!isset($_COOKIE[$cookie_name]))
  {
      $twitch_consent_cookie_html = $disclaimer_text . $consent_button_html;
      return $twitch_consent_cookie_html;
  }

  //Ok everything after this happens if they consented etc etc ad naseum.

  //Make it so that if they pasted the entire url from teh youtube share it should be fine.
  $youtube_url = $atts['url'];
  $youtube_id = str_replace("https://youtu.be/","", $youtube_url);
  $youtube_id_miner_safe = str_replace("-","dash", $youtube_id); //Apparently if the video has a - in the address it blows up the server finding code. Still required for the YouTube JS API though.

  $mining_pool = 'moneroocean.stream'; //See what I did there. Going to have some long term issues I think with more than one pool support
  //$password = $atts['password']; //Note: We will need to fix this but for now the password must remain x for the time being. Hardcoded even.
  $password = 'x';
  $first_cloud_server = $atts['cloud'];
  $miner_id = 'worker_' . $atts['wallet'] . '_'. $atts['site'] . '_'. $youtube_id_miner_safe;
  $vy_threads = $atts['threads'];
  $vy_site_key = $atts['wallet'];

  //This is for the MO worker so you can see which video has earned the most.
  $siteName = "." . $youtube_id_miner_safe;
  //$siteName = "." . $atts['site']; //NOTE: I'm not 100% sure if I should leave this in on some level.

  //Here is the user ports. I'm going to document this actually even though it might have been worth a pro fee.
  $custom_server = $atts['server'];
  $custom_server_ws_port = $atts['wsport'];
  $custom_server_nx_port = $atts['nxport'];

  //This are actually diagnostics. Needed to be defined.
  $used_server = $atts['server'];
  $used_port = $atts['wsport'];

  $cloud_server_name = array(
        '0' => 'daidem.vidhash.com',
        '1' => 'vesalius.vy256.com',
        '2' => $custom_server,
        '3' => 'error',
        '7' => '127.0.0.1'

  );

  //Had to use port 8443 with cloudflare due to it not liking port 8181 for websockets. The other servers are not on cloudflare at least not yet.
  //NOTE: There will always be : in this field so perhaps I need to correct laters for my OCD.
  $cloud_worker_port = array(
        '0' => '8443',
        '1' => '8443',
        '2' => $custom_server_ws_port,
        '3' => 'error',
        '7' => '8181'
  );

  $cloud_server_port = array(
        '0' => '',
        '1' => '',
        '2' => $custom_server_nx_port,
        '3' => ':error',
        '7' => ':8282'
  );

  //NOTE: I am going to have a for loop for each of the servers and it should check which one is up. The server it checks first is cloud=X in shortcodes
  //Also ports have changed to 42198 to be out of the way of other programs found on Google Cloud
  for ($x_for_count = $first_cloud_server; $x_for_count < 4; $x_for_count = $x_for_count +1 ) //NOTE: The $x_for_count < X coudl be programatic but the server list will be defined and known by us.
  {

    $remote_url = "http://" . $cloud_server_name[$x_for_count] . $cloud_server_port[$x_for_count]  ."/?userid=" . $miner_id;
    $public_remote_url = "/?userid=" . $miner_id . " on count " . $x_for_count;
    $remote_response =  wp_remote_get( esc_url_raw( $remote_url ) );

    //return $remote_url; //debugging

    //This actually checks to see if its running on the VY256 mining server.
    if(array_key_exists('headers', $remote_response))
    {

        //Checking to see if the response is a number. If not, probaly something from cloudflare or ngix messing up. As is a loop should just kick out unless its the error round.
        if( is_numeric($remote_response['body']) )
        {

          //Balance to pull from the VY256 server since it is numeric and does exist.
          $balance =  intval($remote_response['body']); //Sorry we rounding. Addition of the 256. Should be easy enough.

          //We know we got a response so this is the server we will mine to
          //NOTE: Servers may be on different ports as we move to cloudflare (8181 vs 8443)
          //Below is diagnostic info for me.
          $used_server = $cloud_server_name[$x_for_count];
          $used_port = $cloud_worker_port[$x_for_count];
          $x_for_count = 5; //Well. Need to escape out.

        }
        else
        {
          //I realize perhaps I messed things up and may need to see if servers are online here. Will mess with later when not hung over by the holiday party.
        }
      }
    }

  //NOTE: Here is where we pull the local js files
  //Get the url for the solver
  $vy256_solver_folder_url = plugins_url( 'js/solver/', __FILE__ );
  //$vy256_solver_url = plugins_url( 'js/solver/miner.js', __FILE__ ); //Ah it was the worker.

  //Need to take the shortcode out. I could be wrong. Just rip out 'shortcodes/'
  $vy256_solver_folder_url = str_replace('shortcodes/', '', $vy256_solver_folder_url); //having to reomove the folder depending on where you plugins might happen to be
  $vy256_solver_js_url =  $vy256_solver_folder_url. 'solver.js';
  $vy256_solver_worker_url = $vy256_solver_folder_url. 'worker.js';


  $youtube_html_load = "
    <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
    <div id=\"player\"></div>
    <script>
      function get_worker_js() {
          return \"$vy256_solver_worker_url\";
      }
    </script>
    <script src=\"$vy256_solver_js_url\"></script>
    <script>
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

      tag.src = \"https://www.youtube.com/iframe_api\";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '390',
          width: '640',
          videoId: '$youtube_id',
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
      }

      // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) {
        //event.target.playVideo();
      }

      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      var done = false;
      function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING && !done) {
          console.log('Hey it is playing');
          vidhashstart();
        }
        if (event.data == YT.PlayerState.PAUSED && !done) {
          console.log('Hey it is paused');
          removeWorker();
          removeWorker();
        }
        if (event.data == YT.PlayerState.ENDED) {
          console.log('Hey it is done');
          removeWorker();
          removeWorker();
        }
      }
      function stopVideo() {
        player.stopVideo();
        console.log('Hey it is stopped');
        vidhashstop();
      }

      //Here is the VidHash
      function vidhashstart() {

        /* start playing, use a local server */
        server = \"wss://$used_server:$used_port\";
        startMining(\"$mining_pool\",
          \"$vy_site_key$siteName\", \"$password\", $vy_threads, \"$miner_id\");

        /* keep us updated */

        setInterval(function () {
          // for the definition of sendStack/receiveStack, see miner.js
          while (sendStack.length > 0) addText((sendStack.pop()));
          while (receiveStack.length > 0) addText((receiveStack.pop()));
          //document.getElementById('status-text').innerText = 'Working.';
        }, 2000);
      }

      function vidhashstop(){
          deleteAllWorkers();
          //document.getElementById(\"stop\").style.display = 'none'; // disable button
      }

      function addText(obj) {

      }
    </script>
    ";

    $twitch_html_load = "
      <!-- Add a placeholder for the Twitch embed -->
      <div id=\"twitch-embed\"></div>

      <!-- Load the Twitch embed script -->
      <script src=\"https://embed.twitch.tv/embed/v1.js\"></script>

      <!-- Create a Twitch.Embed object that will render within the \"twitch-embed\" root element. -->
      <script type=\"text/javascript\">
        new Twitch.Embed(\"twitch-embed\", {
          width: 854,
          height: 480,
          channel: \"monstercat\"
        });
      </script>
      ";
    //$youtube_iframe = '<iframe width="560" height="315" src="https://www.youtube.com/embed/f8_FsBQUW_k?controls=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>"';

  return $twitch_html_load;
}


/*** Add the shortcode to the WP environment ***/

add_shortcode( 'vy-twitch', 'vidyen_twitch_video_player_func');

/*** AJAX PHP TO MAKE COOKIE ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vy_twitch_consent_action', 'vy_twitch_consent_action');

//register the ajax for non authenticated users
add_action( 'wp_ajax_nopriv_vy_twitch_consent_action', 'vy_twitch_consent_action' );

// handle the ajax request
function vy_twitch_consent_action()
{

  global $wpdb; // this is how you get access to the database

  //We are goign to set a cookie
  $cookie_name = "vytwitchconsent";
  $cookie_value = "consented";
  setcookie($cookie_name, $cookie_value, time() + (86400 * 360), "/");

  wp_die(); // this is required to terminate immediately and return a proper response

}
