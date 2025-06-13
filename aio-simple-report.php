<?php
global $wpdb;
global $post;
global $current_user;
$update_reports_file = plugin_dir_url(__FILE__) . '/inc/update_aio_reports.php';

include_once plugin_dir_path(__FILE__) . 'includes/date-range-filter.php';

// Get filter values from GET or fallback to default
$year = null;
$ranges = aio_get_year_ranges($year);
list($date_start, $date_end) = aio_get_selected_date_range($_GET, $ranges);

$range_type = $_GET['aio_range_type'] ?? 'year';
$custom_start = $_GET['aio_custom_start'] ?? '';
$custom_end = $_GET['aio_custom_end'] ?? '';

if ($range_type === 'custom' && $custom_start && $custom_end) {
    $date_start = $custom_start;
    $date_end = $custom_end;
} elseif (isset($ranges[$range_type])) {
    $date_start = $ranges[$range_type]['start'];
    $date_end = $ranges[$range_type]['end'];
} elseif ($range_type === 'all') {
    $date_start = '';
    $date_end = '';
} else {
    $date_start = null;
    $date_end = null;
}

// Render filter UI
?>
<div class="controlDiv">
    <h2><?php echo esc_attr_x('Date Range', 'aio-time-clock-lite'); ?></h2>
    <label for="aio_range_type"><strong>Date Range:</strong></label>
    <select name="aio_range_type" id="aio_range_type">
        <option value="year" <?php selected($range_type, 'year'); ?>><?php echo esc_html($ranges['year']['label']); ?></option>
        <option value="q1" <?php selected($range_type, 'q1'); ?>><?php echo esc_html($ranges['q1']['label']); ?></option>
        <option value="q2" <?php selected($range_type, 'q2'); ?>><?php echo esc_html($ranges['q2']['label']); ?></option>
        <option value="q3" <?php selected($range_type, 'q3'); ?>><?php echo esc_html($ranges['q3']['label']); ?></option>
        <option value="q4" <?php selected($range_type, 'q4'); ?>><?php echo esc_html($ranges['q4']['label']); ?></option>
        <option value="all" <?php selected($range_type, 'all'); ?>>All</option>
        <option value="custom" <?php selected($range_type, 'custom'); ?>>Custom</option>
    </select>
    <span id="aio-custom-range-fields" style="display:<?php echo ($range_type == 'custom') ? 'inline' : 'none'; ?>">
        <input type="date" name="aio_custom_start" id="aio_custom_start" value="<?php echo esc_attr($custom_start); ?>" placeholder="Start Date" />
        <input type="date" name="aio_custom_end" id="aio_custom_end" value="<?php echo esc_attr($custom_end); ?>" placeholder="End Date" />
    </span>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var sel = document.getElementById('aio_range_type');
        var customFields = document.getElementById('aio-custom-range-fields');
        var startInput = document.getElementById('aio_custom_start');
        var endInput = document.getElementById('aio_custom_end');
        var hiddenStart = document.getElementById('aio_pp_start_date');
        var hiddenEnd = document.getElementById('aio_pp_end_date');
        var ranges = <?php echo json_encode($ranges); ?>;

        function updateHiddenDates() {
            var type = sel.value;
            if (type === 'custom') {
                hiddenStart.value = startInput.value;
                hiddenEnd.value = endInput.value;
            } else if (ranges[type]) {
                hiddenStart.value = ranges[type].start;
                hiddenEnd.value = ranges[type].end;
            } else if (type === 'all') {
                hiddenStart.value = '';
                hiddenEnd.value = '';
            }
        }

        sel.addEventListener('change', function() {
            if (this.value === 'custom') {
                customFields.style.display = 'inline';
            } else {
                customFields.style.display = 'none';
            }
            updateHiddenDates();
        });

        if (startInput && endInput) {
            startInput.addEventListener('change', updateHiddenDates);
            endInput.addEventListener('change', updateHiddenDates);
        }

        // Initialize on load
        updateHiddenDates();
    });
    </script>
    <input type="hidden" id="aio_pp_start_date" name="aio_pp_start_date" value="<?php echo esc_attr($date_start); ?>">
    <input type="hidden" id="aio_pp_end_date" name="aio_pp_end_date" value="<?php echo esc_attr($date_end); ?>">
    <label><strong><?php echo esc_attr_x('Employee', 'aio-time-clock-lite'); ?> : </strong></label>
    <select name="employee" id="employee">
        <option value=""><?php echo esc_attr_x('Show All', 'aio-time-clock-lite'); ?></option>
        <?php echo $this->getEmployeeSelect(); ?>
    </select>
    <a id="aio_generate_report" href="<?php echo esc_url($link); ?>" class="button-primary"><?php echo esc_attr_x('Submit', 'aio-time-clock-lite'); ?></a>
    <button id="aio_export_csv" class="button" style="margin-left:10px;"><?php echo esc_attr_x('Export to CSV', 'aio-time-clock-lite'); ?></button>
</div>
<div id="report-response" style="display:none;padding:40px;"></div>
<div id="aio-reports-results" style="display:none;">
    <!-- The report table gets rendered here by JS -->
</div>
<input type="hidden" name="wage_enabled" id="wage_enabled" value="<?php echo esc_attr(get_option("aio_wage_manage")); ?>">