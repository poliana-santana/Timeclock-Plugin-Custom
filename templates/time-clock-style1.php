<?php 
$template = "";
global $wordpress;
global $wpdb;
global $current_user;
get_currentuserinfo();
$profile_button = null;
$eprofile_page = check_eprofile_shortcode_lite();
if ($eprofile_page != null){
    $profile_button = '<button class="aioUserButton" href="' . get_permalink($eprofile_page) . '">Employee Profile</button> ';
}

if (is_user_logged_in()){
    $template .= 
    '<div id="aio_time_clock">
        <div class="aio_form">
            <form class="login-form">
                <h2>' . $current_user->user_firstname . ' ' . $current_user->user_lastname . '</h2>
                <p id="clockMessage"></p>
                <p id="jsTimer"><strong>'.__('Current Time:').' </strong></p>
                <button id="aio_clock_button" href="' . $link . '"><div class="aio-spinner"></div></button>
                <button style="display:none;" id="newShift" class="button clock_in" href="'. get_permalink($tc_page) .'"> New Shift</button>
                <div style="height:20px;"></div>
                '.$profile_button.'
                <div style="height:20px;"></div>
                <button class="aioUserButton" href="' . wp_logout_url() . '">Logout</button>
                <input type="hidden" name="clock_action" id="clock_action">
                <input type="hidden" name="open_shift_id" id="open_shift_id">
            </form>
        </div>
    </div>';
}
else{
    $template .= 
    '<div id="aio_time_clock">
        <div class="aio_form">
            <p>'.__('You must be logged in to use the time clock.').'</p>
            <a href="' . wp_login_url() . '"><button>Login</button></a>
        </div>
    </div>';
}

echo $template;