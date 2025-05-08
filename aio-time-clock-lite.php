<?php
/**
 * Plugin Name: Custom All in One Time Clock Lite
 * Plugin URI:  https://codebangers.com/product/all-in-one-time-clock-lite/
 * Description: Custom version of All in One Time Clock Lite with break time tracking and UI changes.
 * Author:      Codebangers, Poliana Santana
 * Author URI:  https://github.com/poliana-santana/Timeclock-Plugin-Custom
 * Version:     2.0.1
 */
class AIO_Time_Clock_Plugin_Lite
{
    static function init()
    {
        require_once("aio-time-clock-lite-actions.php");       
        $aio_tcl_actions = new AIO_Time_Clock_Lite_Actions();
        $aio_tcl_actions->setup();
    }
}

AIO_Time_Clock_Plugin_Lite::init();