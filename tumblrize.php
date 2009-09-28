<?php
/*
Plugin Name: Tumblrize
Plugin URI: http://log.ijulien.com/post/193997383/tumblrize
Description: This plugin automatically sends your new posts to Tumblr. Please update options with your credentials.
Version: 1.2.2
Author: Julien Ott
Author URI: http://ijulien.com
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

<h3>Username and Password</h3>

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
<th scope="row">Also send to Posterous?</th>
<td><input type="checkbox" name="tumblr_posterous" autocomplete="off" value="posterous" <?php if ( get_option('tumblr_posterous') == "posterous" ) { echo "checked"; }?> /></td>
</tr>
</table>

<p>We &hearts; <a href="http://www.tumblr.com/" title="ijulien" target="_blank">Tumblr</a>. Don't send spam. - <a href="http://ijulien.com/" title="ijulien" target="_blank">&infin;julien</a></p>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="tumblr_email,tumblr_password,tumblr_posterous" />

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
	$tags = "tumblrize";
	$generator = "Tumblrize";
    
// SEND Data

    if(get_option('tumblr_email') && get_option('tumblr_password') && $post_body){

		// Prepare POST request
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

		// Send the POST request (with cURL)
		$c = curl_init('http://www.tumblr.com/api/write');
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $request_data);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($c);
		$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		curl_close($c);

		// Notify Posterous if checked
		if ( get_option('tumblr_posterous') == "posterous" ) { $posterous_email = get_option('tumblr_email'); tumblrize_posterous($posterous_email, $post_title, $post_body, $tags); }
   
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


if(get_option('tumblr_email') == "")
{
	add_action('admin_notices', 'show_tr_warning');
}

add_action('admin_menu', 'tumblrize_menu');
add_action ( 'publish_post', 'tumblrize' );

?>