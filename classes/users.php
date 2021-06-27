<?php

class User
{

    public $name;

    public $userAgent;

    public $IP;

    public $aborted;

    public function __construct()
    {

        if (session_id() == '') {

            session_name('admin');
            session_start();

        }

        session_regenerate_id();

        $this->userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $this->IP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        $authKey = $this->userAgent . $this->IP;

        if (isset($_SESSION['auth_key'])) {

            if ($_SESSION['auth_key'] != $authKey) {

                $this->clear();
                $this->aborted = 'Session data mismatch.';

            }

        } else {

            $_SESSION['auth_key'] = $authKey;

        }

        # Are we verified?
        if (!empty($_SESSION['verified'])) {
            $this->name = $_SESSION['verified'];
        }

        # Have we expired? Only expire if we're logged in of course...
        if ($this->isAdmin() && isset($_SESSION['last_click']) && $_SESSION['last_click'] < (time() - ADMIN_TIMEOUT)) {
            $this->clear();
            $this->aborted = 'Your session timed out after ' . round(ADMIN_TIMEOUT / 60) . ' minutes of inactivity.';
        }

        # Set last click time
        $_SESSION['last_click'] = time();

    }

    # Log out, destroy all session data
    public function clear()
    {

        # Clear existing
        session_destroy();

        # Unset existing variables
        $_SESSION = array();
        $this->name = false;

        # Restart session
        session_start();

    }

    # Log in, saving username session for future requests

    public function isAdmin()
    {
        return (bool)$this->name;
    }

    # Are we verified or not?

    public function login($name)
    {
        $this->name = $name;
        $_SESSION['verified'] = $name;
    }

}