<?php

namespace Salesbeat;

class Tools
{
    /**
     * Tools constructor
     * @param object $registry
     */
    public function __construct($registry = null)
    {

    }

    /**
     * @param array $array
     */
    public function printr($array = [])
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }

    /**
     * @param array $array
     */
    public function vardump($array = [])
    {
        echo '<pre>';
        var_dump($array);
        echo '</pre>';
    }

    /**
     * @param string $phone
     * @return string
     */
    public function phoneToTel($phone = '')
    {
        if ($phone) $phone = preg_replace('/[^+0-9]+/', '', $phone);
        return $phone;
    }

    /**
     * @param int $number
     * @param array $suffix
     * @return string
     */
    public function suffixToNumber($number = 0, $suffix = [])
    {
        $keys = [2, 0, 1, 1, 1, 2];
        $mod = $number % 100;
        $suffixKey = ($mod > 7 && $mod < 20) ? 2 : $keys[min($mod % 10, 5)];
        return $number . ' ' . $suffix[$suffixKey];
    }
}