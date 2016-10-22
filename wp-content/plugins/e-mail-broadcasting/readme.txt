=== E-Mail Broadcasting ===
Contributors: dimitrov.adrian
Tags: email, broadcast, maillist, bulk email
Requires at least: 3.2
Tested up to: 3.3
Stable tag: trunk


== Description ==

Easy for use bulk email sender.
If you have online magazine alike blog or website and want to send users annoncments, this plugin is for you.


When plugin is installed, an **E-Mail Broadcasting** menu item will apear in **Tools** *(requires administrator use role)*

= Feautures: =

*   Import emails from CSV files.
*   Import emails from commenters.
*   Export emails to file
*   Integrated visual editor.
*   Split sending into potions (for example 100 mails per hour).
*   Widgets and shortcodes for user subscribtion.
*   Uses native wp_mail() function, which allow you to configure the mail sending function of WP with what plugin you want.


= Widgets that plugin add: =

*   **E-Mail Broadcasting**                *User (un)subscribe panel*

= Shortcodes: =

*   **[emailbroadcasting]**                *Default for user subscribtion*
*   **[emailbroadcasting unsubscribe]**    *For user unsubscribe*



== Screenshots ==

1. The publish interface
2. The settings



== Changelog ==

= 0.2.2 =
* Fix install plugin profile

= 0.2.1 =
* Fix sending mail instands of debug messages (sorry, my fall)

= 0.2 =
* Complete rewriten code in oop
* Increase required WP version to >= 3.2 (this is need to fix visual editor in lower versions)
* Added localisation support, languages directory with default pot file for easy localisation //Request from Gürol BARIN (thanks for the idea)
* Added cron level activation
	users - all site visits can trigger the sender
	admins - sender is activated automatically only from admin panel
	URL  - it is triggered only from the key url
* Added multiple email templates // Forum request
* Added fullscreen button in the visual editor
* Added new "History" tab, can see last 30 (un)subscribed users
* Added new option "Start sending immediatly"
* Added new option "Export delimeter"
* Droped ajax support while browsing the mails
* Drpped security checkbox in sending
* Droped screen help (need to be reworked in future)
* Fix bug with escaping chars in the editor //Reported from gdurniak in the forum (thanks)
* Fix bug in options in the first run
* Fix bug when plugin is installed as symlink (in windows fs)
* Fix bug in option saving
* Fix bug in sending process autoreloading
* Fix bugs with some strings, now they are more translatable by the core
* Fix some visual glitches
* Tweak import function to import corectly more cvs formats
* Changed name of main file 'emailbroadcasting.php' to 'e-mail-broadcasting.php' (which allow better plugin allocating)
* Changed database structure, added new fields list and deleted
* Changed option saving method, now using one serialized array instsands of multiple database records
* Bump version 0.2


= 0.1.3 =
* Now exit when using triggerhash
* Fix bug in settings, triggerhash is not visible
* Now autoload options only that have to be loaded at start
* Added export option


= 0.1.2 =
* Fix bug with saving the test email list
* Added TriggerHash option
* Added contextual help


= 0.1.1 =
* Readme fix


= 0.1 =
* Initial 'alpha' release


= @TODO =
* Better visual editor integration
* (DONE) Add option to search/filter emails in the browser
* (DNE) Add history or templates in publishing tab
* Add email shortcode for user unsubscription (ex.: [unsubscribe_link]click here if you doesn't want recieve more mails[/unsubscribe_link])
* (DONE) Rewrite the code according oop principies
* Multiple email lists
* (DONE) New tab "Last activity" for last N (un)subscribtions
* Ability to view all unsubscribed and permanently delete them
* Fix the screen help
* Migrate to builtin WP crontab

= @KNOWING ISSUES =


== Frequently Asked Questions ==


= I can't send mails (both test and to list) =

May be you have not correctly configured mail system on your blog/website. You can use some wordpress plugin to reconfigure the builtin wp_mail() function with specific details.
I reccommend this http://wordpress.org/extend/plugins/wp-mail-smtp/ it work fine on my blog
