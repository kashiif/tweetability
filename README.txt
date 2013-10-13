=== Tweetability ===
Contributors: kashiif
Tags: twitter, tweetable, tweet, share
Requires at least: 3.0
Tested up to: 3.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Tweetability lets you indicate, with a shortcode, tweetable text in your posts that your readers can tweet with a single click. As seen on nytimes.com

== Description ==

Let readers of your wordpress tweet the text with a single click. Inspired from a recent [New York Times article](http://www.nytimes.com/2013/08/25/arts/television/the-god-of-snl-will-see-you-now.html) doing the exact same thing. [Demo](http://htmlpreview.github.io/?https://github.com/dukerson/jquery.tweetable.js/blob/master/test.html)

See screenshots.

This plugin is based on jQuery plugin [Tweetable](https://github.com/dukerson/jquery.tweetable.js) by [Justin Duke](http://jmduke.net/)

= How To Use =
Simply place **[tweetability]**your content here**[/tweetability]** in your posts/pages. The text between [tweetability] shortcode would be wrapped in a special clickable block that user can click and it will open users' twitter page to tweet that text.

You can set the following values in options page or use the attribute to override the values from options page:

* **via**: A screen name to associate with the Tweet. The provided screen name will be appended to the end of the tweet with the text: "via @username".
* **related**: Suggest accounts related to the your content or intention by comma-separating a list of screen names. After Tweeting, the user will be encouraged to follow these accounts.
* **linkclass**: Name of additional css classes to add to twitter link. This can be used to change color, background color or hover color.
* **tooltip**: Tooltip text that appears when user hovers over the link.

= Example =

[tweetability **via**="kashiif" **related**="wordpress" **linkclass**="hilight" **tooltip**="Click here to tweet this instantaneously"]your content here[/tweetability]

== Installation ==

1. Copy the zip file to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==


== Screenshots ==

1. Tweetable text on your post.
2. Example use in post editor.
3. Plugin options page.

== Changelog ==

= 0.1.0 =
Initial version.

= 0.2.0 =
* Changes to make plugin compatible with wordpress as old as 3.0.0
* More optimized loading of files.

== Upgrade Notice ==

= 0.2.0 =
Several performance improvements.
