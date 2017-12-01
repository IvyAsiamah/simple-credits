### Simple Credits ###
* Contributors: ekussberg
* Tags: simple credits, woocommerce, credits, shop
* Requires at least: 3.4.0
* Tested up to: 4.4.0
* Stable tag: v3.5
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0.html

Simple Credits is the plugin for WooCommerce in WordPress. This plugin creates a credits payment system in the WooCommerce shop.

## Description ##

The main idea of the credits system, is that your users are buying credits as an internal payment currency.
Credits system was formerly developed as an important sales and marketing tool. Here is one use-case: – Credit bundles: 15, 30 and 60 Credits – Product prices: 4, 9 and 13 Credits In this combination it doesn’t matter how many products the customer buys, there will be some not used credits on the user account. This is a possible reason, why some users will buy the credits again and buy some new products.

## Installation ##
1. Upload and activate the plugin
2. Create bundles as woocommerce products by choosing Category # “Credit”, Type # “virtual” and Credits Amount (NB: Credits Amount field will appear after you save the product in “Credit” category)
3. Add normal products and set the price # number of credits
4. Use [buy_credits_button] shortcode to display a button for buying with credits
5. Create a page where you place this shortocode [creditwoocommerce] to display credit bundles
6. You can change the layout by adding custom css classes for credit object like: creditsTable (bundles table) etc

* “lang.php” – all the translations are saved here. If you would like to add a new language, just add the isoCode to each value in array. “ajax.php” – here is saved the e-mail html code, that users get after buying a product with credits. “download.php” – here is saved the download timeout time. As default the download link expires in 48 hours.
* [creditwoocommerce] – displays a table with all credit bundles you created. To modify the design of the credit bundles, you just need to add some custom css code for the credit bundle table, that has “creditsTable” class.
* [buy_credits_button] – displays a button to buy the product for credits. You can pass “class” and “title” as a parameter for the shortcode. Example: [buy_credits_button class#”custom_buttom” title#”Buy It Now”]
* [usercreditwoocommerce] – displays the current user credits balance. For example: echo do_shortcode(“[usercreditwoocommerce]”).” – Credits you have” – > “100 – Credits you have”
* [user_bought_products] – this is a shortcode, that You can place on user account page to show the list of products user bought with credits.

## Changelog ##

# v1.1 #
* product link in admin panel
* user statistics product links
* default messages in english

# v2.0 #
* Admin table redesign.
* Frontend table design and fix for using in page content.
* security fix for download product
* now using blockui instead of growl
* not only for digital products

# v2.1 #
* Fixed urls for multisite and subdomains

# v2.2 #
* Added a table to view when “admin” added credits to users
* Bought products shot code now displays also a search form
* Fixed some issues with checking the parameters

# v2.3 #
* Fixed the bug, when the product has no file
* Fixed the bug, when the user has exact same amount of credits left as product costs

# v3.0 #
* WooCommerce 2.0 brought a lot of changes and now the plugin is optimized for the latest WooCommerce 2.0+

# v3.1 #
* Fixed the problem with friendly url on thank you page with adding credits to the user.

# v3.2 (11.05.2014) #
* Added a custom field for credits bundle products, now you are able to define the credits amount separate from the title.
* As i saw, that a lot of people use variable products, now i also added support for variable products with multiple buttons for buying different products. (for example images in different sizes: S, M, L, XL etc)

# v3.3 (17.05.2015) #
* Updated the user and products statistics table with pagination.

# v3.4 (04.10.2015) #
* Updated some tags search.
* Updated the download method for some users, who have errors in other plugins/files to clear buffer before download.
* Now the credits are added when the customer order changes status to “completed”. (Before were added after checkout).

# v3.5 (30.10.2015) #
* Updated some translations.
* Added support for products with multiple files.
* Flow of purchase with credits updated with popup.
