=== Simple Share Buttons Adder ===
Contributors: sharethis, scottstorebloom, surlyrightclick, DavidoffNeal
Tags: share buttons, social buttons, facebook, twitter, google+, share, share links, stumble upon, linkedin, pinterest, yummly, vk
Requires at least: 4.5
Tested up to: 4.8.2
Stable tag: 7.3.10
License: GPLv2 or later

A simple plugin that enables you to add share buttons to all of your posts and/or pages.

== Description ==

The Simple Share Buttons Adder does exactly what it says – adds share buttons to all of your posts and pages, simply.

This plugin has a dedicated website! Check out <a href="https://simplesharebuttons.com" target="_blank">simplesharebuttons.com</a>

You can even upload and use your own custom images if you wish!

The Simple Share Buttons Adder utilizes features that, as a WordPress user, you will be familiar with. A self-explanatory administration screen will have you showing your Share Buttons on your posts and pages in no time!

By downloading and installing this plugin you are agreeing to the <a href="https://simplesharebuttons.com/privacy/" target="_blank">Privacy Policy</a> and <a href="https://simplesharebuttons.com/privacy/" target="_blank">Terms of Service</a>.

== Installation ==

= Automatic =
* Click the ‘Plugins’ tab in WordPress admin view
* Click the ‘Add New’ button
* Search for ‘Simple Share’
* Click ‘Install Now’
* Activate the plugin
* Navigate to ‘Installed Plugins’
* Click ‘Settings’
* Toggle On/Off Location Placement
* Click the blue save icon

= FTP =
* Upload Simple Share Adder to the `/wp-content/plugins/` directory
* Activate the plugin
* Navigate to ‘Installed Plugins’
* Click ‘Settings’
* Toggle On/Off Location Placement
* Click the blue save icon

= Upload =
* Upload the downloaded zip file on the ‘Add New’ plugins screen (see the ‘Upload’ tab) in your WordPress admin view
* Activate the plugin
* Navigate to ‘Installed Plugins’
* Click ‘Settings’
* Toggle On/Off Location Placement
* Click the blue save icon

== Frequently Asked Questions ==

This plugin has a dedicated website! Check out the <a href="http://simplesharebuttons.com/wordpress-faq/" target="_blank"> FAQs page</a>

Please visit the <a href="https://wordpress.org/support/plugin/simple-share-buttons-adder">WordPress Support Forum</a> for any assistance you may need.

== Screenshots ==

1. The top of the admin menu including the network selection tool.
2. Some styling options for your button's share text and container.
3. The button preview and additional accordions with more configuration options.
4. An example of the buttons shown below page content using the "Ribbon" theme.

== Changelog ==

= 7.3.10 =
* Fixed pinterest blank pop up.

= 7.3.9 =
* Fixed missing variable error.
* Remove missing index errors.

= 7.3.8 =
* Add mobile specific message for whatsapp network.
* Fix plugin confliction due to non specific function name.

= 7.3.7 =
* Fixed Window specific plugin location error.
* Added missing custom email, diggit, buffer image fields.
* Put old shortcode back in place.

= 7.3.6 =
* Major plugin code clean up and refactor.
* Addition of Button Preview in admin.
* New admin menu layout design.
* Addition of Whatsapp and Xing buttons.

= 6.3.6 =
* minor bug fixes

= 6.3.5 =
* Enable declining of Terms of Service

= 6.3.4 =
* Fixes for FB open graph tags (so it doesn't add extra text)
* W3C code validation (thanks @olga22)
* Other bug fixes

= 6.3.3 =
* Fixes for old versions of PHP

= 6.3.2 =
* Minor bug fixes

= 6.3.1 =
* Added a caching layer on top of Facebook's API to ensure fallback share counts.

= 6.3 =
* Facebook share counts are back! We implemented a solution to Facebook’s deprecated API.
* Added the ability to close the update notice when updating from old plugin versions. You will still need to accept the new terms to receive the new features, however you can now more easily choose to continue using the old version.

= 6.2.4 =
* Fixes the Facebook page widget style bug.

= 6.2.3 =
* Fixes an issue with the Facebook like widget.

= 6.2.2 =
* Improves performance over previous two plugin updates.

= 6.2.1 =
* Fix bug affecting older versions of PHP that didn’t allow people to close the notice.

= 6.2.0 =
* Feature: Add Facebook Save button
* Feature: Add Facebook insights
* Feature: Add Facebook iframe sharing

= 6.1.5 =
* Feature: Add newsharecounts.com option and functionality to show Twitter share counts again

= 6.1.4 =
* Tweak: PHP notice

= 6.1.3 =
* Add sharedcount.com API functionality for Facebook share counts

= 6.1.2 =
* Reluctantly remove the twitter share count - https://blog.twitter.com/2015/hard-decisions-for-a-sustainable-platform

= 6.1.1 =
* Fix: Custom images save fixed

= 6.1.0 =
* Feature: Add Yummly share counts
* Feature: Add Tumblr share counts
* Update: New Google+ branded buttons
* Update: Use JSON array in a single database entry to store all core settings
* Tweak: Add title attributes to the buttons on the network select option to assist recognition of each network
* Tweak: Remove PHP notice

= 6.0.5 =
* Fix: Re-include http/https for URL being shared to Tumblr (404 error)

= 6.0.4 =
* Feature: Add popup-window feature when sharing
* Feature: Add number formatting (e.g. 3,563 = 3.5k)
* Tweak: Improve Facebook count reliability
* Remove link border by default in CSS

= 6.0.3 =
* Tweak: Add RTL compatibility to the admin pages by moving the save button to the left
* Fix: Remove all tags added to page/post titles by other plugins to maintain share buttons as required
* Fix: Allow buttons to be removed if all share buttons have been added

= 6.0.2 =
* Tweak: Add 'multisite' attribute option to [ssba] shortcode to (by default) fallback to the previous ssba_current_url function

= 6.0.1 =
* Tweak: Add more specific classes for better targeting
* Fix: Improve XSS fix from 6.0.0

= 6.0.0 =
* Feature: New admin panel styling
* Feature: Add additional CSS field
* Tweak: Amend ssba_current_url for multisite compatibility https://wordpress.org/support/topic/multi-site-compatibility
* Tweak: Update share count calls to use the WP API instead of file_get_contents
* Tweak: Split codebase into separate files for easier maintenance
* Tweak: Replace ampersands with %26 for page/post titles for email links
* Fix: Remove non-object notice
* Fix: Small XSS bug

= 5.6 =
* Fix: Remove various PHP notices
* Tweak: Correct LinkedIn title from Linkedin
* Tweak: Fix validation error with StumbleUpon link
* Tweak: Use lowercase 'body' and 'subject' in mailto links
* Update: Update 'Tested up to' tag to 4.1.1
* Update: Rebranded settings page

= 5.5 =
* Tweak: Revert to old Pinterest functionality by default
* Update: Add option to use featured images when 'pinning' if desired

= 5.4 =
* Fix: Use full featured image for Pinterest

= 5.3 =
* Tweak: Use full featured image instead of thumbnail

= 5.2 =
* Feature: Use a post's featured image when 'pinning'
* Feature: Add a default Pinterest image (advanced tab)

= 5.1 =
* Tweak: Button images approved by Yummly
* Update: Compatible up to tag 4.1

= 5.0 =
* Feature: Add Yummly button!
* Feature: Add VK button!
* Update: Compatible up to tag 4.0.1
* Fix: Undefined ssba_excerpts notice

= 4.8 =
* Fix: Undefined ssba_excerpts
* Update: Compatible up to tag 4.0

= 4.7 =
* Fix issue with download pages
* New branding

= 4.6 =
* Fix notice regarding ssba_excerpts

= 4.5 =
* Hotfix for potential vulnerability

= 4.4 =
* Tidy up author info and add link to Simple Share Buttons Plus
* Tidy up author info and add link to Simple Share Buttons Plus
* New option in settings to only show buttons with excerpts if wanted
* Port number not added if present
* Page title pulled more accurately and efficiently, most noticed by those using twitter and/or with shortcode

= 4.3 =
* Improved homepage detection
* Commas added for share counts in the thousands
* Images losslessly compressed for improved loadtime
* Pinterest and font fix for HTTPS
* Image src attribute moved to the start of all image tags, for improved validation

= 4.2 =
* Fix for those having trouble, error for centered buttons only

= 4.1 =
* Add missing closing div causing trouble

= 4.0 =
* I was hoping 4.0 would be really exciting, but there were a few things to fix for you guys :)
* Align-right option added!
* Center option fixed
* booShowShareCount notice removed
* Settings page CSS updated to fit more nicely

= 3.9 =
* A temporary fix for including CSS when using shortcode only

= 3.8 =
* A serious cleanup of previous small wanrings/notices
* CSS now only shows when it is required (thanks goes out to https://github.com/emilyljohnson)
* Add rel="nofollow" option added
* Add different text for the widget area if you wish
* Flattr bug fix, and title added (thanks to https://github.com/Sena for that)
* Remove 'tooltip' style hover to share text link
* Fix more validation errors, please check any custom CSS you may have applied to links with IDs
* WHAT'S NEXT?? Well providing all goes well with this release, it shall include responsive icon buttons!!

= 3.7 =
* Error reporting on if WP_DEBUG is set to true
* Share text no longer links to simplesharebuttons.com by default
* There will more improvements for 3.8, this version is a requirement from WordPress

= 3.6 =
* Errors removed, too many thousands of different possible server configurations out there!
* Custom Print button upload option added.

= 3.5 =
* Due to an extremely generous donation, a Print button has been added!
* Changed div from an ID to a class. Please note if your CSS points to #ssba, you must update this to .ssba
* Removed extra a couple of extra double quotes
* Improved/more descriptive 'alt' tags for the visually-impaired
* Improved code for placement checking
* Error reporting switched back on for developers trying to debug their code. If you have any errors displayed, please report them. Then temporarily hide by removing the two double slashes on line 24 of simple-share-buttons-adder.php - //error_reporting(0);
* Obsolete 'center' tags relaced with a div styled with text-align:center
* Whitespace of email sharing option fixed with %20
* & replaced with &amp; to fix validation errors

= 3.4 =
* An attempted fix for a reported broken Pinterest count, massive thanks to http://wordpress.org/support/profile/crookedmicks for taking the time to find this and let us know
* Sadly I have far less time than I used to have in order to maintain this plugin. I have now uploaded the source to GitHub so please use this to suggest any fixes/improvements! https://github.com/davidsneal/simplesharebuttons

= 3.3 =
* You can now make your own custom-coloured share icons here for free - http://make.simplesharebuttons.com/ !!
* Many apologies for the lack of support recently, I shall try to get better!

= 3.2 =
* Tweet text reordered to read: Page Title, Custom Twitter Text, URL
* Improved URL encoding for sharing by twitter

= 3.1 =
* Titles and twitter text issues resolved for those experiencing problems with certain characters

= 3.0 =
* CSS scripts now loaded within the HEAD tag!
* Improved loadtime!
* Note that the URL functionality has been temporarily removed, pending it's improvement.
* Fixes a clash with a function name from another plugin

= 2.9 =
* Fixes a clash of a function name with other plugins.
* Fixes blank URL for Twitter when Bit.ly limit is exceeded.

= 2.8 =
* Now you can use the widget option to add your share buttons!
* Links shared by twitter are now shortened using bit.ly!
* [ssba_hide] shortcode is greatly improved! The buttons used to just be hidden, now they aren't created at all!

= 2.7 =
* Author bio moved with new ways to show your support!
* Slightly improved admin panel, resizing of fields.

= 2.6 =
* Tumblr button added!
* You can now order your own custom-colour share buttons!! http://simplesharebuttons.com/custom-share-buttons/

= 2.5 =
* Further Twitter customisation for Twitter under 'Advanced' options!
* Buffer and Flattr buttons added! (share counts still to come!)
* Flattr requires you to enter your user ID under the advanced tab, you must also submit pages/posts here http://flattr.com/submit
* You can now set a specified URL and Title when using shortcode - [ssba url="http://simplesharebuttons.com" title="Simple Share Buttons"]!
* When doing the above, both a title and url must be specified.
* 'Default' image set has been removed! If you are currently using this image set, it will be replaced with 'Somacro'!
* Only relevant fonts are loaded if specified.

= 2.4 =
* The one people have been waiting for!
* Choose to display a share count with the buttons! (all except Digg sorry!)
* Checkout the new 'Counters' tab in the admin panel for setup!

= 2.3 =
* Add a background colour and border to your share buttons container!
* Fixes a bug that inserted an empty line at the top of pages/posts
* Apologies for so many updates!

= 2.2 =
* Standby for Share Counters, currently under development!
* Increased functionality for those with older versions of WordPress
* To get the full Simple Share Buttons experience, update to version 3.5.1

= 2.1 =
* An attempted fix for those experiencing problems with the new drag and drop facility
* Others reported problems with a blank space above their pages, this should fix this too
* Apologies for the inconvenience everyone

= 2.0 =
* Note: this update will require a moment's configuration to reinstate your share buttons
* The settings link has moved - 'Settings' -> 'Share Buttons'
* Drag and drop to reorder the buttons how you like!
* Brand new redesign of the admin panel!
* New support forums launched!
* Resize images by pixels!
* A choice of fonts for your share text!
* Code and functions optimised!
* More lightweight on database use!
* Title attributes added to all buttons!
* Set links to open in the same or new window!

= 1.9 =
* Choose placement of your share text: above, left, right or below!
* Hide share buttons on posts you wish to, using shortcode [ssba_hide]
* [ssba] shortcode will now work for any page being displayed if used outside of the norm.
* Images aligned with text by default.
* Lots of great things coming in version 2.0! No more updates for a little while :)

= 1.8 =
* Much needed fix for 1.7, many apologies all!!

= 1.7 =
* You can now use shortcode to add share buttons wherever you like! [ssba] This relies on it being place on a page/post that has a permalink attribute.
* The email subject message can now be personalised!
* A general tidyup of some of the code, separating a couple of the larger functions into separate files.

= 1.6 =
* A reluctant update, sorry!
* A small fix for when clicking the Pinterest button when using Internet Explorer. Note that IE requires the same image to be set across all pages if an image is to be pinned, I do not wish to restrict users to this so have left it this way in hope that Microsoft will resolve things their end!

= 1.5 =
* You can now add some custom text if you wish!
* Reddit and Email buttons added!
* Add your own custom styling to the Buttons!

= 1.4 =
* You can now upload and use your own custom images!
* Share Buttons can now also be shown before/after excerpts of posts when viewing categories, archives or your homepage!

= 1.3 =
* Apologies for all the updates recently, trying to get things just right!
* By popular request, share links now open in a new window.
* New 'Retro' image set added!
* Added a link to a page to showcase your website.
* Links added to the image sets for where the credit is due.

= 1.2 =
* 3 New button sets added!
* Tooltips added to the admin menu.
* Borders removed by default to prevent inheriting them from themes.

= 1.1 =
* New buttons added - Pinterest, LinkedIn and Stumble Upon.
* You can now also choose to display smaller versions of the buttons if you wish.

= 1.0 =
* Initial release
