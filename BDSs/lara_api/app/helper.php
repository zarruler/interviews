<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 4/7/19
 * Time: 7:21 PM
 */

if (! function_exists('md5token')) {
    function md5token() {
        return md5(rand(1, 10) . microtime());
    }
}
