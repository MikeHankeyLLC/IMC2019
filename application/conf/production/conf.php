<?

// Require the database settings
require('database.php');

ini_set('display_errors', 'off');
if (isset($_GET['debug'])) {
    ini_set('display_errors', 'on');
}

// IMC 2017 Specific
define ('MAIN_CONTACT', 'imc2019@imo.net'); 

define ('PUBLIC_URL', 'https://imc2019.imo.net');

define ('BASE_URL', 'http://imc2019.amsmeteors.org');
define ('ROOT_DIR', '/DATA/sites/imc2019.imo.net');
define ('HTDOCS_DIR', ROOT_DIR . '/htdocs');
define ('GEOIP_DIR', ROOT_DIR . '/application/system/vendors/maxmind');
define ('APP_DIR',  ROOT_DIR . '/application');

define ('PROFILE_DIR', ROOT_DIR . '/images/profile');
define ('PROFILE_URL', BASE_URL . '/images/profile');
define ('UPLOAD_DIR', ROOT_DIR . '/upload_pic');
define ('PUBLIC_UPLOAD', HTDOCS_DIR . '/members/upload');
define ('UPLOAD_URL', BASE_URL . '/members/upload');
define ('VIDEO_DIR', PUBLIC_UPLOAD . '/videos');
define ('VIDEO_HOLDING', ROOT_DIR . '/application/video_holding');


//whether or not the wordpress side is live. if this is set to 1, then do not print wordpress links
define ('FRAMEWORK_ONLY', 0);

//whether or not to print paypal donate buttons
define ('PAYPAL_ON', 0);

//whether or not to allow access to mobile apis
define ('MOBILE_API_ON', 1);

//number of videos or photos per row
define ('ROW_LENGTH', 5);

//thumbnail size
define ('THUMB_W', 300);
define ('THUMB_H', 255);

//max upload image size
define ('MAX_W', 1800);
define ('MAX_H', 1800);

//max width that looks good on a page
define ('MEDIUM_W', 900);

//max profile picture size
define ('PROF_W', 300);
define ('PROF_H', 300);

//number representing that the user was unsure of a certain field's value
define ('UNSURE_VAL', -999);

// Turn logging on or off
define ('ENABLE_LOGGING', true);
define ('LOG_DIR', ROOT_DIR . '/logs');

// Cookie Variables
define ('COOKIE_EXPIRE', 31536000);

// Memcache Settings
define ('MEMCACHE_ADDR', 'localhost');
define ('MEMCACHE_PORT', 11211);

// Encryption Variables
define ('ENC_KEY', 'your_crypt_key_here');

// Put any dev only defines below here


// Conference official email (for the confimation email)
define ('IMC_EMAIL','imc2019@imo.net');

// List of developer email addresses delimited by commas
define ('DEVELOPER_EMAIL_ADDRESSES', 'vperlerin@gmail.com');

// List of admins email
define ('ADMINS',   serialize(array('imc2019@imo.net')));    
 
// List of emails that will receive payment confirmation and reminders from the email_api 
// treasurer@imo.net
define ('FINANCE_ADMINS',   serialize(array('marc.gyssens@uhasselt.be,vperlerin@gmail.com')));  

// Needs to be alone
define ('FINANCE_ADMIN', 'marc.gyssens@uhasselt.be');
