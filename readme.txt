=== DB Share Count ===
Contributors: nathanwebb
Tags: social share count, social share, share counter, social icons
Requires at least: 4.0
Tested up to: 4.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Social share buttons with count

== Description ==
DB Social Share count has been designed to be a simple and effective social share counter. We needed a solution when migrating to https, as the old share counts weren't visible anymore. DB Social Share count solved this problem.

[See it in action at Go Science Girls](https://gosciencegirls.com.au/dirt-backyard-science-experiment/)

With DB Share Count, you can:

* add share buttons to your site
* display your social share count on each post
* only show share counts above some amount - so small shares don't look so bad!

= Features =

* Share with Facebook, Stumbleupon, Pinterest, Twitter and Google+
* Accurate - recover your share count by combining http and https share counts
* Fast - the results are cached so the share icons load straight away

DB Share Count is a free and open wordpress plugin. Twitter shares are provided
by the free service newsharecounts.com.

== Installation ==
You can either install this through the Wordpress admin page, or manually.

Via the Wordpress Admin page:

1. Navigate to "Plugins",
1. Click on "Add New"
1. Search for "db share count"
1. Click on "Install Now"
1. Click on "Activate Plugin"

To install manually:

1. Download the db_share_count.zip from [github](https://github.com/Crasily/db_share_count/archive/master.zip).
1. Extract the archive and upload the whole directory to your "/wp-content/plugins/" directory
1. Go to the Wordpress admin page
1. Go to "Plugins" and scroll down until you can see DB Share Count

Note for Twitter:
Since Twitter no longer provides access to your Twitter share count,
DB Share Count uses newsharecounts.com instead. If your Twitter share count
isn't showing up, then you may need to go to http://www.newsharecounts.com/
and enter your website's URL and sign in using your Twitter account to authorize
access. That's all!

= Activation =

There are two ways to use DB Share Count:

1. Add the `[dbsc]` shortcode to any page or post.
1. Make it part of your theme. Add the following code to any where in your theme: `<?php dbsc_get_icons(); ?>`


== Frequently Asked Questions ==
= Do you provide support? =
Sure! You can raise an issue on the [DB Share Count Github page](https://github.com/Crasily/db_share_count)

== Changelog ==
= 0.1.3 =
* Fixed options was not displaying correct min_count

= 0.1.2 =
* Fixed readme.txt incorrect shortcode
* Added extra error checking for bad responses

= 0.1.1 =
* Fixed facebook API
