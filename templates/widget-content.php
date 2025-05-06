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
if ($eprofile_page != null){
    $profile_button = '<button class="clock-button aioUserButton" href="' . esc_url(get_permalink($eprofile_page)) . '">' . esc_attr_x("Employee Profile", 'aio-time-clock-lite').'</button>';
}

if (is_user_logged_in()){
    $template .= 
    '<div id="aio_time_clock_widget">
        <div class="widget-main">
            <div class="widget-greeting">
                <h1>Hi, ' . esc_attr($current_user->user_firstname) . ' ' . esc_attr($current_user->user_lastname) . '!</h1>
                <p id="clockMessage"></p>
                <div class="current-time">
                    <span id="jsTimer"><strong>' . esc_attr_x('Current Time', 'aio-time-clock-lite') . ':</strong></span>
                </div>
            </div>
            <div class="widget-buttons">
                <button id="aio_clock_button" class="clock-button" href="' . esc_url($link) . '"><div class="aio-spinner"></div></button>
                <button style="display:none;" id="newShift" class="clock-button clock_in" href="' . esc_url(get_permalink($tc_page)) .'"> ' . esc_attr_x("New Shift", 'aio-time-clock-lite') . '</button>
                ' . $profile_button . '
                <button class="clock-button aioUserButton" href="' . esc_url(wp_logout_url()) . '">' . esc_attr_x("Logout", 'aio-time-clock-lite').'</button>
                <input type="hidden" name="clock_action" id="clock_action">
                <input type="hidden" name="open_shift_id" id="open_shift_id">
            </div>
        </div>
        <div class="shift-details">
            <h2>' . esc_attr_x("Shift Details", 'aio-time-clock-lite') . '</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Total Shift Hour</th>
                        <th>On Break</th>
                        <th>Off Break</th>
                        <th>Total Hour</th>
                    </tr>
                </thead>
                <tbody>';
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
                if ($shifts->have_posts()) {
                    while ($shifts->have_posts()) {
                        $shifts->the_post();
                        $custom = get_post_custom(get_the_ID());
                        if (strpos($custom['employee_clock_in_time'][0], $today) !== false) {
                            $template .= '<tr>
                                <td>' . esc_html(date('m / d / Y', strtotime($custom['employee_clock_in_time'][0]))) . '</td>
                                <td>' . esc_html($custom['employee_clock_in_time'][0] ?? '-- : -- : --') . '</td>
                                <td>' . esc_html($custom['employee_clock_out_time'][0] ?? '-- : -- : --') . '</td>
                                <td>' . esc_html($custom['total_shift_time'][0] ?? '-- : -- : --') . '</td>
                                <td>' . esc_html($custom['break_in_time'][0] ?? '-- : -- : --') . '</td>
                                <td>' . esc_html($custom['break_out_time'][0] ?? '-- : -- : --') . '</td>
                                <td>' . esc_html($custom['total_hour'][0] ?? '-- : -- : --') . '</td>
                            </tr>';
                        }
                    }
                    wp_reset_postdata();
                } else {
                    $template .= '<tr>
                        <td>' . date('m / d / Y') . '</td>
                        <td>-- : -- : --</td>
                        <td>-- : -- : --</td>
                        <td>-- : -- : --</td>
                        <td>-- : -- : --</td>
                        <td>-- : -- : --</td>
                        <td>-- : -- : --</td>
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