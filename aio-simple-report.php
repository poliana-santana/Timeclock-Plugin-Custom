<?php
global $wpdb;
global $post;
global $current_user;
get_currentuserinfo();
$update_reports_file = plugin_dir_url(__FILE__) . '/inc/update_aio_reports.php';
?>
    <script>
        jQuery(function () {
            jQuery('#aio_pp_start_date').datetimepicker({
                format:'Y/m/d g:i:s A',
                formatTime: 'g:i A'
            });
            jQuery('#aio_pp_end_date').datetimepicker({
                format:'Y/m/d g:i:s A',
                formatTime: 'g:i A'
            });
            jQuery('#aio_pp_start_date_week2').datetimepicker({
                format:'Y/m/d g:i:s A',
                formatTime: 'g:i A'
            });
            jQuery('#aio_pp_end_date_week2').datetimepicker({
                format:'Y/m/d g:i:s A',
                formatTime: 'g:i A'
            });
        });
    </script>
    <?php
echo '
    <div class="controlDiv">
    <h2>Date Range</h2>';
$aio_pp_start_date = date('Y/m/d h:i:s A', strtotime("-2 weeks"));
echo '<strong>From: </strong><input type="text" id="aio_pp_start_date" name="aio_pp_start_date" class="adminInputDate" placeholder="Start Date" value="' . $aio_pp_start_date . '"> <strong>Thru: </strong> ';
$aio_pp_end_date = date('Y/m/d h:i:s A', strtotime("+1 day"));
echo '
    <input type="text" id="aio_pp_end_date" class="adminInputDate" name="aio_pp_end_date" placeholder="End Date" value="' . $aio_pp_end_date . '" >
    <label><strong>Employee : </strong></label>
    <select name= "employee" id="employee">
    <option>Show All</option>';
$users = get_users('fields=all_with_meta');
usort($users, create_function('$a, $b', 'if($a->last_name == $b->last_name) { return 0;} return ($a->last_name > $b->last_name) ? 1 : -1;'));
foreach (array_filter($users, 'aio_filter_roles') as $user) {
    echo '<option value="' . $user->ID . '">' . $user->user_lastname . ", " . $user->user_firstname . '</option>';
}
echo '</select>
    <a id="aio_generate_report" href="' . $link . '" class="button-primary" data-nonce="' . $nonce . '" >Submit</a>
    </div>
    <div id="report-response" style="display:none;padding:40px;"></div>
    <div id="aio-reports-results" style="display:none;"></div>
    ';