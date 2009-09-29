<?php
/*
Plugin Name: Tumblrize
Plugin URI: http://log.ijulien.com/post/193997383/tumblrize
Description: This plugin automatically sends your new posts to your Tumblr blog.
Version: 1.2.3
Author: Julien Ott
Author URI: http://ijulien.com
*/

/*  Copyright 2009 Julien Ott

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Please DON'T use Tumblrize for SPAM.

// To configure Tumblrize, go to the plugin options of your admin panel.

global $tusername, $tpassword, $tags, $post;
$tusername = get_option('tumblr_email');
$tpassword = get_option('tumblr_password');

function tumblrize_menu() {
  add_options_page('Tumblrize Options', 'Tumblrize Options', 8, __FILE__, 'tumblrize_options');
}


function show_tr_warning()
{
	echo "<div class=\"error\"><p>Tumblrize not configured. Please update your <a href=\"".get_bloginfo('wpurl')."/wp-admin/options-general.php?page=tumblrize/tumblrize.php\">Tumblr email and password</a>.</p></div>";
}

function tumblrize_options() {
?>
<div class="wrap">
<br />
<img src="http://id.ijulien.com/img/tumblrize.png" alt="Tumblrize" />

<form method="post" action="options.php">

<?php wp_nonce_field('update-options'); ?>

<h3>Options</h3>

<p>Enter your Tumblr email and password to enable Tumblrize to post updates.</p>

<table class="form-table">
<tr valign="top">
<th scope="row">Tumblr Email</th>
<td><input type="text" name="tumblr_email" autocomplete="off" value="<?php echo get_option('tumblr_email'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Tumblr Password</th>
<td><input type="password" name="tumblr_password" autocomplete="off" value="<?php echo get_option('tumblr_password'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Also send to <a href="http://www.posterous.com/" title="Posterous" target="_blank">Posterous</a>?</th>
<td><input type="checkbox" name="tumblr_posterous" autocomplete="off" value="posterous" <?php if ( get_option('tumblr_posterous') == "posterous" ) { echo "checked"; }?> /></td>
</tr>

<tr valign="top">
<th scope="row">Also send an email to <strong><?php if ( get_option('tumblr_email') ) { echo get_option('tumblr_email'); } else { echo "me"; } ?></strong>?</th>
<td><input type="checkbox" name="notify_me" autocomplete="off" value="notify_on" <?php if ( get_option('notify_me') == "notify_on" ) { echo "checked"; }?> /></td>
</tr>

<tr valign="top">
<th scope="row">Also add link to the original article?</th>
<td><input type="checkbox" name="add_permalink" autocomplete="off" value="add_permalink_on" <?php if ( get_option('add_permalink') == "add_permalink_on" ) { echo "checked"; }?> /></td>
</tr>

<tr valign="top">
<th scope="row"><strong>Turn Off Tumblrize</strong></th>
<td><input type="checkbox" name="shut_tumblrize" autocomplete="off" value="shut_tumblrize_on" <?php if ( get_option('shut_tumblrize') == "shut_tumblrize_on" ) { echo "checked"; }?> /></td>
</tr>
</table>

<p>We &hearts; <a href="http://www.tumblr.com/" title="Tumblr" target="_blank">Tumblr</a>. Don't send spam. - <a href="http://ijulien.com/" title="ijulien" target="_blank">&infin;julien</a></p>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="tumblr_email,tumblr_password,tumblr_posterous,notify_me,add_permalink,shut_tumblrize" />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>

<?php
}

function tumblrize($post_ID)  {

// DATA Preparation

	$post = get_post($post_ID);
	$post_type  = 'regular';
	$post_title = $post->post_title;
	$post_title = html_entity_decode($post->post_title);
	$post_body  = $post->post_content;
	$post_body  = html_entity_decode($post_body);
	$post_body = str_replace('\"',"",$post_body);
	if ( the_category(', ') ) { $tags = the_category(', '); $tags .= ", tumblrize"; } else { $tags = "tumblrize"; }
	$generator = "Tumblrize";
	$postlink = get_permalink($post_ID);

// Add Data

	if ( get_option('add_permalink') ) { $post_body .="<br /><a href=\"".$postlink."\" title=\"Original Article\">Original Article</a>" ; }
    
// SEND Data

    if(get_option('tumblr_email') && get_option('tumblr_password') && $post_body && get_option('shut_tumblrize') == ""){

		// Prepare DATA
		$request_data = http_build_query(
   		 array(
        	'email'     => get_option('tumblr_email'),
        	'password'  => get_option('tumblr_password'),
        	'type'      => $post_type,
        	'title'     => $post_title,
        	'body'      => $post_body,
        	'tags'      => $tags,
        	'generator' => $generator
   		 )
		);

		// Notify TUMBLR
		$c = curl_init('http://www.tumblr.com/api/write');
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $request_data);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($c);
		$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		curl_close($c);
		// Notify POSTEROUS
		if ( get_option('tumblr_posterous') == "posterous" ) { $posterous_email = get_option('tumblr_email'); tumblrize_posterous($posterous_email, $post_title, $post_body, $tags); }
		// Notify USER
   		if ( get_option('notify_me') == "notify_on" ) { $notify_email = get_option('tumblr_email'); tumblrize_notify($notify_email, $post_title, $post_body); }

}
    
    return $post_ID;
}

function tumblrize_posterous($posterous_sender, $posterous_title, $posterous_body, $posterous_tags)  {
    $recipient = 'posterous@posterous.com';
    $subject = $posterous_title.'((tag: '.$posterous_tags.'))';
    $body = $posterous_body;
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: " . $posterous_sender . "\n" . "Return-Path: " . $posterous_sender . "\n" . "Reply-To: " . $posterous_sender . "\n";
	mail($recipient, $subject, $body, $headers);
    return $post_ID;
}

function tumblrize_notify($notify_destination, $notify_title, $notify_body)  {
    $recipient = $notify_destination;
    $subject = 'Tumblrize: '.$notify_title;
    $body = $notify_body."<br /><br />Posted with<br /><img src=\"http://id.ijulien.com/img/tumblrize.png\" alt=\"Tumblrize\" />";
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: Tumblrize <tumblrize@ijulien.com>"."\n"."Return-Path: Tumblrize <tumblrize@ijulien.com>"."\n"."Reply-To: Tumblrize <tumblrize@ijulien.com>"."\n";
	mail($recipient, $subject, $body, $headers);
    return $post_ID;
}


if(get_option('tumblr_email') == "")
{
	add_action('admin_notices', 'show_tr_warning');
}

add_action('admin_menu', 'tumblrize_menu');
add_action ( 'publish_post', 'tumblrize' );

?>