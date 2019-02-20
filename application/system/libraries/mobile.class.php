<?php

/**
 * @author     Derek Sanford
 * @copyright  (c) 2011 servalphp.com
 */

class Mobile {
    /*
    * redirect based on mobile user agent
    */

    function redirect($url = 'http://www.servalphp.com/') {
        /*
        if (self::mobile_get_user_agent()) {
            header('Cache-Control: no-transform');
            header('Vary: User-Agent, Accept');
            header("robots:noindex,nofollow");
            header('Location: ' . $url);
            exit;
        }
        */
    }

    /**
    * get our mobile agent - returns key of agent array
    */

    function mobile_get_user_agent() {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $accept     = strtolower($_SERVER['HTTP_ACCEPT']);

        // array of agents that we need to test against

        $agents = array(
            'iphone'     => array('ipod', 'iphone'),
            'blackberry' => 'blackberry',
            'android'    => 'android',
            'opera_mini' => 'opera mini',
            'blackberry' => 'blackberry',
            'nokia'      => 'nokia',
            'wap'        => array('text/vnd.wap.wml', 'application/vnd.wap.xhtml+xml'),
            'palm'       => array('up.browser', 'up.link', 'mmp', 'symbian', 'smartphone', 'midp', 'wap',
            'vodafone', 'o2', 'pocket', 'kindle', 'mobile', 'pda', 'psp', 'treo')
        );

        // loop over our agents and see if we have a match
        foreach ($agents as $key => $terms) {
            if (is_array($terms)) {
                foreach ($terms as $term) {
                    if (strstr($user_agent, $term)) {
                        return $key;
                    }
                }
            } else {
                if (strstr($user_agent, $terms)) {
                    return $key;
                }
            }
        }

        // we need to test for unknown mobile devices
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return 'UNKNOWN';
        }

        return false;
    }
}

