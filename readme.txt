=== PresenPress ===
Contributors: miyauchi
Donate link: http://wpist.me/
Tags: presentation, leapmotion, reveal.js
Requires at least: 3.6
Tested up to: 3.8
Stable tag: 0.2.4

Presentation with WordPress + Leap Motion.

== Description ==

http://www.youtube.com/watch?v=lZrNsrq18xk

* This plugin allows you to craete presentation.
* You are able to control these presentations by gestures of the Leap Motion.
* JavaScript for slide are supported by [reveal.js](https://github.com/hakimel/reveal.js/).

[This Plugin published on GitHub.](https://github.com/miya0001/presenpress)

= Some Feature =

* Allow you to create mutiple presentation.
* Custom style sheet support.
* Some themes alredy included.
* cube, page, linear and other transition support.
* Convert gallery shortcode to the presentation automatically.

= Supported Gestures =

* swipe left - Move to next slide.
* swipe right - Move to previous slide.
* point - normal pointer
* touch - Highlighted pointer

= How to create presentation =

* Click Add New link in Presenation on the WordPress admin menu.
* If you want to add next slide, please place &lt;!--nextpage--&gt; tag in the editor.

= How to add your custom theme =

* Create your theme CSS. See [documentation](https://github.com/hakimel/reveal.js/blob/master/css/theme/README.md).
* Please place the code like following in your functions.php or your custom plugin.

`<?php

add_filter('presenpress_themes', function($themes){

    $themes['your_theme_name'] = array(
        'url' => 'http://example.com/your_theme.css',
        'version' => '1.0.0'
    );

    return $themes;
});
`

= Translators =

* Turkish(tr_TR) - [Serkan Algur](http://wpadami.com/)
* Japanese(ja) - [Takayuki Miyauchi](http://firegoby.jp/)

== Installation ==

* A plug-in installation screen is displayed on the WordPress admin panel.
* It installs it in `wp-content/plugins`.
* The plug-in is made effective.

== Screenshots ==

1. Visual Editor
2. Presentation Settings
3. Presentation Example
4. Pointer
5. Highlighted pointer


