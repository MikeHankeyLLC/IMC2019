<?php

class Auth {

//binds only needs to contain a user id if reload is false
//or an email and password if reload is true for a site login
//or just an email if reload is true and it's a facebook login
    public function check_user($binds, $reload = false) {
        if ($reload) {
            $result = Users_Model::get_user_details($binds);
        } else {
            $result = Users_Model::get_user_auth_details($binds);
        }

        if (!empty($result)) {

            $result['auth_type'] = $binds['auth_type'];

            if ( !empty($binds['access_token']) ) {
                $result['access_token'] = $binds['access_token'];
            }

            $session = md5(date("YmdHis") . $result['email'] . rand(99999,999999));

            if ($reload) {
                $session = Cookie::get('sc_381');
            } else {
                Cookie::set('sc_381', $session, false);
            }

            $_SESSION[$session] = $result;

            return true;
        }

        return false;
    }

    public function admin_auth_required() {
        if(!empty($_SESSION[Cookie::get('sc_381')]) && $this->user_auth_details['user_type'] == 1) {
            return true;
        }

        redirect(BASE_URL);
    }

    //if page is set, the user will be redirected to the page he/she was trying to get to
    public function user_auth_required($page = "") {
        if (!isset($_SESSION[Cookie::get('sc_381')]) || !$_SESSION[Cookie::get('sc_381')]) {
            redirect('/members/user/login?redirect=/members' . $page);
        } else {
            // Update the cookie time;
            $sc = Cookie::get('sc_381');
            //Cookie::set('sc_381', $sc, true);
            Cookie::set('sc_381', $sc, false);

            unset ($sc);
        }

        return true;
    }

    public static function is_user_logged_in() {
        if (!isset($_SESSION[Cookie::get('sc_381')]) || !$_SESSION[Cookie::get('sc_381')]) {
           return false;
        }

        return true;
    }

}
