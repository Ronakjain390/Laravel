<?php

if (!function_exists('isMobileUserAgent')) {
    function isMobileUserAgent($userAgent)
    {
        // Define an array of mobile device identifiers
        $mobileIdentifiers = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'Windows Phone'
        ];

        // Check if the User-Agent contains any of the mobile identifiers
        foreach ($mobileIdentifiers as $identifier) {
            if (strpos($userAgent, $identifier) !== false) {
                return true;
            }
        }

        return false;
    }
}
