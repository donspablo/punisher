<?php

class Location
{

    private $observers;

    public function redirect($to = '')
    {

        $this->notifyObservers('redirect');

        header('Location: ' . ADMIN_URI . '?' . $to);
        exit;
    }

    public function notifyObservers($action)
    {

        $method = 'on' . ucfirst($action);

        $params = func_get_args();
        array_shift($params);

        foreach ($this->observers as $obj) {

            if (method_exists($obj, $method)) {

                call_user_func_array(array(&$obj, $method), $params);

            }

        }

    }

    public function cleanRedirect($to = '')
    {

        header('Location: ' . ADMIN_URI . '?' . $to);
        exit;
    }

    public function addObserver(&$obj)
    {
        $this->observers[] = $obj;
    }

}