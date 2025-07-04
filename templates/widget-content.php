<?php 
wp_enqueue_style('timeclock-widget-style', plugins_url('../css/layouts/widget/new-time-clock.css', __FILE__));

$template = "";
global $wordpress;
global $wpdb;
global $current_user;
$profile_button = null;
$tc = new AIO_Time_Clock_Lite_Actions();
$eprofile_page = $tc->check_eprofile_shortcode_lite();
$tc_page = $tc->aio_check_tc_shortcode_lite();
$nonce = wp_create_nonce("clock_in_nonce");
$link = admin_url('admin-ajax.php?action=clock_in_nonce&post_id=' . get_the_ID() . '&nonce=' . esc_attr($nonce));

if (is_user_logged_in()){
    $template .= 
    '<div id="aio_time_clock_widget">
        <div class="widget-main">
            <div class="widget-greeting">
                <h1>Hi, ' . esc_attr($current_user->user_firstname) . '!</h1>
                <p id="clockMessage"></p>
                <p class="current-time">
                    <span id="jsTimer"><strong>' . esc_attr_x('Current Time', 'aio-time-clock-lite') . '</strong></span>
                </p>
            </div>
            <div class="widget-buttons">
                <button id="aio_clock_button" class="clock-button" href="' . esc_url($link) . '"><div class="aio-spinner"></div></button>
                <button style="display:none;" id="newShift" class="clock-button clock_in" href="' . esc_url(get_permalink($tc_page)) .'"> ' . esc_attr_x("New Shift", 'aio-time-clock-lite') . '</button>
                <button id="aio_break_in_button" style="display:none;" class="clock-button break_in">' . esc_attr_x("On Break", 'aio-time-clock-lite') . '</button>
                <button id="aio_break_out_button" style="display:none;" class="clock-button break_out">' . esc_attr_x("Off Break", 'aio-time-clock-lite') . '</button>
                <input type="hidden" name="clock_action" id="clock_action">
                <input type="hidden" name="open_shift_id" id="open_shift_id">
                <input type="hidden" name="wage_enabled" value="' . esc_attr(get_option("aio_wage_manage")) . '">
                <input type="hidden" name="employee" id="employee" value="' . esc_attr($current_user->ID) . '">
            </div>
        </div>
        <div class="shift-details">
            <h2>' . esc_attr_x("Shift Details", 'aio-time-clock-lite') . '</h2>
            <table class="shift-summary">
                <thead>
                    <tr>
                        <th colspan="4">Date: <span id="shift-date">' . date('m / d / Y') . '</span></th>
                    </tr>
                </thead>
                <tbody id="shift-details-body">';
                $today = date('Y-m-d');
                $shifts = new WP_Query(array(
                    'post_type' => 'shift',
                    'author' => $current_user->ID,
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'employee_clock_in_time',
                            'value' => $today,
                            'compare' => 'LIKE'
                        )
                    )
                ));
                $break_in_time = null;
                $break_out_time = null;
                if ($shifts->have_posts()) {
                    while ($shifts->have_posts()) {
                        $shifts->the_post();
                        $custom = get_post_custom(get_the_ID());
                        $break_in_time = $custom['break_in_time'][0] ?? null;
                        $break_out_time = $custom['break_out_time'][0] ?? null;
                        $shift_duration = $tc->secondsToTime($tc->getShiftTotal(get_the_ID()));

                        $clock_in = !empty($custom['employee_clock_in_time'][0]) ? date('h:i A', strtotime($custom['employee_clock_in_time'][0])) : '-- : -- : --';
                        $clock_out = !empty($custom['employee_clock_out_time'][0]) ? date('h:i A', strtotime($custom['employee_clock_out_time'][0])) : '-- : -- : --';
                        $break_in = !empty($break_in_time) ? date('h:i A', strtotime($break_in_time)) : '-- : -- : --';
                        $break_out = !empty($break_out_time) ? date('h:i A', strtotime($break_out_time)) : '-- : -- : --';

                        $template .= '
                        <tr>
                            <td>Clock In</td>
                            <td>' . esc_html($clock_in) . '</td>
                            <td>On Break</td>
                            <td>' . esc_html($break_in) . '</td>
                        </tr>
                        <tr>
                            <td>Clock Out</td>
                            <td>' . esc_html($clock_out) . '</td>
                            <td>Off Break</td>
                            <td>' . esc_html($break_out) . '</td>
                        </tr>
                        <tr>
                            <td colspan="4">Shift Duration: <span>' . esc_html($shift_duration ?? '-- : -- : --') . '</span></td>
                        </tr>';
                    }
                    wp_reset_postdata();
                } else {
                    $template .= '
                    <tr>
                        <td>Clock In</td>
                        <td>-- : -- : --</td>
                        <td>On Break</td>
                        <td>-- : -- : --</td>
                    </tr>
                    <tr>
                        <td>Clock Out</td>
                        <td>-- : -- : --</td>
                        <td>Off Break</td>
                        <td>-- : -- : --</td>
                    </tr>
                    <tr>
                        <td colspan="4">Shift Duration: <span>-- : -- : --</span></td>
                    </tr>';
                }
                $template .= '</tbody>
            </table>
        </div>
    </div>';
}
else{
    $template .= 
    '<div id="aio_time_clock_widget">
        <p>' . esc_attr_x('You must be logged in to use the time clock', 'aio-time-clock-lite') . '.</p>
        <a href="' . esc_url(wp_login_url()) . '"><button>' . esc_attr_x("Login", 'aio-time-clock-lite') . '</button></a>
    </div>';
}

echo $template;