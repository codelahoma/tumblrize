<?php
/*
Plugin Name: Tumblrize
Plugin URI: http://log.ijulien.com/post/193997383/tumblrize
Description: Automatically crossposts to your Tumblr blog when you publish a post on your WordPress blog.
Version: 1.3
Author: Julien Ott and <a href="http://maymay.net/">Meitar Moscovitz</a>
Author URI: http://ijulien.com
*/

/*  Copyright 2009 Julien Ott and Meitar Moscovitz

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

// DON'T use Tumblrize for SPAM.

if (!defined('TUMBLRIZE_PLUGIN_VERISON')) {
    define('TUMBLRIZE_PLUGIN_VERSION', '1.3');
} else { die('A constant by the name of TUMBLRIZE_PLUGIN_VERSION has already been defined.'); }

// Load helper functions.
require_once(dirname(__FILE__) . '/helperlib.php');

// To configure Tumblrize, go to the plugin options of your admin panel.

global $tusername, $tpassword, $tags, $post;
$tusername = get_option('tumblrize_tumblr_email');
$tpassword = get_option('tumblrize_tumblr_password');

function tumblrize_menu() {
  add_options_page('Tumblrize', 'Tumblrize', 8, __FILE__, 'tumblrize_options');
}

/**
 * function tumblrize_add_custom_box
 * Adds a customized box to the Write Post screen.
 */
function tumblrize_add_custom_box () {
    add_meta_box('tumblrize', 'Tumblrize', 'tumblrize_custom_box', 'post', 'advanced');
}
add_action('admin_menu', 'tumblrize_add_custom_box');
add_action('save_post', 'tumblrize_save_post');

/**
 * function tumblrize_custom_box
 * Prints the actual custom box for the plugin.
 */
function tumblrize_custom_box () {
    global $post;

    // Use nonce for verification
    print '<input type="hidden" name="tumblrize_nonce" id="tumblrize_nonce" value="' . 
            wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

    // The actual fields for data entry
    $tumblr_post_type = get_post_meta($post->ID, 'tumblrize_post-type', true);
?>
<label for="tumblrize_post-type">Tumblr Post Type</label>
<select id="tumblrize_post-type"
        name="tumblrize_post-type"
        <?php if ($tumblr_post_type) : print 'disabled="disabled"';endif;?>>
    <optgroup label="Select">
        <option value="regular" <?php if ('regular' === $tumblr_post_type) : print 'selected="selected"';endif;?>>Text</option>
        <option value="photo" <?php if ('photo' === $tumblr_post_type) : print 'selected="selected"';endif;?>>Photo</option>
    </optgroup>
    <optgroup label="Not Implemented Yet (do not use these)">
        <option value="quote">Quote</option>
        <option value="link">Link</option>
        <option value="conversation">Conversation</option>
        <option value="video">Video</option>
        <option value="audio">Audio</option>
    </optgroup>
</select>
<?php
} // END function tumblrize_custom_box

/**
 * Saves data from custom box on WordPress Write Post screen to database.
 */
function tumblrize_save_post ($post_id) {
    // Verify this came from our screen appropriately.
    if ( !wp_verify_nonce($_POST['tumblrize_nonce'], plugin_basename(__FILE__) )) {
        return $post_id;
    }

    // Verify we're not doing an auto save routine.
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) { return $post_id; }

    // Check permissions
    if ( 'post' == $_POST['post_type'] ) {
        if ( !current_user_can('edit_post', $post_id) ) { return $post_id; }
    }

    // Okay, we've authenticated correctly. Find and save the data.
    add_post_meta($post_id, 'tumblrize_post-type', $_POST['tumblrize_post-type'], true);
}

/**
 * function tumblrize_options
 * Provides plugin-wide options screen.
 */
function tumblrize_options () {
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
<td><input type="text" name="tumblrize_tumblr_email" autocomplete="off" value="<?php echo get_option('tumblrize_tumblr_email'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Tumblr Password</th>
<td><input type="password" name="tumblrize_tumblr_password" autocomplete="off" value="<?php echo get_option('tumblrize_tumblr_password'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Also send to <a href="http://www.posterous.com/" title="Posterous" target="_blank">Posterous</a>?</th>
<td><input type="checkbox" name="tumblrize_tumblr_posterous" autocomplete="off" value="posterous" <?php if ( get_option('tumblrize_tumblr_posterous') == "posterous" ) { echo "checked"; }?> /></td>
</tr>

<tr valign="top">
<th scope="row">Also send an email to <strong><?php if ( get_option('tumblrize_tumblr_email') ) { echo get_option('tumblrize_tumblr_email'); } else { echo "me"; } ?></strong>?</th>
<td><input type="checkbox" name="tumblrize_notify_me" autocomplete="off" value="notify_on" <?php if ( get_option('tumblrize_notify_me') == "notify_on" ) { echo "checked"; }?> /></td>
</tr>

<tr valign="top">
<th scope="row">Also add link to the original article?</th>
<td><input type="checkbox" name="tumblrize_add_permalink" autocomplete="off" value="tumblrize_add_permalink_on" <?php if ( get_option('tumblrize_add_permalink') == "tumblrize_add_permalink_on" ) { echo "checked"; }?> /></td>
</tr>

<tr valign="top">
<th scope="row">Add tags?<br>(comma-separated)</th>
<td><input type="text" name="tumblrize_tags" autocomplete="off" value="<?php echo get_option('tumblrize_tags'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Add post tags, too?<br>(adds individual post tags in addition to tags always added, above)</th>
<td><input type="checkbox" name="tumblrize_add_post_tags" autocomplete="off" value="tumblrize_add_post_tags" <?php if ( get_option('tumblrize_add_post_tags') == "tumblrize_add_post_tags" ) { echo "checked"; }?> /></td>
</tr>

<tr valign="top">
<th scope="row"><strong>Turn Off Tumblrize</strong><br>(will still post to Posterous if checked)</th>
<td><input type="checkbox" name="tumblrize_shutoff" autocomplete="off" value="tumblrize_shutoff_on" <?php if ( get_option('tumblrize_shutoff') == "tumblrize_shutoff_on" ) { echo "checked"; }?> /></td>
</tr>

<tr valign="top">
<th scope="row">Purge Tumblrize options on uninstall?<br>(deletes saved options for this plugin from database when you uninstall this plugin)</th>
<td><input type="checkbox" name="tumblrize_purge_database" autocomplete="off" value="tumblrize_purge_database" <?php if ( get_option('tumblrize_purge_database', 'tumblrize_purge_database') == "tumblrize_purge_database" ) { echo "checked"; }?> /></td>
</tr>

</table>

<p>We &hearts; <a href="http://www.tumblr.com/" title="Tumblr" target="_blank">Tumblr</a>. Don't send spam. &mdash; <a href="http://ijulien.com/" title="ijulien" target="_blank">&infin;julien</a> &amp; <a href="http://maymay.net/">Meitar</a></p>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="tumblrize_tumblr_email,tumblrize_tumblr_password,tumblrize_tumblr_posterous,tumblrize_notify_me,tumblrize_add_permalink,tumblrize_shutoff,tumblrize_tags,tumblrize_add_post_tags,tumblrize_purge_database" />

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
    $post_type  = get_post_meta($post_ID, 'tumblrize_post-type', true);
    $post_title = $post->post_title;
    $post_title = html_entity_decode($post->post_title);
    $post_body  = $post->post_content;
    $post_body  = html_entity_decode($post_body);
    $post_body = str_replace('\"',"",$post_body);
    if ( get_option('tumblrize_tags') ) { $tags = get_option('tumblrize_tags'); $tags .= ", tumblrize"; } else { $tags = "tumblrize"; }
    if ( get_option('tumblrize_add_post_tags') ) {
        query_posts("p=$post_ID");
        if (have_posts()) : while (have_posts()) : the_post();
            $more_tags = get_the_tags();
        endwhile; endif;
        if ($more_tags) {
            foreach ($more_tags as $another_tag) {
                $tags .= ",{$another_tag->name}";
            }
        }
    }
    $generator = "Tumblrize";
    $postlink = get_permalink($post_ID);

    // Gather and verify Tumblr post types have valid data.

    if ('photo' === $post_type) {
        $pattern = '/<img.*?src="(.*?)".*?\/?>/';
        $matches = array();
        preg_match($pattern, $post_body, $matches);
        $photo_source = $matches[1];

        $post_body = strip_only($post_body, '<img>');
    }

    switch ($post_type) {
        case 'photo':
            if (!isset($photo_source) || empty($photo_source)) {
                return $post_ID; // will forego posting a photo; silently fails
            }
            break;
        case 'text':
        case 'regular':
        default:
            if (empty($post_title) || empty($post_body)) {
                return $post_ID; // just bail out now
            }
    }

// Add Data

    if ( get_option('tumblrize_add_permalink') ) { $post_body .="<br /><a href=\"".$postlink."\" title=\"Original Article\">Original Article</a>" ; }

// SEND Data

    if (get_option('tumblrize_tumblr_email') &&
        get_option('tumblrize_tumblr_password') && $post_body) {

        // Prepare DATA
        $request_data = array();
        $request_data['email']     = get_option('tumblrize_tumblr_email');
        $request_data['password']  = get_option('tumblrize_tumblr_password');
        $request_data['post-id']   = get_post_meta($post_ID, 'tumblrize_post-id', true);
        if (empty($request_data['post-id'])) { unset($request_data['post-id']); } // no need
        $request_data['type']      = $post_type;
        $request_data['tags']      = $tags;
        $request_data['generator'] = $generator;

        switch ($post_type) {
            case 'photo':
                $request_data['source']  = $photo_source;
                $request_data['caption'] = $post_body;
                //$request_data['click-through-url'] = $photo_source;
                break;
            case 'text':
            case 'regular':
            default:
                $request_data['title'] = $post_title;
                $request_data['body']  = $post_body;
                break;
        }

        $request_data = http_build_query($request_data);

        // What's notified?
        if(get_option('tumblrize_shutoff' ) == ""){ $tb_services = "Tumblr"; }
        if(get_option('tumblrize_tumblr_posterous') == "posterous"){ $tb_services .= " and Posterous"; }
        if(get_option('tumblrize_shutoff' ) && get_option('tumblrize_tumblr_posterous') == "posterous"){ $tb_services = "Posterous"; }
        // Notify TUMBLR if checked
        if(get_option('tumblrize_shutoff') == ""){
            $c = curl_init('http://www.tumblr.com/api/write');
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, $request_data);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($c);
            $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
            curl_close($c);
            if ($status == 201) {
                add_post_meta($post_ID, 'tumblrize_post-id', $result, true);
            }
        }
        // Notify POSTEROUS if checked
        if ( get_option('tumblrize_tumblr_posterous') == "posterous" ) { $posterous_email = get_option('tumblrize_tumblr_email'); tumblrize_posterous($posterous_email, $post_title, $post_body, $tags); }
        // Notify USER if checked
        if ( get_option('tumblrize_notify_me') == "notify_on" ) { $notify_email = get_option('tumblrize_tumblr_email'); tumblrize_notify($notify_email, $post_title, $post_body, $tb_services); }
    }

    return $post_ID;
}

// Send to Posterous
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

// Send to Author
function tumblrize_notify($notify_destination, $notify_title, $notify_body, $notify_dest)  {
    $recipient = $notify_destination;
    $subject = 'Tumblrize: '.$notify_title;
    $body = "This article was posted to ".$notify_dest.".<br /><br />".$notify_body."<br /><br />from <img src=\"http://id.ijulien.com/img/tumblrize-small.png\" alt=\"Tumblrize\" /> with &hearts;<br />";
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: Tumblrize <tumblrize@ijulien.com>"."\n"."Return-Path: Tumblrize <tumblrize@ijulien.com>"."\n"."Reply-To: Tumblrize <tumblrize@ijulien.com>"."\n";
    mail($recipient, $subject, $body, $headers);
    return $post_ID;
}

if(get_option('tumblrize_tumblr_email') == "")
{
    add_action('admin_notices', 'show_tr_warning');
}

/**
 * function show_tr_warning
 * Alerts user of incomplete settings if no Tumblr email address is provided.
 */
function show_tr_warning ()
{
    echo "<div class=\"error\"><p>Tumblrize is not configured. Please provide your <a href=\"".get_bloginfo('wpurl')."/wp-admin/options-general.php?page=tumblrize/tumblrize.php\">Tumblr email and password</a>.</p></div>";
}

/**
 * Performs programmatic activation/upgrade from prior version actions.
 */
function tumblrize_activate () {
    if (TUMBLRIZE_PLUGIN_VERSION != '1.3') {
        // These were the pre-v1.3 option names, and should be moved.
        add_option('tumblrize_add_permalink', get_option('add_permalink'));
        delete_option('add_permalink');

        add_option('tumblrize_notify_me', get_option('notify_me'));
        delete_option('notify_me');

        add_option('tumblrize_shutoff', get_option('shut_tumblrize'));
        delete_option('shut_tumblrize');

        add_option('tumblrize_tumblr_email', get_option('tumblr_email'));
        delete_option('tumblr_email');

        add_option('tumblrize_tumblr_password', get_option('tumblr_password'));
        delete_option('tumblr_password');

        add_option('tumblrize_tumblr_posterous', get_option('tumblr_posterous'));
        delete_option('tumblr_posterous');
    }
}
register_activation_hook(__FILE__, 'tumblrize_activate');

/**
 * Deletes a post from Tumblr when it is deleted from WordPress.
 *
 * @return void
 */
function tumblrize_delete_post ($post_ID) {
    $r = tumblrize_to_tumblr('delete',
                             http_build_query(array(
                                 'email' => get_option('tumblrize_tumblr_email'),
                                 'password' => get_option('tumblrize_tumblr_password'),
                                 'post-id' => get_post_meta($post_ID, 'tumblrize_post-id', true),
                                 'generator' => 'Tumblrize'
                            )));
    return;
}

/**
 * Performs a single API method call to Tumblr's API endpoint.
 *
 * @param string $do The API endpoint to call.
 * @param string $data The URL-encoded request data to send.
 * @return mixed Array containing cURL response status and result if successful, false otherwise.
 */
function tumblrize_to_tumblr ($do, $data) {

    // Verify API URI endpoints
    switch ($do) {
        case 'write':
        case 'delete':
        case 'authenticate':
        case 'read':
            continue;
        default:
            return false;
    }

    // Notify TUMBLR if checked
    if (get_option('tumblrize_shutoff') == '' && $data) {
        $c = curl_init("http://www.tumblr.com/api/$do");
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        $r['result'] = $result;
        $r['status'] = $status;
        return $r;
    } else {
        // Posting to Tumblr is disabled.
        return false;
    }
}

add_action('admin_menu', 'tumblrize_menu');
add_action('publish_post', 'tumblrize');
add_action('delete_post', 'tumblrize_delete_post');
?>
