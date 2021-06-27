<?php

abstract class Output extends Overloader
{

    protected $output;

    protected $content;

    protected $observers = array();

    final public function out()
    {

        $this->notifyObservers('print', $this);

        $this->wrap();

        $this->sendHeaders();

        print $this->output;

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


    protected function wrap()
    {
        $this->output = $this->content;
    }


    protected function sendHeaders()
    {
    }


    public function addContent($content)
    {
        $this->content .= $content;
    }


    public function addObserver(&$obj)
    {
        $this->observers[] = $obj;
    }


    public function sendStatus($code)
    {
        header(' ', true, $code);
    }

    public function __call($func, $args)
    {
        if (substr($func, 0, 3) == 'add' && strlen($func) > 3 && !isset($args[2])) {

            if (isset($args[1])) {
                $this->data[strtolower(substr($func, 3))][$args[0]] = $args[1];
            } else {
                $this->data[strtolower(substr($func, 3))][] = $args[0];
            }

        }
    }

}
