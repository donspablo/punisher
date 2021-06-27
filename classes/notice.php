<?php

class Notice
{

    # Storage of messages
    private $data = array();

    # Type of notice handler
    private $type;

    # Constructor fetches any stored from session and clears session
    public function __construct($type)
    {

        # Save type
        $this->type = $type;

        # Array key
        $key = 'notice_' . $type;

        # Any existing?
        if (isset($_SESSION[$key])) {

            # Extract
            $this->data = $_SESSION[$key];

            # And clear
            unset($_SESSION[$key]);

        }

    }

    # Get messages
    public function get($id = false)
    {

        # Requesting an individual message?
        if ($id !== false) {
            return isset($this->data[$id]) ? $this->data[$id] : false;
        }

        # Requesting all
        return $this->data;

    }

    # Add message
    public function add($msg, $id = false)
    {

        # Add with or without an explicit key
        if ($id) {
            $this->data[$id] = $msg;
        } else {
            $this->data[] = $msg;
        }

    }

    # Do we have any messages?
    public function hasMsg()
    {
        return !empty($this->data);
    }

    # Observer the print method of output
    public function onPrint($output)
    {

        $funcName = 'add' . $this->type;

        # Add our messages to the output object
        foreach ($this->data as $msg) {
            $output->{$funcName}($msg);
        }

    }

    # Observe redirects - store notices in session
    public function onRedirect()
    {
        $_SESSION['notice_' . $this->type] = $this->data;
    }

}
