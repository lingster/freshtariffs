<?php

/**
 * File: Utils.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */
class Utils
{
    public static function formatTemplate($template, array $data) {
        foreach ($data as $key => $value)
        {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }

    public static function getSubdomainURL($subdomain) {
        return $subdomain . '.' . str_replace('http://', '', Config::get('app.url'));
    }

    public static function getSubdomain() {
        $configHost = str_replace('http://', '', Config::get('app.url'));
        return str_replace([$configHost, '.'], '', $_SERVER['HTTP_HOST']);
    }
}