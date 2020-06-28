<?php

function dbsc_refresh_all_posts() {
  $args = array(
  	'sort_order' => 'asc',
  	'sort_column' => 'post_title',
  	'hierarchical' => 1,
  	'exclude' => '',
  	'include' => '',
  	'meta_key' => '',
  	'meta_value' => '',
  	'authors' => '',
  	'child_of' => 0,
  	'parent' => -1,
  	'exclude_tree' => '',
  	'number' => '',
  	'offset' => 0,
  	'post_type' => 'page',
  	'post_status' => 'publish'
  );
  $pages = get_pages($args);
  foreach ( $pages as $page ) {
    $url = get_permalink( $page->ID );
    dbsc_refresh_url($url, $meta);
  }
}

function dbsc_refresh_url($post_url) {
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
    dbsc_refresh_site_counts($fullUrl, $countsDict);
  }
  $meta = array(
    'counts' => $countsDict,
    'createdAt' => time()
  );
  update_post_meta($url, 'dbsc_meta', $meta);
  die();
}

function dbsc_refresh_site_counts($url, &$countsDict) {
  $func_map = array(
    "f" => "get_facebook_count",
    #"t" => "get_twitter_count",
    "g" => "get_gplus_count",
    "s" => "get_stumble_count",
    "p" => "get_pinterest_count"
  );

  foreach ($func_map as $site_code => $site_function) {
    add_count_for_site_unless_equal($countsDict, $url, $site_code, $site_function);
  }
}

function add_count_for_site_unless_equal(&$countsDict, $url, $site_code, $site_function) {
  $site_count = $site_function($url);
  if($site_count != $countsDict[$site_code]) {
    $countsDict[$site_code] += $site_count;
  }
}

function get_facebook_count($url) {
  $countUrl = "https://graph.facebook.com/?id=" . $url . "&fields=og_object{engagement}";
  $rawdata = file_get_contents($countUrl);
  $data = json_decode($rawdata, true);
  if(is_array($data) && array_key_exists("og_object", $data)) {
    $newCount = $data["og_object"]["engagement"]["count"];
  } else {
    $newCount = 0;
  }
  return $newCount;
}

function get_twitter_count($url) {
  $urlTwitter = "http://public.newsharecounts.com/count.json?url=" . $url;
  $dTwitter = file_get_contents($urlTwitter);
  $data = json_decode($dTwitter, true);
  if(is_array($data) && array_key_exists("count", $data)) {
    $newCount = $data["count"];
  } else {
    $newCount = 0;
  }
  return $newCount;
}

function get_gplus_count($url) {
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
    return $gcount;
}

function get_stumble_count($url) {
  $urlStumble = "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=" . $url;
  $dStumble = file_get_contents($urlStumble);
  $data = json_decode($dStumble, true);
  if(is_array($data) && array_key_exists("count", $data) &&
       is_array($data["count"]) && array_key_exists("views", $data["count"])) {
    $newCount = $data["count"]["views"];
  } else {
    $newCount = 0;
  }
  return $newCount;
}

function get_pinterest_count($url) {
  $urlPinterest = "https://widgets.pinterest.com/v1/urls/count.json?url=" . $url;
  $dPinterest = file_get_contents($urlPinterest);
  $dPinterest = preg_replace("/receiveCount\(({.*})\)$/", "$1", $dPinterest);
  $data = json_decode($dPinterest, true);
  if(is_array($data) && array_key_exists("count", $data)) {
    $newCount = $data["count"];
  } else {
    $newCount = 0;
  }
  return $newCount;
}

?>
