<?php
/**
 * Here is your custom functions.
 */

/**
 * 下划线转驼峰
 */
function convert($str, $ucfirst = true)
{
    $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
        return strtoupper($matches[2]);
    }, $str);
    return $ucfirst ? ucfirst($str) : $str;
}

/**
 * 驼峰转下划线
 */
function reconvert($str)
{
    $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
        return '_' . strtolower($matches[0]);
    }, $str);
    return $str;
}
