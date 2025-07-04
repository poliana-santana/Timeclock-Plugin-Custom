<?php
global $wordpress;
global $wpdb;
global $current_user;
$timeclock_button = null;
$count = 0;
$shift_total_time = "00:00";
include_once plugin_dir_path(__FILE__) . 'includes/date-range-filter.php';

if (is_user_logged_in() == true) {
    wp_get_current_user();

    // Get filter values from GET
    $year = null; // Optionally allow year selection
    $ranges = aio_get_year_ranges($year);
    list($date_start, $date_end) = aio_get_selected_date_range($_GET, $ranges);

    // Render filter UI
    $selected = $_GET['aio_range_type'] ?? 'year';
    $custom_start = $_GET['aio_custom_start'] ?? '';
    $custom_end = $_GET['aio_custom_end'] ?? '';
    aio_render_date_range_filter([
        'action' => '', // current page
        'selected' => $selected,
        'custom_start' => $custom_start,
        'custom_end' => $custom_end,
        'year' => $year,
    ]);
    ?>
    <table>
    <!--<input type="text" id="employeeProfileInput" onkeyup="employeProfileSearch()" placeholder="Filter shifts..">-->

    <table id="employeeProfileTable">
    <tr class="header">
        <th style="width:20%;"><?php echo esc_attr_x('Date', 'aio-time-clock-lite'); ?></th>
        <th style="width:20%;"><?php echo esc_attr_x('Clock In', 'aio-time-clock-lite'); ?></th>
        <th style="width:20%;"><?php echo esc_attr_x('Clock Out', 'aio-time-clock-lite'); ?></th>
        <th style="width:20%;"><?php echo esc_attr_x('On Break', 'aio-time-clock-lite'); ?></th>
        <th style="width:20%;"><?php echo esc_attr_x('Off Break', 'aio-time-clock-lite'); ?></th>
        <th style="width:20%;"><?php echo esc_attr_x('Total', 'aio-time-clock-lite'); ?></th>
    </tr>
    <?php
    // Build meta_query for date range
    $meta_query = aio_build_date_meta_query($date_start, $date_end);
    $args = [
        'post_type' => 'shift',
        'author' => $current_user->ID,
        'posts_per_page' => -1,
    ];
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }
    $loop = new WP_Query($args);
    while ($loop->have_posts()): $loop->the_post();
        $custom = get_post_custom($loop->post->ID);
        $employee_clock_in_time = isset($custom["employee_clock_in_time"][0]) ? sanitize_text_field($custom["employee_clock_in_time"][0]) : null;
        $employee_clock_out_time = isset($custom["employee_clock_out_time"][0]) ? sanitize_text_field($custom["employee_clock_out_time"][0]) : null;
        $break_in_time = isset($custom["break_in_time"][0]) ? sanitize_text_field($custom["break_in_time"][0]) : null;
        $break_out_time = isset($custom["break_out_time"][0]) ? sanitize_text_field($custom["break_out_time"][0]) : null;
        $shift_date = $employee_clock_in_time ? date('Y-m-d', strtotime($employee_clock_in_time)) : esc_attr_x('N/A', 'aio-time-clock-lite');
        $shift_sum = '00:00';
        ?>
        <tr valign="top">
            <td scope="row"><?php echo esc_attr($shift_date); ?></td>
            <td>
                <?php
                if ($employee_clock_in_time != null) {
                    echo esc_attr(date('h:i A', strtotime($employee_clock_in_time)));
                } else {
                    echo esc_attr_x('Clock In Empty', 'aio-time-clock-lite');
                }
                ?>
            </td>
            <td>
                <?php
                if ($employee_clock_out_time != null) {
                    echo esc_attr(date('h:i A', strtotime($employee_clock_out_time)));
                } else {
                    echo esc_attr_x('Clock Out Empty', 'aio-time-clock-lite');
                }
                ?>
            </td>
            <td>
                <?php
                if ($break_in_time != null) {
                    echo esc_attr(date('h:i A', strtotime($break_in_time)));
                } else {
                    echo esc_attr_x('-', 'aio-time-clock-lite');
                }
                ?>
            </td>
            <td>
                <?php
                if ($break_out_time != null) {
                    echo esc_attr(date('h:i A', strtotime($break_out_time)));
                } else {
                    echo esc_attr_x('-', 'aio-time-clock-lite');
                }
                ?>
            </td>
            <td>
                <?php 
                    if ($employee_clock_in_time != null && $employee_clock_out_time != null) {
                        $shift_sum = $this->secondsToTime($this->getShiftTotal(get_the_ID()));
                        $shift_total_time = $this->addTwoTimes($shift_total_time, $shift_sum);
                        echo esc_attr($shift_sum);
                    }
                ?>
            </td>
        </tr>
    <?php $count++;
    endwhile;
    ?>
    <tr><td></td><td></td><td></td><td></td><td><strong><?php echo esc_attr_x('Total Shift Time', 'aio-time-clock-lite'); ?>:</strong> </td><td><?php echo esc_attr($shift_total_time); ?></td></tr>
    </table>

    <style>
    #employeeProfileInput {
    background-image: url(<?php echo plugins_url( '/images/search.png', __FILE__ ); ?>); /* Add a search icon to input */
    background-position: 10px 12px; /* Position the search icon */
    background-repeat: no-repeat; /* Do not repeat the icon image */
    width: 100%; /* Full-width */
    font-size: 16px; /* Increase font-size */
    padding: 12px 20px 12px 40px; /* Add some padding */
    border: 1px solid #ddd; /* Add a grey border */
    margin-bottom: 12px; /* Add some space below the input */
    }

    #employeeProfileTable {
    border-collapse: collapse; /* Collapse borders */
    width: 100%; /* Full-width */
    border: 1px solid #ddd; /* Add a grey border */
    font-size: 18px; /* Increase font-size */
    }

    #employeeProfileTable th, #employeeProfileTable td {
    text-align: left; /* Left-align text */
    padding: 12px; /* Add padding */
    }

    #employeeProfileTable tr {
    /* Add a bottom border to all table rows */
    border-bottom: 1px solid #ddd; 
    }

    #employeeProfileTable tr.header, #employeeProfileTable tr:hover {
    /* Add a grey background color to the table header and on hover */
    background-color: #f1f1f1;
    }
    </style>
    <?php 
}
else{
    echo esc_attr_x("You must be logged in to view the Employee Profile Page", 'aio-time-clock-lite');
}
?>