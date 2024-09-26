<?php

if (!function_exists('storage')) {
    function storage()
    {
        if (class_exists('\Scrawler\App')) {
            if (!\Scrawler\App::engine()->has('storage')) {
                \Scrawler\App::engine()->register('storage', new Scrawler\Storage());
            }
            return \Scrawler\App::engine()->storage();
        }
        return new Scrawler\Storage();
    }

}

if (!function_exists('storage_url')) {
    function storage_url($path)
    {
        return storage()->getUrl($path);
    }
}