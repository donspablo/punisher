<?php

class Input
{

    public function __construct()
    {

        $this->GET = $this->prepare($_GET);
        $this->POST = $this->prepare($_POST);
        $this->COOKIE = $this->prepare($_COOKIE);

    }

    private function prepare($array)
    {

        $return = array();

        foreach ($array as $key => $value) {
            $return[strtolower($key)] = self::clean($value);
        }

        return $return;

    }


    static public function clean($val)
    {
        switch (true) {
            case is_string($val):

                $val = trim($val);

                break;

            case is_array($val):

                $val = array_map(array('Input', 'clean'), $val);

                break;

            default:
                return $val;
        }

        return $val;

    }


    public function __get($name)
    {

        if (!isset($name[1])) {
            return NULL;
        }

        $from = strtolower($name[0]);
        $var = strtolower(substr($name, 1));

        $targets = array('g' => $this->GET,
            'p' => $this->POST,
            'c' => $this->COOKIE);

        if (isset($targets[$from][$var])) {
            return $targets[$from][$var];
        }

        return NULL;

    }

}





