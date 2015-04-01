<?php

include_once 'Configs.php';

class Secure {

    public function secSessionStart() {
        $session_name = 'bestsmart_session_id'; // Set a custom session name
        $secure = SECURE;
        // This stops JavaScript being able to access the session id.
        $httponly = true;
        // Forces sessions to only use cookies.
        if (ini_set('session.use_only_cookies', 1) === FALSE) {
            return "Could not initiate a safe session (ini_set)";
            exit();
        }
        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params($cookieParams ["lifetime"], $cookieParams ["path"], $cookieParams ["domain"], $secure, $httponly);
        // Sets the session name to the one set above.
        session_name($session_name);
        session_start(); // Start the PHP session
        session_regenerate_id(); // regenerated the session, delete the old one.
        return $this;
    }

    public function secSessionDestroy() {
        // Unset all session values
        $_SESSION = array();
        // get session parameters
        $params = session_get_cookie_params();
        // Delete the actual cookie.
        setcookie(session_name(), '', time() - 42000, $params ["path"], $params ["domain"], $params ["secure"], $params ["httponly"]);
        // Destroy session
        session_destroy();
        return $this;
    }

}
