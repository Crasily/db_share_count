<?php
/*
Plugin Name: DB Share Count
Plugin URI: https://github.com/Crasily/db_share_count
Description: Social share buttons with count
Version: 0.1
Author: Nathan Webb
License: GPLv2 or later
*/

include 'dbsc_admin_options.php';

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('init', 'dbsc_init');

function dbsc_init() {
  add_action( 'wp_enqueue_scripts', 'dbsc_ajax_enqueue_scripts' );
  add_shortcode( 'dbsc', 'dbsc_get_icons');
  add_action( 'wp_ajax_nopriv_dbsc_get_counts', 'dbsc_get_counts' );
  add_action( 'wp_ajax_dbsc_get_counts', 'dbsc_get_counts' );
}

function dbsc_get_min_count() {
  $options = get_option('dbsc_settings');
  $minCount = isset($options['min_count_display']) && is_int($options['min_count_display']) ? $options['min_count_display'] : 10;
  return intval($minCount);
}

function dbsc_get_icons() {
  echo dbsc_icons();
}

function dbsc_icons() {
  $counts = dbsc_get_counts(true);
  $minCount = dbsc_get_min_count();
  $dbsc_content = '<div class="dbsc_icons"><p>Like it? Share it&hellip;</p>';
  $dbsc_content .= dbsc_add_button('f', $counts, $minCount);
  $dbsc_content .= dbsc_add_button('p', $counts, $minCount);
  $dbsc_content .= dbsc_add_button('t', $counts, $minCount);
  $dbsc_content .= dbsc_add_button('g', $counts, $minCount);
  $dbsc_content .= dbsc_add_button('s', $counts, $minCount);
  $dbsc_content .= '</div>';
	return $dbsc_content;
}

function dbsc_ajax_enqueue_scripts() {
	wp_enqueue_style( 'dbsc', plugins_url( '/incl/dbsc_style.css', __FILE__ ) );
  wp_enqueue_script( 'dbsc', plugins_url( '/js/dbsc.js', __FILE__ ), array('jquery'), '1.0', true );
  wp_localize_script( 'dbsc', 'dbsc', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
    'post_id' => get_the_ID(),
    'min_count_display' => dbsc_get_min_count()
	));
}

function dbsc_add_button($site, $meta, $minCount = 10) {
  $buttonHtml = '<div class="dbsc_button">';
  switch($site) {
    case 'f':
    // facebook
     $buttonHtml .= '<a target="_blank" class="dbsc-icon dbsc-icon-facebook" href="http://www.facebook.com/sharer.php?u=' . get_permalink() . '"  rel="nofollow"></a>';
     break;
    case 's':
    // stumbleupon
      $buttonHtml .= '<a target="_blank" class="dbsc-icon dbsc-icon-stumbleupon" href="http://www.stumbleupon.com/submit?url='. get_permalink() . '&title=' . urlencode( get_the_title() ). '"></a>';
      break;
    case 'p':
    // pinterest
      $buttonHtml .= "<a class='dbsc-icon dbsc-icon-pinterest' href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;//assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());'></a>";
      break;
    case 't':
    // twitter
      $buttonHtml .= '<a target="_blank" class="dbsc-icon dbsc-icon-twitter" href="http://twitter.com/share?url=' . get_permalink() . '&text=' . urlencode( get_the_title() ) . '" rel="nofollow"></a>';
      break;
    case 'g':
    // google+
      $buttonHtml .= '<a target="_blank" class="dbsc-icon dbsc-icon-googleplus" href="https://plus.google.com/share?url=' . get_permalink() . '" rel="nofollow" ></a>';
      break;
    default:
      break;
  }
  $thisCount = $meta['counts'][$site];
  $styleToAdd = 'inherit';
  if((int) $thisCount < (int) $minCount) {
    $styleToAdd = 'none';
  }
  $buttonHtml .= '<div class="dbsc_count callout" style="display:' . $styleToAdd .';" id="dbsc_count_' . $site .'">'. $thisCount .'</div></div>';
  return $buttonHtml;
}

function dbsc_respondJson($metajson) {
  echo json_encode($metajson['counts']);
}

function dbsc_getCounts($url, &$countsDict) {
  # facebook
  $countUrl = "https://graph.facebook.com/?id=" . $url;
  $rawdata = file_get_contents($countUrl);
  $data = json_decode($rawdata, true);
  if(array_key_exists("shares", $data)) {
    $newCount = $data["shares"];
  } else {
    $newCount = 0;
  }
  // if they are the same, then they are probably duplicates, so don't
  // double count.
  if($newCount != $countsDict["f"]) {
    $countsDict["f"] += $newCount;
  }

  # twitter
  $urlTwitter = "http://public.newsharecounts.com/count.json?url=" . $url;
  $dTwitter = file_get_contents($urlTwitter);
  $data = json_decode($dTwitter, true);
  if(array_key_exists("count", $data)) {
    $newCount = $data["count"];
  } else {
    $newCount = 0;
  }
  if($newCount != $countsDict["t"]) {
    $countsDict["t"] += $newCount;
  }

  # google plus
  $post_body = '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]';
  $pst_data = array(
    "method" => 'POST',
    "headers" => array('Content-Type' => 'application/json'),
    "body" => $post_body
  );
  $dGoogle = wp_remote_request('https://clients6.google.com/rpc', $post_data);
  $data = json_decode($dGoogle['body'], true);
  $gcount = (isset($data[0]['result']['metadata']['globalCounts']['count'])) ?
    $json[0]['result']['metadata']['globalCounts']['count'] : 0;
  if($newCount != $countsDict["g"]) {
    $countsDict["g"] += $gcount;
  }

  # stumbleupon
  $urlStumble = "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=" . $url;
  $dStumble = file_get_contents($urlStumble);
  $data = json_decode($dStumble, true);
  if(array_key_exists("count", $data) and
       array_key_exists("views", $data["count"])) {
    $newCount = $data["count"]["views"];
  } else {
    $newCount = 0;
  }
  if($newCount != $countsDict["s"]) {
    $countsDict["s"] += $newCount;
  }

  # Pinterest
  $urlPinterest = "http://api.pinterest.com/v1/urls/count.json?url=" . $url;
  $dPinterest = file_get_contents($urlPinterest);
  $dPinterest = preg_replace("/receiveCount\(({.*})\)$/", "$1", $dPinterest);
  $data = json_decode($dPinterest, true);
  if(array_key_exists("count", $data)) {
    $newCount = $data["count"];
  } else {
    $newCount = 0;
  }
  if($newCount != $countsDict["p"]) {
    $countsDict["p"] += $newCount;
  }
}

function dbsc_get_counts($isPhp = false) {
  if($isPhp) {
    $post_url = get_the_ID();
    $meta = get_post_meta(get_the_ID(), 'dbsc_meta', true) ?: array(
      "createdAt" => 0,
    );
    return $meta;
  } else {
    $post_url = get_permalink($_GET['post_id']);
    $meta = get_post_meta($_GET['post_id'], 'dbsc_meta', true) ?: array(
      "createdAt" => 0,
    );
  }
  if ($meta['createdAt'] > (time() - 300)) {
    dbsc_respondJson($meta);
    die();
  }
  $countsDict = array(
    "f" => 0,
    "g" => 0,
    "l" => 0,
    "t" => 0,
    "s" => 0,
    "p" => 0
  );
  $endPart = preg_replace('/^(http[s]?)/', '', $post_url);
  $schemas = array('http', 'https');
  foreach ($schemas as $schema) {
    $fullUrl = $schema . $endPart;
    dbsc_getCounts($fullUrl, $countsDict);
  }
  $meta = array(
    'counts' => $countsDict,
    'createdAt' => time()
  );
  update_post_meta($_GET['post_id'], 'dbsc_meta', $meta);
  dbsc_respondJson($meta);
  die();
}


?>
