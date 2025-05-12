<?php

if (!function_exists('str_random')) {
    /**
     * Generate a random string of specified length.
     *
     * @param  int  $length
     * @return string
     */
    function str_random($length = 16)
    {
        return \Illuminate\Support\Str::random($length);
    }
}