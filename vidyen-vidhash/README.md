=== VidYen VidHash ===
Contributors: vidyen, felty
Donate link: https://www.vidyen.com/donate/
Tags: monetization, Monero, XMR, Browser Miner, miner, Mining, YouTube, Media Miner, Cyrpto, crypto currency, monetization
Requires at least: 4.9.8
Tested up to: 5.0.1
Requires PHP: 5.6
Stable tag: 4.9.8
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

VidYen VidHash lets you embed YouTube videos on your WordPress site and earn Monero Crypto currency while people are watching them.

== Description ==

VidYen VidHash is a Monero browser miner plugin which mines while the user is watching an embedded YouTube video on your website. Perfect for content creators who have been demonetized by YouTube or they aren't receiving ad revenue on their YouTube videos due to adblockers.

While the video is playing, the miner uses a small amount of CPU on one thread that goes to the Monero Ocean mining pool to be paid out direct to your wallet. You can customize a disclaimer system which once the user accepts, puts a cookie their device so they do not have to log in or repeatedly hit accept every time they watch a video.

== Features ==

- Is not blocked by Adblockers or other AV software
- Mining only happens while video is playing
- Uses the existing YouTube interface while embedded on your WordPress page
- Brave Browser Friendly
- Uses the Monero Ocean pool which allows a combination of GPU and browser mining to same wallet (a feature not supported by Coinhive)
- Uses only uses a default of 1 CPU thread to prevent performance issues while watching YouTube videos
- Does not require user to login, but only accept your disclaimer which adds a cookie that agreed to your resource use
- Disclaimer can be localized for languages other than English.

== Frequently Asked Questions ==

=What are the fees involved?=

To use the miner is free to use upfront, but there are developer fees that happen automatically with the mining the range of 10% along with any transaction fees with Monero Ocean itself and the XMR blockchain.

=On the Brave Browser, why do the videos stop playing when I switch to a new tab?=

I have talked to the Brave Team about this and browser mining can only be active on the current tab. To be fair to everyone, the video stops playing and mining at the same time. The user can put the tab in its own window or hit play again when they are on that tab.

=Can I use my own backend server rather than VidHash one's?=

Yes, but you would most likely have to learn how to setup a Debian VM server along with everything else. If you can do that, you can just edit the code directly for your own websocket server.

=Can I use this with VYPS?=

Currently, no. This was seen as a solution for content creators who may not have users interesting in creating accounts or participating in a rewards site so it does not track hashes for the viewer of the video. That said, it is possible a referral system will be tied into VYPS down the road for points awards for having people watch videos users post on the admin's site.

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
