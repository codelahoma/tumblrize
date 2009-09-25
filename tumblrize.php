<?php
/*
Plugin Name: Tumblrize
Plugin URI: http://log.ijulien.com/post/193997383/tumblrize
Description: This plugin automatically sends your new posts to Tumblr. Please edit the plugin to change your login credentials.
Version: 1.1
Author: Julien Ott
Author URI: http://ijulien.com
*/


// User Info: PLEASE EDIT THE DATA BELOW
$tumblr_email    = "";
$tumblr_password = "";
// STOP EDITING


function tumblrize($post_ID)  {
    global $tumblr_email, $tumblr_password, $tags;

// DATA Preparation
	$post = get_post($post_ID);
	$post_type  = 'regular';
	$post_title = $post->post_title;
	$post_title = html_entity_decode($post->post_title);
	$post_body  = $post->post_content;
	$post_body  = html_entity_decode($post_body);
	$post_body = str_replace('\"',"",$post_body);
	$tags = "tumblrize";
	$generator = "Tumblrize";
    
// SEND Data

    if($tumblr_email && $tumblr_password){

		// Prepare POST request
		$request_data = http_build_query(
   		 array(
        	'email'     => $tumblr_email,
        	'password'  => $tumblr_password,
        	'type'      => $post_type,
        	'title'     => $post_title,
        	'body'      => $post_body,
        	'tags'      => $tags,
        	'generator' => $generator
   		 )
		);

		// Send the POST request (with cURL)
		$c = curl_init('http://www.tumblr.com/api/write');
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $request_data);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($c);
		$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		curl_close($c);
   
}
    
    return $post_ID;
}

add_action ( 'publish_post', 'tumblrize' );

?>