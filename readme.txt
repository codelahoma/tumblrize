=== Tumblrize ===
Author: Julien Ott and Meitar Moscovitz
Plugin URL: http://tumblrize.ijulien.com/
Tags: tumblr, post, posterous
Requires at least: 2.6
Tested up to: 2.8.5
Stable tag: 1.3.5

Tumblrize crossposts your published WordPress entries to Tumblr and Posterous. All you need is a Tumblr account. Changes you make to your WordPress posts are reflected in your Tumblr posts.

== Description ==

Tumblrize posts to Tumblr whenever you hit the "publish" button. It uses Tumblr's simple API to keep posts in sync; when you edit your WordPress post, it updates your Tumblr post.

Tumblrize is very lightweight. It just requires you to input your Tumblr email and password in the plugin's options screen. After that, you're ready to cross-post!

Other options allow posting to Posterous, sending additional metadata from your WordPress entry (notably tags), to Tumblr, and more.

== Installation ==

1. Download the plugin file.
1. Unzip the file into your 'wp-content/plugins/' directory.
1. Go to your WordPress administration panel and activate the plugin.
1. Go to Tumblrize Options (from the Settings menu) and provide your Tumblr login information.

== Frequently Asked Questions ==

= Can I specify tags? =

Yes. WordPress's tags are also crossposted to Tumblr. Be certain you have enabled the "Add post tags, too?" setting in the plugin's option screen.

= I checked the Posterous box for the first time, what happens next? =

It will either create a new Posterous blog if you hadn't one yet, and send exactly the same content to Tumblr and Posterous. Posterous will probably find it suspicious at first, asking you by email to confirm your post. Tumblrize will use your Tumblr email login for Posterous.

= Can send older Wordpress posts to Tumblr? =

Yes. Go edit the desired post, verify the crosspost option is set to YES, and update the post. Tumblrize will keep the original post date.

= What if I edit a post that has been tumblrized? =

If you edit or delete, changes will appear on Tumblr accordingly.

= Can I cross-post Private posts from WordPress to Tumblr? =

No. Currently Tumblrize only supports cross-posting public posts (i.e., posts with the status of <code>publish</code>). If you would like support for private posts, please [contact us](http://tumblrize.ijulien.com) to let us know, or indicate your feature request on [our issue tracker](http://github.com/meitar/tumblrize/issues/).

== Screenshots ==

1. The Tumblrize options screen.

2. The Tumblrize custom post editing box, allowing you to specify individual Tumblrize options on a per-post basis.

== Changelog ==

= 1.3.5 =
* 24/10/2009: v1.3.5 - Added Video Support. If you use Youtube's embed code in your Wordpress post body, it'll send the URL to Tumblr which will generate the code.

= 1.3.4 =
* 24/10/2009: v.1.3.4 - Scheduled posting is now supported. This effectively mimics the Tumblr queue feature.
** Fixed bug where deleting a post on WordPress did not delete the right post on Tumblr.
** Fixed bug where timezone differences resulted in incorrectly dated Tumblr post. Fixed by using UTC time for everything. Make sure both your Tumblr blog and your WordPress blog are using the same timezone.
** Improved error handling. Tumblrize will warn you of authentication failures, connection errors, and some other potential issues.
** Other minor bugfixes.

= 1.3.3 =
* 22/10/2009: v1.3.3 - Post date is taken into account. This will help older Wordpress posts not to be posted to Tumblr as new entries. To tumblrize an old post, go edit and update it with Tumblrize checked.

= 1.3.2 =
* 20/10/2009: v1.3.2 - New features:
** Link, Audio, and Quote Tumblr Post types support added.
** Tumblr public group support added. Tumblrize can now post to your non-default (public) Tumblr blog.
** Individual post option override added. Override default plugin settings on a per-post basis.
* Minor code cleanup.

= 1.3.1 =
* 19/10/2009: v1.3.1 - Improved options screen, significant code and database access enhancements.

= 1.3 =
* 18/10/2009: v1.3 - New features:
** Post editing. When you edit a WordPress post that has been previously cross-posted to Tumblr, Tumblrize will update Tumblr with the new information.
** Post deletion. When you delete a WordPress post that has been previously cross-posted to Tumblr, Tumblrize will delete that post from your Tumblr blog to keep both blogs up-to-date.
** Tag support added. WordPress post tags become Tumblr post tags.
** Photo post type support added. Tumblrize will search for the first instance of an `<img>` tag in your post if 'Photo' is selected as the Tumblr Post Type and use it as the photo source of the Tumblr post.
* Uninstallation support. Tumblrize will clean up after itself if you uninstall it, removing your Tumblr login credentials from your database. This improves your security.
* Significant code cleanup.

= 1.2.4 =
* 29/09/2009: v1.2.4 - Prevents double posting: if the post is updated, the update won't be sent. Switch off Tumblr but post on posterous. Added a tags option.

= 1.2.3 =
* 29/09/2009: v1.2.3 - You can now get notifications by email when Tumblrize has sucessfully posted. You can also add a link to the original article redirecting to your Wordpress blog, and turn off the plugin if needed.

= 1.2.2 =
* 28/09/2009: v1.2.2 - Added a checkbox to post also to Posterous. You may have to confirm your first post sent with that option.

= 1.2.1 =
* 27/09/2009: v1.2.1 - Added a shiny new Tumblrize logo.

= 1.2 =
* 26/09/2009: v1.2 - Tumblr email and password can be set from the settings panel.

= 1.1 =
* 25/09/2009: v1.1 - code cleanup, new posting methods

= 1.0 =
* 22/09/2009: v1.0 - initial release

== Other notes ==

Please go to http://log.ijulien.com/post/193997383/tumblrize for more details or help.