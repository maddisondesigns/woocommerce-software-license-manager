=== WooCommerce Software License Manager ===
Contributors: ahortin, goback2
Tags: wc, wc license, wc software license, software license, software license manager, woocommerce, wc licensing
Requires at least: 3.5.1
Tested up to: 4.8.2
Stable tag: 2.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Seamless integration between WooCommerce and the [Software License Manager](https://wordpress.org/plugins/software-license-manager/) plugin.

== Description ==
Seamless integration between WooCommerce and the Software License Manager Plugin. Originally adopted from EDD Software License Manager, thanks to flowdee (coder@flowdee.de).

= Features =

* Automatically creates license keys for each sale with WC
* Licensing is optional and can be activated/deactivated individually
* Send generated license keys to your customers within your existing email notifications

**Please Note:** This is a fork of the [WooCommerce Software License Manager](https://wordpress.org/plugins/wc-software-license-manager/) plugin from the WordPress.org Plugin Directory, which hasn't been updated since July 2016, and no longer works with the latest version of WordPress and WooCommerce.

Significant code updates and tidying have been performed and this version now works with WordPress 4.8.2 and WooCommerce 3.2.


= Theme & Plugin Integration =
Please Note: The license validation part for your distributed plugins and themes is not part of this plugin. More on this can be found in the [Software License Manager documentation](https://www.tipsandtricks-hq.com/software-license-manager-plugin-for-wordpress).

To implement License Key validation in your plugin or theme, please check out the sample code in [Maddison Designs' Github repo](https://github.com/maddisondesigns/woocommerce-software-license-manager-client-theme).

> **Attention**
> **Known incompatibility issue with iThemes Security**
> If you have installed "iThemes Security", uncheck **Long URL Strings** where the Software License Manager plugin is installed


= Credits =

* [Woocommerce](https://wordpress.org/plugins/woocommerce/)
* [Software License Manager](https://wordpress.org/plugins/software-license-manager/)
* [EDD Software License Manager](https://wordpress.org/plugins/edd-software-license-manager/)
* [WP 4.8.2 and WC 3.2.0 Compatibility](https://maddisondesigns.com)

= Translates =

* Persian
* Spanish (Spain) [Art Project Group](http://www.artprojectgroup.es/)

== Installation ==

The installation and configuration of the plugin is as simple as it can be.

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'wc software license manager'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select plugin zip file from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

== Configuration ==
The plugin is really simple and well structured so you don’t have to prepare a lot in order to get it working.

1. After the successful installation you will find a prepared options page here: “WooCommerce” > “Settings” > “Products” > “License Manager”
2. Enter your Software License Manager API credentials
3. Go to your Products and activate licensing where it's required

== Frequently Asked Questions ==

= Is it necessary to install both plugins on the same WordPress installation? =
No! That's one of the biggest benefits of this integration. WooCommerce and the Software License Manager can be installed on different sites.

= Can I use this plugin to validate the generated license keys? =
No! The license validation part for your distributed plugins and themes is not part of this plugin. Therefore please take a look into the Software License Manager documentation.

== Screenshots ==

1. Configuration screen
2. Activating licensing for a download
3. Output the generated license keys within your emails

== Changelog ==

= 2.0.1 =
- Updated License table styles so it matches the Order table.
- Updated hook to insert License details after Order details rather than before

= 2.0.0 =
- Updated plugin to work with WordPress 4.8.2 and WooCommerce 3.2
- Fixed error from WC Order Properties being accessed directly on My Account Order View page and when generating order email
- Added functionality to pass Product Ref to Software License Manager API when generating key
- Added ability to enable/disable debug logging messages
- Added logging messages throughout code which are only displayed when logging is enabled
- Added settings option for Secret Key for Verification to enable viewing License Key Registered Domains on Order View page
- Added the display of Registered Domains for each License Key on the WooCommerce Order View page
- Removed code that wasn't being used
- Removed superfluous comments and added in lots of extra comments where needed
- Reformatted and tidied code to WordPress Plugin Directory specifications
- Reworded meta box labels and added input field descriptions on add/edit product page
- Updated Version to 2.0.0

= 1.0.7 (20th July 2016) =
- Add Expire Date to Email and Purchase Details (Thanks to Albert Van Der Ploeg)
- txn_id change from $product_id to $order_id

= 1.0.6 (8th November 2015) =
- Small fix

= 1.0.5 (7th November 2015) =
- `has_downloadable_item` and `$product->has_file()` removed from code, so downloadable tick is enough for working

= 1.0.4 (1st November 2015) =
- Small fix
- Spanish translate added

= 1.0.3 (6th October 2015) =
- License details get from billing form now
- License renewal can be set in product page
- License details added to user account page

= 1.0.1 (5th October 2015) =
- Sample code was added
- Small fix


== Upgrade Notice ==
