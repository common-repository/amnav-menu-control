===  Menu Control for aMember ===

Contributors: jenolan
Donate link: https://paypal.me/jenolan/25usd
Tags: amember, menu, menus, nav menu, nav menus
Requires at least: 4.4.0
Requires aMember: 4.7.x
Tested up to: 4.5.0
Stable tag: 1.0.3
License: GPLv3

Hide custom menu items based on aMember products and logged in state.

== Description ==

This plugin lets you hide custom menu items based on aMember login status and product purchases.  So if you have a link in the menu that you only want to show to aMember logged in users, certain types of users, or even only to logged out users, this plugin is for you.


= Thanks =

A special thank you to [helgatheviking](https://profiles.wordpress.org/helgatheviking/) for the [Nav Menu Roles](https://wordpress.org/plugins/nav-menu-roles/) that this plugin is based on.

= IMPORTANT NOTE =

In WordPress menu items and pages are completely separate entities. aMember Menu Control does not restrict access to content. Nav Menu Roles is *only* for showing/hiding *nav menu* items. If you wish to restrict content then you need to also be using a membership plugin.

= Usage =

1. Go to Appearance > Menus
1. Edit the menu items accordingly.
1. If you choose 'Logged In' and don't check any boxes, the item will be visible to a;; logged in aMember users like normal.

= Support =

Support is handled in the [WordPress forums](https://wordpress.org/support/plugin/amnav-menu-control). Please note that support is limited and does not cover any custom implementation of the plugin. Before posting, please read the [FAQ](http://wordpress.org/plugins/amnav-menu-control/faq/). Also, please verify the problem with other plugins disabled and while using a default theme.

Please report any bugs, errors, warnings, code problems to [Github](https://github.com/JLogica/aMember.WP.Menu/issues)

== Installation ==

1. Upload the `plugin` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Appearance > Menus
1. Edit the menu items accordingly. First select whether you'd like to display the item to Everyone, all logged out users, or all logged in users.
1. Logged in users can be further limited to specific products by checking the boxes next to the products you'd like to restrict visibility to.

== Screenshots ==

1. Show the new options for the menu items in the admin menu customizer

== Frequently Asked Questions ==

= <a id="conflict"></a>I don't see the Nav Menu Control options in the admin menu items?  =

This is because you have another plugin (or theme) that is also trying to alter the same code that creates the Menu section in the admin.

WordPress does not have sufficient hooks in this area of the admin and until they do plugins are forced to replace everything via custom admin menu Walker, of which there can be only one. There's a [trac ticket](http://core.trac.wordpress.org/ticket/18584) for this, but it has been around a while.


= What happened to my menu controls on import/export? =

The Menu Control for aMember plugin stores 1 piece of post *meta* to every menu item/post.  This is exported just fine by the default Export tool.

However, the Import plugin only imports certain post meta for menu items. A custom Importer is available as a work around.

= How Do I Use the Custom Importer? =

1. Go to Tools>Export, choose to export All Content and download the Export file
1. Go to Tools>Import on your new site and perform your normal WordPress import
1. Return to Tools>Import and this time select the Nav Menu Control for aMember importer.
1. Use the same .xml file and perform a second import
1. No duplicate posts will be created but all menu post meta (including your Nav Menu Controls info) will be imported

== Changelog ==

= 1.0.3 =
* updated for WP v4.5

= 1.0.2 =
* remove redundant Wordpress from title
* Added icon/banner

= 1.0.1 =
* Wordpress review changes

= 1.0.0 =
* Initial release

== Upgrade Notice ==

None current

