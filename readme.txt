=== WordPress UnocoinBitcoin ===
Contributors: Tips and Tricks HQ
Tags: bitcoin, payment, unocoin, shortcode, payment gateway, cart, checkout, e-commerce, online store, sell, accept bitcoin, bitcoin store
Requires at least: 3.0
Tested up to: 4.2
Stable tag: 1.1
License: GPLv2 or later

Easily accept Bitcoin payments into your wallet via unocoin payment gateway. Quick on-site checkout functionality.

== Description ==

This plugin enables unocoin payment gateway functionality on your WordPress site to accept bitcoin payments for your products or services.

= Features = 

* Accept payments in bitcoins from any country in 150+ currencies
* Automatically convert your store currency to bitcoin via flexible exchange rate and with no hidden fees
* The plugin comes with a full bitcoin checkout process (your customers will be able to click a button to pay for your items using bitcoin)
* Track unocoin orders from your WordPress dashboard (the order dashboard menu will show you all the bitcoin transactions from your site)
* Automatically send confirmation email to your user when a payment is accepted on the bitcoin network
* Accept payments in bitcoins directly into your wallet.

= Settings Configuration =

Once you have installed the plugin you need to provide your bitcoin merchant details in the settings menu (Settings -> WP Bitcoin).

* API Key (Your unocoin merchant api key)
* Transaction Speed (Speed at which the bitcoin transaction registers as "confirmed" to the store)
* Order Information Page (URL of the page where order information will be collected from your users)
* Order Information Page Title (Title of the page where order information will be collected from your users)
 
Now create a new post/page and insert Bitcoin shortcode for your product. For example:

`[wpbc_buy_now item_name="Test Product" price="5.00" currency="INR"]`

You can also use your custom button image using the "image" parameter:

`[wpbc_buy_now item_name="Test Product" price="5.00" image="http://www.example.com/images/button.jpg"]`

For screenshots, detailed documentation, support and updates, please visit: [WordPress Bitcoin plugin](http://www.tipsandtricks-hq.com/wordpress-bitcoin-payment-plugin) page

== Usage ==

You need to embed the appropriate shortcode on a post/page to create unocoin Buy Now button.

Here is an example shortcode:

[wpbc_buy_now item_name="My Test Product" price="29.95" currency="INR"]

== Installation ==

Upload the plugin to the plugins directory via WordPress Plugin Uploader (Plugins -> Add New -> Upload -> Choose File -> Install Now) and Activate it.

== Frequently Asked Questions ==

= Can this plugin be used to create Buy Now button for unocoin payment gateway? =
Yes

= Can I accept Bitcoin payments using this plugin? =
Yes

= Can I sell products using bitcoin with this plugin? =
Yes

== Screenshots ==

None

== Upgrade Notice ==

None

== Changelog ==

= 1.1 =
* WordPress 3.9 compatibility

= 1.0 = 
* First commit
