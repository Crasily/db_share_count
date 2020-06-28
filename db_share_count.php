<?php
/*
Plugin Name: DB Share Count
Plugin URI: https://github.com/Crasily/db_share_count
Description: Social share buttons with count
Version: 0.2.1
Author: Nathan Webb
License: GPLv2 or later
*/

include 'admin_options.php';

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('init', 'dbsc_init');

function dbsc_init() {
  add_action( 'wp_enqueue_scripts', 'dbsc_enqueue_scripts' );
  add_shortcode( 'dbsc', 'dbsc_call_shortcode');
}

function dbsc_enqueue_scripts() {
	wp_enqueue_style( 'dbsc', plugins_url( '/incl/dbsc_style.css', __FILE__ ) );
}

function dbsc_get_icons() {
  echo dbsc_icons();
}

function dbsc_icons() {
  $counts = dbsc_get_counts_for_php();
  $minCount = dbsc_get_min_count();
  $dbsc_content = '<div class="dbsc_icons"><p>Like it? Share it&hellip;</p>';
  $dbsc_content .= dbsc_add_button('f', $counts, $minCount);
  $dbsc_content .= dbsc_add_button('p', $counts, $minCount);
  $dbsc_content .= dbsc_add_button('t', $counts, $minCount);
  $dbsc_content .= '</div>';
	return $dbsc_content;
}

function dbsc_get_counts_for_php() {
  return get_post_meta(get_the_ID(), 'dbsc_meta', true) ?: array(
    "createdAt" => 0,
  );
}

function dbsc_get_min_count() {
  $options = get_option('dbsc_settings');
  $minCount = isset($options['min_count_display']) && is_int($options['min_count_display']) ? $options['min_count_display'] : 10;
  return intval($minCount);
}

function dbsc_add_button_facebook($meta, $minCount, $permalink) {
  $innerHtml = '<a target="_blank" class="dbsc-icon dbsc-icon-facebook" href="http://www.facebook.com/sharer.php?u=' . $permalink . '"  rel="nofollow"></a>';
  return dbsc_add_button('f', $innerHtml, $meta, $minCount);
}

function dbsc_add_button_stumbleupon($meta, $minCount, $permalink,$the_title) {
  $innerHtml = '<a target="_blank" class="dbsc-icon dbsc-icon-stumbleupon" href="http://www.stumbleupon.com/submit?url='. $permalink . '&title=' . urlencode( $the_title ). '"></a>';
  return dbsc_add_button('s', $innerHtml, $meta, $minCount);
}

function dbsc_add_button_pinterest($meta, $minCount, $permalink) {
  $innerHtml = "<a class='dbsc-icon dbsc-icon-pinterest' href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;//assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());'></a>";
  return dbsc_add_button('p', $innerHtml, $meta, $minCount);
}

function dbsc_add_button_twitter($meta, $minCount, $permalink, $the_title) {
  $innerHtml = '<a target="_blank" class="dbsc-icon dbsc-icon-twitter" href="http://twitter.com/share?url=' . $permalink . '&text=' . urlencode( $the_title ) . '" rel="nofollow"></a>';
  return dbsc_add_button('t', $innerHtml, $meta, $minCount);
}

function dbsc_add_button_gplus($meta, $minCount, $permalink) {
  $innerHtml = '<a target="_blank" class="dbsc-icon dbsc-icon-googleplus" href="https://plus.google.com/share?url=' . $permalink . '" rel="nofollow" ></a>';
  return dbsc_add_button('g', $innerHtml, $meta, $minCount);
}

function dbsc_add_button($site_code, $innerHTML, $meta, $minCount = 10) {
  $buttonHtml = '<div class="dbsc_button">';
  $buttonHtml .= $innerHTML;
  $styleToAdd = 'inherit';
  $thisCount = $meta['counts'][$site_code];
  if((int) $thisCount < (int) $minCount) {
    $styleToAdd = 'none';
  }
  $buttonHtml .= '<div class="dbsc_count callout" style="display:' . $styleToAdd .';" id="dbsc_count_' . $site_code .'">'. $thisCount .'</div></div>';
  return $buttonHtml;
}

function dbsc_respondJson($metajson) {
  echo json_encode($metajson['counts']);
}

?>
