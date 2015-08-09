=== Extract Filters and Actions from Plugins ===
Contributors: n7studios,wpcube
Donate link: http://www.wpcube.co.uk/plugins/extract-filters-actions
Tags: extract,filters,actions,apply_filters,do_action
Requires at least: 3.6
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extracts all instances of apply_filters() and do_action() from any installed WordPress Plugin.

== Description ==

Extract Filters and Actions lets you choose a WordPress Plugin on your installation (whether active or inactive), and find all references to apply_filters() and
do_action() recursively, building output in either a HTML table or PHP array for you to then use in support documentation, personal reference etc.

Options include:

* Choose which installed WordPress Plugin to extract filters and actions from
* Only return filters and actions which start with a given prefix - useful if your Plugin has its own filters and actions, and you only want the output of those
* Choose to extract filters (i.e. apply_filters() function calls), actions (i.e. do_action() function calls) or both
* Output results in HTML or PHP array, which you can then copy and paste
* Output HTML will show in a table.

Example usage:
<a href="http://soliloquywp.com/docs/hooks-filters/" title="Soliloquy Hooks and Filters">Soliloquy - Hooks and Filters</a>

= Support =

We will do our best to provide support through the WordPress forums. However, please understand that this is a free plugin, 
so support will be limited. Please read this article on <a href="http://www.wpbeginner.com/beginners-guide/how-to-properly-ask-for-wordpress-support-and-get-it/">how to properly ask for WordPress support and get it</a>.

= WP Cube =
We produce free and premium WordPress Plugins that supercharge your site, by increasing user engagement, boost site visitor numbers
and keep your WordPress web sites secure.

Find out more about us at <a href="http://www.wpcube.co.uk" title="Premium WordPress Plugins">wpcube.co.uk</a>

== Installation ==

1. Either upload the Plugin ZIP file to Plugins > Add New > Upload Plugin, or FTP the extracted folder to your WordPress Plugins directory
2. Activate the Plugin through the Plugins menu in WordPress
3. Configure the Plugin through Plugins > Extract Filters and Actions in the WordPress Administration Menu

== Frequently Asked Questions ==

= Output appears incorrect / cut off? =
The Plugin assumes that your function calls are on a single line. For example:
`apply_filters( 'my_filter', $foo, $bar );`
`do_action( 'my_action' );`

Output will not be correctly generated if your function calls are on multiple lines. For example:
`apply_filters(' my_filter', $foo, array(
	'key' => 'value',
) );`

== Screenshots ==

1. Settings Screen
2. Settings Screen with Filters and Actions extracted from a WordPress Plugin 

== Changelog ==

= 1.0.2 =
* Added: WordPress 4.3 compatibility

= 1.0.1 =
* Added: Table CSS Classes option
* Fix: HTML Table for Actions wrongly had title = Filter Name. Changed to Action Name

= 1.0 =
* First release.

== Upgrade Notice ==
