<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Available date formats
 * The setting value represents the PHP date() formatting, the datepicker value represents the
 * DatePicker formatting (see http://bootstrap-datepicker.readthedocs.io/en/stable/options.html#format)
 *
 * @return array
 */


/**
 * @param $date
 * @return string
 */
function date_to_mysql($date)
{
    $CI = &get_instance();

    $date = DateTime::createFromFormat('d/m/Y', $date);
    return $date->format('Y-m-d');
}

/**
 * Adds interval to user formatted date and returns user formatted date
 * To be used when date is being output back to user.
 *
 * @param $date - user formatted date
 * @param $increment - interval (1D, 2M, 1Y, etc)
 * @return string
 */
function increment_user_date($date, $increment)
{   
    $CI = &get_instance();
    $mysql_date = date_to_mysql($date);

    $today = date("Y-m-d");
    if($today > $mysql_date) { 
        $mysql_date = $today;
    }
    $new_date = new DateTime($mysql_date);
    $new_date->add(new DateInterval('P' . $increment));
   
    return $new_date->format('d/m/Y');
}


/**
 * Adds interval to yyyy-mm-dd date and returns in same format
 *
 * @param $date
 * @param $increment
 * @return string
 */
function increment_date($date, $increment)
{
    $new_date = new DateTime($date);
    $new_date->add(new DateInterval('P' . $increment));
    return $new_date->format('Y-m-d');
}

function increment_time($date, $increment) {
    try {
        $new_date = new DateTime($date);
        $new_date->add(new DateInterval('PT' . $increment));
        return $new_date->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        
    }
        
    
}

function increment_date2($date, $increment) {
    $new_date = new DateTime($date);
    $new_date->add(new DateInterval('P' . $increment));
    return $new_date->format('Y-m-d H:i:s');
}

function dmtoymd($dm) {
    $dm = explode('-', $dm);
    return date('Y') . '-' . $dm[1]  . '-' . $dm[0];
}