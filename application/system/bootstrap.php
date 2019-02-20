<?php

if(!defined('STARTUP_ONLY')) {
    session_start();
}

// Pull in the misc functions
require_once('misc.php');
require_once('libraries/recaptcha.class.php');
require_once(ROOT_DIR. '/application/system/libraries/PHPMailer/class.phpmailer.php');
require_once(ROOT_DIR. '/application/system/libraries/PHPMailer/class.smtp.php');
require_once(ROOT_DIR. '/application/system/libraries/PHPMailer/class.pop3.php');


// Load Proper config.php for the current organisation
require_once( APP_DIR . '/conf/config.php'); // For Languages info

  
$_SESSION["org"] = 'imo';

// Load Default Language if any 
if(defined('DEFAULT_LANGUAGE') && empty($_COOKIE['language'])) {
    // Remove previous cookie
    setcookie("language", null, time()-3600, "/");
    // Set new language cookie
    setcookie("language", DEFAULT_LANGUAGE, time()+60000 , "/");
    $lang_local = $_GET['locale'] =  DEFAULT_LANGUAGE;
} 

// Pull in the necessary classes for use if needed
$paths = array(
    'conf'      => APP_DIR . '/conf',
    'lib'       => APP_DIR . '/system/libraries',
    'models'    => APP_DIR . '/models',
    'helpers'   => APP_DIR . '/helpers',
);


// LOCALE
$locale 	 = !empty($_GET['locale'])?$_GET['locale']:'';			 // Set by URL
$lang_cookie = !empty($_COOKIE['language'])?$_COOKIE['language']:''; // Set by cookie
  
 
// By Cookie 
if(empty($locale) && !empty($lang_cookie)) {
	$lang_local = $lang_cookie;
} 
// By URL
elseif(!empty($locale)) {
	$lang_local = $locale;
}
// By Default
else {
	/************************/
	/* Test default Browser */
	/************************/
	
	// All languages defined in config.php
	$languages = unserialize(LANGUAGES);  
	
	// Build array of supported languages based on LANGUAGES defined in config.php
	$supported_languages = array();
	foreach($languages as $language) {
		foreach($language['PossibleValues'] as $lang) {
			$supported_languages[] = $lang;
		}
		unset($lang);
	}
	
	// Get default browser language
	$browser_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	
	// Test if default browser language is supported 
	if(in_array($browser_language, $supported_languages)) {
		
		// If the default browser language is supported
		// we're looking for the current "Cookie" value in $languages
		foreach($languages as $lang) {
			if(	in_array($browser_language, $lang['PossibleValues'])) {
				$lang_local = $lang['Cookie'];
				break;
			}
		}
	}
	
}

// If we didn't find anything: default language is english
if(empty($lang_local)) {
	$lang_local = "en_US";
}
	
// Remove previous cookie
setcookie("language", "", time()-3600, "/");
	 
// Set new language cookie
setcookie("language", $lang_local, time()+60000 , "/");

// Setup language for the site
if (!empty($lang_local) ) {
    $domain = 'messages';
    setlocale(LC_MESSAGES, $lang_local);
    putenv("LANG=" . $lang_local);
    bindtextdomain($domain, APP_DIR . '/locale');
    textdomain($domain);
    bind_textdomain_codeset($domain, 'UTF-8');
}


// END LANGUAGES

//if FROM_WORDPRESS isn't defined, you're loading from the framework
//if it is, you're loading from wordpress
if (!defined('FROM_WORDPRESS')) {

    foreach ($paths as $path) {
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    }

    require_once('config.php');

    spl_autoload_extensions(".class.php,.php");
    spl_autoload_register();

    if(!defined('STARTUP_ONLY')) {
        require_once('loadPage.php');
    }

} else {

    foreach ($paths as $path) {
        foreach (glob( $path . '/*.php') as $filename) {
            include_once( $filename );
        }
    }

    unset($paths, $path, $filename);

}
 
// Poor Man's WAF
if (!defined('FROM_CRON')) {
	poorWAF::hacker_check();
}
?>
