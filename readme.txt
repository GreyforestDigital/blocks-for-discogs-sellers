=== Blocks for Discogs Sellers ===
Plugin URI: https://www.greyforest.digital/plugins/blocks-for-discogs-sellers
Author: Greyforest
Author URI: https://www.greyforest.digital
Contributors: GreyforestDigital
Donate link: https://www.greyforest.digital/donate
Tags: discogs, api, collection, inventory, vinyl
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 1.0.2
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gutenberg blocks for Discogs Sellers to embed a realtime sync of profile data and Marketplace Listings with sorting and tons of customization options.

== Description ==

Gutenberg blocks for Discogs Sellers to embed a realtime sync of profile data and Marketplace Listings with sorting and tons of customization options.

Embed your Discogs marketplace listings on your own site, allowing you to build an entirely customizable digital storefront on your own platform with up-to-the-minute inventory syncing from your Discogs account. Send customers to your website but leave the complications of checkout & payment to Discogs. 

Add trust to your online record store by displaying a Seller Badge with a link to your Discogs store, plus realtime stats of your positive feedback and ratings.

Powered by the Discogs API, this plugin requires a valid Discogs account and personal access token to use.

Options for front-end sorting, filtering, and pagination to easily navigate large sets of titles.

Display data from any seller whose profile is set to public - not just your own.

## SELLER INVENTORY
* Display titles from a seller's Marketplace Listings as grid or list
	* Choose default desktop column count
	* Built-in responsive layout adjustments for smaller screens
	* Choose where to send users on click - to single listing page or to Discogs for purchase
* Control which fields are displayed
    * Image, title, artist, price, format, year, catalog number, grading, comments, community stats, "accepts offer" status, and purchase button
* Set default sorting, sort order, and per page options
* Allow users to sort on front end or use default settings
* Fetches 100 releases maximum per page with front-end pagination features
* Works for any username with public listings

## SELLER LISTING
* Display a single marketplace listing via dynamic URL parameters, post meta fields, or providing the Listing ID per block
	* Dynamic ID population allows a single page to serve as template for all listings
	* Manual ID population requires page-per-listing, but builds SEO value
* Control which fields are displayed
    * Image, title, artist, price, format, year, catalog number, grading, comments, community stats, "accepts offer" status, and purchase button
* Works for any Marketplace listing

## SELLER PROFILE
* Display a visually-pleasing Seller Profile section featuring your Discogs profile data
* Header section with banner graphic or background color
* Statistics from Discogs Marketplace listings
	* Buyer Rating (stars + percentage), Seller Rating (stars + percentage), rating counts, and items for sale count
* Control which fields are displayed
    * Avatar, banner, profile link, store link, location, registration date, profile text, and all statistics mentioned above
* Visual customization of button colors and border
* Easy to style with CSS or inherits text colors from parent elements
* Works for any public username

## SELLER BADGE
* Display a minimal Seller badge featuring Discogs profile data
* Banner graphic background or background color
* Realtime statistics from Discogs Marketplace listings
	* Seller Rating (stars + percentage), rating counts, and items for sale count
* Visual customization of colors and border
* Works for any public username

## SETTINGS PAGE
* Fields for providing username and personal access token to blocks globally
* Visual API rate limit interface for keeping track of current rate limits
* Transient cache interface for clearing outdated data
* Debug log for tracking every API call to Discogs

## CACHING
One of the most important features in this plugin is the ability to cache Discogs API calls in temporary transients. This has several benefits:  
* Reduces the amount of times your token needs to "ping" Discogs, preventing API overages or your site being blocked for overuse
* Drastically reduces the page load time after data has been cached

Caching works for all blocks and types of API calls, WITH THE EXCEPTION of when users are allowed to sort/filter on the front-end (seller inventory). Allowing sort parameters turns caching off, and will require an API call on every page load.

## COMPATIBILITY
* This plugin requires a minimum PHP version of 8.0
* This plugin requires Gutenberg Editor to be activated
* This plugin requires an active Discogs account and personal access token

### NOTICE
*Your use of this plugin as an individual and a site owner is governed by the terms outlined on Discogs' ["Terms of Use"](https://support.discogs.com/hc/en-us/articles/360009334593-API-Terms-of-Use).*

*This plugin is not affiliated with, endorsed by, or built in collaboration with Discogs.

== Screenshots ==

1. Display a grid or list of titles from a user’s default collection, with options for displaying any/all fields (image, title, artist, price, comments, grading, etc)
2. Display a grid or list of titles from a user’s default collection, with options for displaying any/all fields (image, title, artist, price, comments, grading, etc)
3. Customizable realtime display of your profile information, including buyer/seller stats, feedback ratings, current inventory count, and more
4. Minimal seller badge that includes profile photo, customizable colors, inventory count, seller ratings, and more 
5. Display a single Marketplace listing, with options for displaying any/all fields (image, title, price, comments, grading, etc)
6. Editor preview of dynamic single Marketplace listing, populated via URL parameters or custom meta field

== Support ==

If you have questions, need help, or just want to share feedback, I recommend using the Support tab on the WordPress.org plugin page. It's the best way to reach out quickly and keep everything organized. I appreciate positive reviews if you liked this plugin or found it useful.

For general inquiries, you can also reach out at [https://www.greyforest.digital/contact](https://www.greyforest.digital/contact).

== Changelog ==

= 1.0.2 -> July 10th, 2026 =
* Compatibility check for WordPress 7.0.0 + 7.0.1

= 1.0.1 -> March 19th, 2026 =
* Fixed admin-ajax.php unauthorized user bug caused by current_user_can() function placement in logging admin_init() action

= 1.0.0 -> February 15th, 2026 =
* Initial commit to repository