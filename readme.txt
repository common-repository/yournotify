=== Yournotify ===
Contributors: yournotify
Tags: email, newsletter, widget, yournotify, wordpress, form, plugin, subscribe, landing page
Requires at least: 4.6
Tested up to: 6.1
Stable tag: 1.1.0
License: GPLv3 or later

Capture your visitor's email address and subscribe them to your newsletter campaign with this simple Yournotify plugin!

== Description ==

This plugin registers a custom WordPress widget called **Yournotify**. This widget will allow you to connect to your Yournotify account with your API key and you will be able to select the list you want your visitors to subscribe to.

This widget can be used for all sorts of things, like: newsletter, lead capture, email sequence, and much more!

The Widget will output an email and telephone input field and a submit button, that's all you need to capture your visitor's email address and telephone. Main idea behind this plugin is that's easy and simple to use.

== External service ==

• This plugin uses external service (https://yournotify.com) via AJAX to pull your contact list, we use these data to populate your admin list selection for your newsletter subscribers.
• The link to the external service: https://yournotify.com
• The link to our data privacy policy page with information what we use your data for: https://yournotify.com/privacy-policy

== Installation ==

**From your WordPress dashboard**

1. Visit 'Plugins > Add New',
2. Search for 'Yournotify' and install the plugin,
3. Activate 'Yournotify' from your Plugins page.

**From WordPress.org**

1. Download 'Yournotify'.
2. Upload the 'yournotify' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
3. Activate 'Yournotify' from your Plugins page.

**Once the plugin is activated you will find the widget (Yournotify) in Appearance -> Widgets or in your page builder, if it supports widgets**

== Frequently Asked Questions ==

= How do I disable the default widget form styles? =

You can do that easily with a help of custom WP filter. Please add this code to your theme:

`add_filter( 'yournotify/disable_frontend_styles', '__return_true' );`

= How do I change the texts of the widget? =

You can change it with a help of custom WP filter. Please add this code to your theme and change the texts to your liking:

`
function yournotify_form_texts() {
    return array(
		'name'  => esc_html__( 'Your Name', 'yournotify-btn' ),
        'email'  => esc_html__( 'Your E-mail Address', 'yournotify-btn' ),
		'telephone'  => esc_html__( 'Your Telephone', 'yournotify-btn' ),
        'submit' => esc_html__( 'Subscribe!', 'yournotify-btn' ),
    );
}
add_filter( 'yournotify-btn/form_texts', 'yournotify_form_texts' );
`

== Screenshots ==

1. Widget settings
2. Widget frontend with basic design
3. Widget frontend with styled design

== Changelog ==

*Release Date - 23 May 2022*

* Initial release!
