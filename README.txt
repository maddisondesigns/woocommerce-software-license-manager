=== Woocommerce Software License Manager ===
Contributors: goback2, ahortin
Tags: wc, wc license, wc software license, software license, software license manager, woocommerce, wc licensing
Requires at least: 3.5.1
Tested up to: 4.8.2
Stable tag: 2.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Seamless integration between WooCommerce and the [Software License Manager](https://wordpress.org/plugins/software-license-manager/) plugin.

== Description ==
Seamless integration between WooCommerce and the Software License Manager Plugin. Adopted from EDD Software License Manager, thanks to flowdee (coder@flowdee.de).

= Features =

* Automatically creates license keys for each sale with WC
* Licensing is optional and can be activated/deactivated individually
* Send generated license keys to your customers within your existing email notifications

**Please Note:** This is a fork of the [Woocommerce Software License Manager](https://wordpress.org/plugins/wc-software-license-manager/) plugin from the WordPress.org Plugin Directory, which hasn't been updated since July 2016, and no longer worked with the latest version of WordPress and WooCommerce.

Significant code updates and tidying have been performed and this version now works with WordPress 4.8.2 and WooCommerce 3.1.2.


#### Sample code

`
<?php
/*
  Plugin Name: License Checker
  Version: v1.0
  Plugin URI: http://wp-master.ir
  Author: Omid Shamloo
  Author URI: http://wp-master.ir
  Description: Sample plugin to show you how you can interact with the software license manager API from your WordPress plugin or theme
 */


// This is the secret key for API authentication. You configured it in the settings menu of the license manager plugin.
define('YOUR_SPECIAL_SECRET_KEY', 'YOUR_SPECIAL_SECRET_KEY'); //Rename this constant name so it is specific to your plugin or theme.

// This is the URL where API query request will be sent to. This should be the URL of the site where you have installed the main license manager plugin. Get this value from the integration help page.
define('YOUR_LICENSE_SERVER_URL', 'http://wp-master.ir'); //Rename this constant name so it is specific to your plugin or theme.

// This is a value that will be recorded in the license manager data so you can identify licenses for this item/product.
define('YOUR_ITEM_REFERENCE', 'YOUR_ITEM_REFERENCE'); //Rename this constant name so it is specific to your plugin or theme.

add_action('admin_menu', 'slm_sample_license_menu');

function slm_sample_license_menu() {
    add_options_page('Sample License Activation Menu', 'Sample License', 'manage_options', 'youlice_classesence', 'sample_license_management_page');
}

function sample_license_management_page() {
    echo '<div class="wrap">';
    echo '<h2>Sample License Management</h2>';

    /*** License activate button was clicked ***/
    if (isset($_REQUEST['activate_license'])) {
        $license_key = $_REQUEST['sample_license_key'];
        // Send query to the license manager server
        $lic    = new youlice_class($license_key , YOUR_LICENSE_SERVER_URL , YOUR_SPECIAL_SECRET_KEY );
        if($lic->active()){
            echo 'You license Activated successfuly';
        }else{
            echo $lic->err;
        }

    }

    $lic = new youlice_class($license_key , YOUR_LICENSE_SERVER_URL , YOUR_SPECIAL_SECRET_KEY );
    if($lic->is_licensed()){
        echo 'Thank You Phurchasing!';
    }else{
        ?>
        <form action="" method="post">
            <table class="form-table">
                <tr>
                    <th style="width:100px;"><label for="sample_license_key">License Key</label></th>
                    <td ><input class="regular-text" type="text" id="sample_license_key" name="sample_license_key"  value="<?php echo get_option('sample_license_key'); ?>" ></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="activate_license" value="Activate" class="button-primary" />
            </p>
        </form>
        <?php
    }


    echo '</div>';
}


class youlice_class{
    public $lic;
    public $server;
    public $api_key;
    private $wp_option 	= 'product_1450';
    private $product_id = 'My_product_name_OR_ID';
    public $err;
    public function __construct($lic=false , $server , $api_key){
        if($this->is_licensed())
            $this->lic      =   get_option($this->wp_option);
        else
            $this->lic      =   $lic;

        $this->server   =   $server;
        $this->api_key  =   $api_key;
    }
    /**
     * check for current product if licensed
     * @return boolean
     */
    public function is_licensed(){
        $lic = get_option($this->wp_option);
        if(!empty( $lic ))
            return true;
        return false;
    }

    /**
     * send query to server and try to active lisence
     * @return boolean
     */
    public function active(){
        $url = YOUR_LICENSE_SERVER_URL . '/?secret_key=' . YOUR_SPECIAL_SECRET_KEY . '&slm_action=slm_activate&license_key=' . $this->lic . '&registered_domain=' . get_bloginfo('siteurl').'&item_reference='.$this->product_id;
        $response = wp_remote_get($url, array('timeout' => 20, 'sslverify' => false));

        if(is_array($response)){
            $json = $response['body']; // use the content
            $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', utf8_encode($json));
            $license_data = json_decode($json);
        }
        if($license_data->result == 'success'){
            update_option( $this->wp_option, $this->lic );
            return true;
        }else{
            $this->err = $license_data->message;
            return false;
        }
    }

    /**
     * send query to server and try to deactive lisence
     * @return boolean
     */
    public function deactive(){

    }

}
`
> **Please note**
> The license validation part for your distributed plugins and themes is not part of this plugin. Therefore please take a look into the Software License Manager documentation.
>  
> **Known incompatibility issue with iThemes Security**
> If you have installed "iThemes Security", uncheck **Long URL Strings** where the Software License Manager plugin is installed

= Credits =

* [Woocommerce](https://wordpress.org/plugins/woocommerce/)
* [Software License Manager](https://wordpress.org/plugins/software-license-manager/)
* [EDD Software License Manager](https://wordpress.org/plugins/edd-software-license-manager/)
* [WP 4.8.2 and WC 3.1.2 Compatibility](https://maddisondesigns.com)

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
No! That's one of the biggest benefits of this integration. Woocommerce and the Software License Manager can be installed on different sites.

= Can I use this plugin to validate the generated license keys? =
No! The license validation part for your distributed plugins and themes is not part of this plugin. Therefore please take a look into the Software License Manager documentation.

== Screenshots ==

1. Configuration screen
2. Activating licensing for a download
3. Output the generated license keys within your emails

== Changelog ==

= 2.0.0 =
- Updated plugin to work with WordPress 4.8.2 and WooCommerce 3.1.2
- Fixed error produced due to WC Order Properties being accessed directly on My Account Order View page
- Pass Product Ref to Software License Manager API when generating key
- Added ability to enable/disable debug logging messages
- Added logging messages throughout code which are only displayed when logging is enabled
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

= 1.0.4 (1th November 2015) =
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
- nothing yet
