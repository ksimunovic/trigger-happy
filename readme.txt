=== Trigger Happy ===
Contributors: hotsource
Donate link: http://hotsource.io
Tags: visual scripting, actions, triggers
Requires at least: 4.6
Tested up to: 4.7
Requires PHP: 5.3
Stable tag: 4.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your plugins and automate your workflow using Trigger Happy - A visual Trigger and Action workflow tool for WordPress

== Description ==

Connect your plugins and automate your workflow.

Trigger Happy is a visual scripting tool for WordPress, allowing you to connect plugins and events together using a simple user interface.

Currently supports core WordPress functionality, plus WooCommerce and Ninja Forms

Some examples of what can be achieved using Trigger Happy:
* Sending emails when an event occurs (such as user registration)
* Create posts from the front-end using WP Forms
* Calculate discounts or fees on WooCommerce orders
* Create WooCommerce coupons when a user fills out a contact form
* Change Navigation menus when the user is logged in
* Add HTML to a WooCommerce page
* Modify the WordPress post titles and content before they are rendered

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/trigger-happy` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Start creating your Trigger Happy flow by clicking "Trigger Happy->Add New" in the dashboard
4. Select a trigger (for example: When an comment is created)
5. Select the actions you want to attach to this trigger (eg: Send an email, or Create a post)

== Upgrade Notice ==
Version 1.0.1 - Now with IFTTT WebHook support

== Frequently Asked Questions ==

= Can I perform calculations? =

Yes - calculations are fully supported. Use existing data (for example, post meta data or product prices) to calculate values for actions.

= Does this replace PHP coding? =

No. While it does allow you to add functionality to your site that would require coding without this plugin, it is not intended to replace coding completely.
What it intends to do is allow non-developers to tweak the functionality of their site without copying+pasting code.

= Do you support [Plugin name here]? =

We are adding support for plugins every week. The supported plugins are listed above.
If you need support for a particular plugin, let us know.

= Can I hook into core WordPress hooks such as init or pre_get_posts =

This will be possible in the next version. We are trying to figure out how to allow this while protecting non-developer users from breaking their sites.

= Can I export my flow to a PHP file or plugin? =

Not yet - but this is currently being worked on.

== Screenshots ==

1. An example of a flow redirecting to a URL once a form has been submitted
2. Flow showing a members-only WooCommerce discount for logged in users
3. Adding custom content to the bottom of all posts

== Changelog ==

= 1.0.1 =
* Added IFTTT WebHook Support

= 1.0 =
* First release


== Conditional execution ==

Conditions can be applied to flows (for example, "If User is Logged In"). These conditions control the execution of the "Flow" they're added to, allowing you to
create complex flows without having to write code.
