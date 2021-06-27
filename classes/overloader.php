<?php

class Overloader
{

    protected $data;


    public function __get($name)
    {
        $name = strtolower($name);
        return isset($this->data[$name]) ? $this->data[$name] : '';
    }


    public function __set($name, $value)
    {
        $this->data[strtolower($name)] = $value;
    }

}