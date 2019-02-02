<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//VidHash Player Shortcode. Note the euphemisms.

function vidyen_vidhash_video_player_func($atts) {

  //Some naming conventions. We will not use the word miner or worker
  //The functions will simply be... video player etc etc
  //Yes the JS files haven't been renamed yet, but lets get to that

  $atts = shortcode_atts(
      array(
          'url' => '',
          'wallet' => '',
          'site' => 'vidhash',
          'pid' => 0,
          'pool' => 'moneroocean.stream',
          'threads' => 2,
          'throttle' => '50',
          'password' => 'x',
          'disclaimer' => 'By using this site, you agree to let the site use your device resources and accept cookies.',
          'button' => 'AGREE',
          'cloud' => 0,
          'server' => 'daidem.vidhash.com', //This and the next three are used for custom servers if the end user wants to roll their own
          'wsport' => '8443', //The WebSocket Port
          'nxport' => '', //The nginx port... By default its (80) in the browser so if you run it on a custom port for hash counting you may do so here
          'vyps' => FALSE,
      ), $atts, 'vy-vidhash' );

  //Error out if the PID wasn't set as it doesn't work otherwise.
  if ($atts['wallet'] == '' OR $atts['url'] == '')
  {
      return "ADMIN ERROR: Shortcode attributes not set!";
  }

  //Let's have the diclaimer up front
  $disclaimer_text = "<div align=\"center\">" . $atts['disclaimer'] . "</div>";
  $consent_btn_text = $atts['button'];
  $consent_button_html = "
    <script>
      function createconsentcookie() {
        jQuery(document).ready(function($) {
         var data = {
           'action': 'vy_vidhash_consent_action',
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
  $cookie_name = "vidhashconsent";
  $cookie_value = "consented";
  if(!isset($_COOKIE[$cookie_name]))
  {
      $vidhash_consent_cookie_html = $disclaimer_text . $consent_button_html;
      return $vidhash_consent_cookie_html;
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

  //VYPS MODE
  $vyps_mode = $atts['vyps'];

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

  //I'm using the same code as vyps here. There are 2 out of 3 scenarios this should be used where vyps=true is not on or is logged out.
  if(!is_user_logged_in() OR $vyps_mode != TRUE)
  {
    //OK going to do a shuffle of servers to pick one at random from top.
    if(empty($custom_server))
    {
      $server_random_pick = mt_rand(0,2); //Some distribution

      $server_name = array(
            array('vesalius.vy256.com', '8443'), //0,0 0,1
            array('daidem.vidhash.com', '8443'), //1,0 1,1
            array('savona.vy256.com', '8183'), //2,0 2,1
      );

      shuffle($server_name);
    }
    else //Going to allow for custom servers is admin wants. No need for redudance as its on them.
    {
      $server_name = array(
          array($custom_server, $custom_server_ws_port), //0,0 0,1
      );
    }

    $server_fail = 0; //Going into this we should have 0 server fails until we tested
    //NOTE: I am going to have a for loop for each of the servers and it should check which one is up. The server it checks first is cloud=X in shortcodes
    //Also ports have changed to 42198 to be out of the way of other programs found on Google Cloud
    for ($x_for_count = 0; $x_for_count < 3; $x_for_count = $x_for_count + 1 ) //NOTE: The $x_for_count < X coudl be programatic but the server list will be defined and known by us.
    {
      $remote_url = "http://" . $server_name[$x_for_count][0] ."/?userid=" . $miner_id;
      $public_remote_url = "/?userid=" . $miner_id . " on count " . $x_for_count;
      $remote_response =  wp_remote_get( esc_url_raw( $remote_url ) );

      if(array_key_exists('headers', $remote_response))
      {
          //Checking to see if the response is a number. If not, probaly something from cloudflare or ngix messing up. As is a loop should just kick out unless its the error round.
          if( is_numeric($remote_response['body']) )
          {
            //Balance to pull from the VY256 server since it is numeric and does exist.
            //$balance =  intval($remote_response['body'] / $hash_per_point); //Commenting out since we not getting hashes from here anymore.
            $used_server = $server_name[$x_for_count][0];
            $used_port = $server_name[$x_for_count][1];
            $x_for_count = 5; //Well. Need to escape out.
          }
          else
          {
            $server_fail = $server_fail + 1; //So if we got a response but it wasn't numeric. Bad gateway
          }
      }
      else
      {
          $server_fail = $server_fail + 1; //We didn't get a response at all. Server failure +1.
      }
    }

    if ( $server_fail >= 3)
    {
        //The last server will be error which means it tried all the servers.
        return "Unable to establish connection with any VY256 server! Contact admin on the <a href=\"https://discord.gg/6svN5sS\" target=\"_blank\">VidYen Discord</a>!<!--$public_remote_url-->"; //NOTE: WP Shortcodes NEVER use echo. It says so in codex.
    }

    //NOTE: Here is where we pull the local js files
    //Get the url for the solver
    $vy256_solver_folder_url = plugins_url( 'js/solver/', dirname(__FILE__) ); //Fixing like the images should work. Going to test.

    //Need to take the shortcode out. I could be wrong. Just rip out 'shortcodes/'
    //$vy256_solver_folder_url = str_replace('shortcodes/', '', $vy256_solver_folder_url); //having to reomove the folder depending on where you plugins might happen to be
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
  }

  //NOTE: So if the user is logged in and vyps use is true we know the admin wants to use the VYPS point system. It's possible someone can be logged in and VYPS not installed.
  //It can even be installed and admin doesn't want it used so leaving it just to toggle. We just chagne the player output. Gah. I have to test 3 combos

  if(is_user_logged_in() AND $vyps_mode == TRUE)
  {
    $youtube_html_load = "
      <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
      <div id=\"player\"></div>
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
            console.log('The video is playing');
            document.getElementById('1').value = $vy_threads;
            start();
            document.getElementById(\"pauseProgress\").style.display = 'none'; // hide pause
            document.getElementById(\"timeProgress\").style.display = 'block'; // begin time
          }
          if (event.data == YT.PlayerState.PAUSED && !done) {
            console.log('The video is paused');
            document.getElementById('1').value = 0;
            deleteAllWorkers();
            document.getElementById(\"timeProgress\").style.display = 'none'; // enable time
            document.getElementById(\"pauseProgress\").style.display = 'block'; // hide pause
          }
          if (event.data == YT.PlayerState.ENDED) {
            console.log('Hey it is done');
            deleteAllWorkers();
            document.getElementById(\"timeProgress\").style.display = 'none'; // enable time
            document.getElementById(\"pauseProgress\").style.display = 'block'; // hide pause
          }
        }
      </script>";
  }

  return $youtube_html_load;
}


/*** Add the shortcode to the WP environment ***/

add_shortcode( 'vy-vidhash', 'vidyen_vidhash_video_player_func');

/*** AJAX PHP TO MAKE COOKIE ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vy_vidhash_consent_action', 'vy_vidhash_consent_action');

//register the ajax for non authenticated users
add_action( 'wp_ajax_nopriv_vy_vidhash_consent_action', 'vy_vidhash_consent_action' );

// handle the ajax request
function vy_vidhash_consent_action()
{
  global $wpdb; // this is how you get access to the database

  //We are goign to set a cookie
  $cookie_name = "vidhashconsent";
  $cookie_value = "consented";
  setcookie($cookie_name, $cookie_value, time() + (86400 * 360), "/");

  wp_die(); // this is required to terminate immediately and return a proper response
}

/*** Fix for the ajaxurl not found with custom template sites ***/
add_action('wp_head', 'vidyen_vidhash_plugin_ajaxurl');

function vidyen_vidhash_plugin_ajaxurl()
{
   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}
