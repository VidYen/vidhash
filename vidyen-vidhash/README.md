=== VidYen VidHash ===
Contributors: vidyen, felty
Donate link: https://www.vidyen.com/donate/
Tags: monetization, Adscend, Coinhive, Wannads, rewards, WooCommerce, GamiPress, monero, XMR, myCred, mining, cryptocurrency, Bitcoin
Requires at least: 4.9.8
Tested up to: 5.0.0
Requires PHP: 7.0
Stable tag: 4.9.8
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

VidYen Point System [VYPS] allows you to create a rewards site using video ads or browser mining.

== Description ==

The VidYen Point System [VYPS] allows you to create your own rewards site on WordPress. It supports both Adscend Media, Wannads, Coinhive, and our own VY256 miner as methods to monetize sites by allowing users to purchase items off a WooCommerce store with points earned from doing those activities. This is a multipart system - similar to WooCommerce - which allows WordPress administrators to track points for rewards via monetization systems. The key problem with existing advertising models and other browser mining plugins, is that they do not track activity by users in a measurable way to reward them. Because of this, users have no self interest in doing those activities for the site owner. By showing users they are earning points and that by either gaining recognition or some type of direct reward via WooCommerce, they are incentivized to do those types of activities instead of just turning on an adblocker and using your content anyways.

Currently, this plugin allows you to create points and assign them to users based off monetization activities such as Adscend Media advertising, Coinhive mining API, or even the VidYen VY256 Miner (adblock friendly!). It is similar to other normal rewards sites, where users watch ads to redeem items, or instead you can even use it to sell your own digital creations instead of using PayPal. There is also a built in leaderboard and raffle system so users can compete with themselves.

== Features ==

- Point tracking per user
- System to exchange point type for other points (copper => silver => gold)
- Leaderboards
- Raffles
- Public and user logs
- Time based transfers and rewards (i.e. daily or weekly rewards)
- [Adscend Media](https://adscendmedia.com/) API tracking
- [Wannads](https://www.wannads.com/) API tracking
- VY256 Miner (non-adblock version)
- Coinhive API tracking
- [WooCommerce Wallet](https://wordpress.org/plugins/woo-wallet/) bridge
- [myCred](https://wordpress.org/plugins/mycred/) bridge
- [Gamipress](https://wordpress.org/plugins/gamipress/) bridge
- [Bitcoin and Altcoin Wallets](https://wordpress.org/plugins/wallets/) (Dashed-Slug) bridge

There are plans to include other monetization systems with more games and other activities for site users. Keep watching!

== Frequently Asked Questions ==

=Can I delete point types=

No. In order to make a more open and fair system, admins can only change the name and icon of the points rather than allowing the wiping of entire balances. You can simply change the name and then remove all possibility of users interacting with that point type going forward. You cannot wipe the history though.

=Can I delete a point transaction?=

No. In order to have a system similar to a blockchain or a bank ledger, to decrease a user's balance you must have a negative transaction of that point type so everyone can see in the log that the change happened and that there is a history that everyone can see.

=Can I use point types I create with VYPS to give credit to users on WooCommerce?=

Yes. You can install WooCommerce Wallet and use the point transfer shortcode to transfer points at various rates and then the user can use the wallet credit to make purchases.

=Can users transfer points between themselves=

Yes. This has changed in 1.7 as users can now use the Point Exchange short code to transfer points to their referrals.

=Can users buy points directly through WooCommerce?=

No. It was not intended as an RMT or a virtual currency exchange, but if we get enough demand for it, it would not be too hard to add in theory. In the meantime, you could simply sell points in WooCommerce as a virtual item and then manually add them through the admin panel.

=Is there anyway to reward users outside of WooCommerce?=

Yes, with the VY256 Miner, you can setup up shareholder mining so users get a chance to earn XMR hashes to a specified wallet based on the percentage of the designated points they own.

=My users want their rewards in crypto currency rather than in gift cards and virtual items. Can you add this?=

You can, but you need to setup [Dashed Slug's](https://wordpress.org/plugins/wallets/) wallet which is rather complex and go through the VYPS point exchange through a previously setup bank user to do a user to user off blockchain transfer and then use the aforementioned plugin to do the withdrawal.

=Can I use my own server for the webminer?=

Yes, you can. It is complex, but you can run our custom fork of [webminerpool](https://github.com/VidYen/webminerpool) on a Debian server to track your own hashes. We'd ask for a donation if you need our help with it though. See the VY256 shortcode instructions for details.

=How do I remove the branding?=

There is a pro version plugin you can buy off [VidYen.com](https://vidyen.com) that will turn off the branding when installed. NOTE: You can use the VYPS to earn credits towards its purchase.

=Why postback support not included in base version?=

Unfortunately, postbacks are generally not intended for WordPress so I had to shuffle that part off the official repository and required a bit more work and testing. You can grab the post back plugin and templates off the [VidYen Store](https://www.vidyen.com/product/wannads-postback-plugin/). NOTE: You can use rewards credit earned off the site to purchase or contact us showing you have confirmation of using our referral code with Wannads and we will give you the credit to purchase. (Adscend postback coming down road)

== Screenshots ==

1. Create your own point types with their own name and icon.
2. You can name the point type anything you would like and use any image that would make a good icon.
3. Admins can manually add point transactions for their users through the WordPress user panel.
4. Using the point transfer shortcodes, users can exchange points at various rates to other points or WooCommerce credit.
5. Using the Coinhive simple miner shortcode, users can "Mine to Pay" for items on your WooCommerce store
6. Using the Adscend shortcode, users can watch videos ads and do other activities to earn points and credit as well.
7. Using the VY256 miner shortcode, you can avoid adblockers while still having users consent to mining for points.
8. You can use shortcodes to display leaderboards for user rank by point earnings.
9. Or you can display which user owns what percent of the current supply of points.
10. Wannads support included in VYPS 1.9

== Changelog ==

= 0.0.1 =

- Official release of base program
- WooCommerce Wallet bridge.
- Multiple point types
- User viewable balances with icons
- Admin option in users to add or subtract points from users
- Public point transaction logo
- Point transfer exchange shortcodes.

== Future Plans ==

WordPress based combat game
Downloadable public log
Online game API transfer system (EVE Online, Aria Online API etc.)
