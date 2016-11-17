<?php
/**
 * Auto Generated by GWFv3.04-2012.Jul.01 *
 * It is good to have a backup at a second physical location *
 * Because of the GWF_SECRET_SALT *
 */
#######################
### Error reporting ###
#######################
ini_set('display_errors', 1);
error_reporting(0xffffffff);

############
### Main ###
############
define('GWF_DOMAIN', 'gwf4.giz.org'); # Example: 'www.foobar.com'.
define('GWF_SITENAME', 'LinkUup'); # Your Site`s name. htmlspecialchars() it yourself.
define('GWF_WEB_ROOT_NO_LANG', '/'); # Add trailing and leading slash. Example: '/' or '/mywebdir/'.
define('GWF_DEFAULT_DOCTYPE', 'html5'); # Set the default html-doctype for gwf. Modules can change it.
define('GWF_LOG_BITS', 4095); # bitmask for logging: NONE = 0; GWF_WARNING = 0x01; GWF_MESSAGE = 0x02; GWF_ERROR = 0x04; GWF_CRITICAL = 0x08; PHP_ERROR = 0x10; DB_ERROR = 0x20; SMARTY = 0x40; HTTP_ERROR = 0x80; HTTP_GET = 0x100; HTTP_POST = 0x200; IP = 0x400;
define('GWF_DEBUG_JS', true);

################
### Defaults ###
################
define('GWF_SERVER_VERSION', 'APACHE2.4'); # Either APACHE2.2, APACHE2.4 or NGINX
define('GWF_DEFAULT_LANG', 'en'); # Fallback language. Should be 'en'.
define('GWF_DEFAULT_MODULE', 'GWF'); # 1st visit module. Example: 'Wanda'.
define('GWF_DEFAULT_METHOD', 'About'); # 1st visit method. Example: 'Home'.
define('GWF_DEFAULT_DESIGN', 'default'); # Default design. Example: 'wanda'.
define('GWF_DOWN_REASON', 'Converting the database atm. should be back within 45 minutes.'); # The Message if maintainance-mode is enabled.

###############
### Various ###
###############
define('GWF_ONLINE_TIMEOUT', 300); # A request will mark you online for N seconds.
define('GWF_CRONJOB_BY_WEB', 1); # Chance in permille to trigger cronjob by www clients (0-1000)
define('GWF_USER_STACKTRACE', false); # Show stacktrace to the user on error? Example: true.

################
### Database ###
################
define('GWF_SECRET_SALT', 'sUROGnzSROhak1Zv'); # May not be changed after install!
define('GWF_CHMOD', 0700); # CHMOD mask for file creation. 0700 for mpm-itk env. 0777 in worst case.
define('GWF_DB_HOST', 'localhost'); # Database host. Usually localhost.
define('GWF_DB_USER', 'gwf4'); # Database username. Example: 'some_sql_username'.
define('GWF_DB_PASSWORD', 'gwf4'); # Database password.
define('GWF_DB_DATABASE', 'gwf4'); # Database db-name.
define('GWF_DB_TYPE', 'mysqli'); # Database type. Currently only 'mysql' is supported.
define('GWF_DB_ENGINE', 'myIsam'); # Default database table type. Either 'innoDB' or 'myIsam'.
define('GWF_TABLE_PREFIX', 'gwf_'); # Database table prefix. Example: 'gwf3_'.

###############
### Session ###
###############
define('GWF_SESS_NAME', 'LUP'); # Cookie Prefix. Example: 'GWF'.
define('GWF_SESS_LIFETIME', 28800); # Session lifetime in seconds.
define('GWF_SESS_PER_USER', 2); # Number of allowed simultanous sessions per user. Example: 1

#############
### EMail ###
#############
define('GWF_DEBUG_EMAIL', 31); # Send Mail on errors? 0=NONE, 1=DB ERRORS, 2=PHP_ERRORS, 4=404, 8=403, 16=MailToScreen, 31=DEBUG)
define('GWF_BOT_EMAIL', 'robot@gwf4.giz.org'); # Robot sender email. Example: robot@www.site.com.
define('GWF_ADMIN_EMAIL', 'admin@gwf4.giz.org'); # Hardcoded admin mail. Example: admin@www.site.com.
define('GWF_SUPPORT_EMAIL', 'support@gwf4.giz.org'); # Support email. Example: support@www.site.com.
define('GWF_STAFF_EMAILS', 'admin@gwf4.giz.org'); # CC staff emails seperated by comma. Example: 'staff@foo.bar,staff2@blub.org'.

#####################
### Website Down? ###
#####################
#define('GWF_WORKER_IP', '192.168.0.4');
#(c)2009-2012 gizmore.
?>
