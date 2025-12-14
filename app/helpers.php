<?php

use Carbon\Carbon;

if (!function_exists('formatDate')) {
    /**
     * Format a date to Standard Gregorian.
     * Replaces Jalali functionality.
     * 
     * @param mixed $date Date to convert (Carbon, DateTime, string, or timestamp)
     * @param string|null $format Optional format string (e.g., 'Y/m/d' or '%d %B %Y')
     * @return \Carbon\Carbon|string
     */
    function formatDate($date = null, $format = null)
    {
        try {
            if ($date === null) {
                $carbon = Carbon::now();
            } elseif ($date instanceof \DateTimeInterface) {
                $carbon = Carbon::instance($date);
            } elseif (is_numeric($date)) {
                $carbon = Carbon::createFromTimestamp($date);
            } else {
                $carbon = Carbon::parse($date);
            }
            
            // If format is provided, return formatted string
            if ($format !== null) {
                return $carbon->format($format);
            }
            
            // Otherwise return the Carbon object
            return $carbon;
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format a datetime, Y/m/d H:i
     */
    function formatDateTime($date = null)
    {
        return formatDate($date, 'Y/m/d H:i');
    }
}
