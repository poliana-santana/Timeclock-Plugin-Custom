<?php
/**
 * Shared date range logic for employee/admin reports.
 */

function aio_get_year_ranges($current_year = null) {
    // "Year" is July 1 to June 30 of next year
    $now = new DateTime();
    // Always use an integer for $year
    $year = isset($current_year) && is_numeric($current_year)
        ? intval($current_year)
        : ($now->format('n') >= 7 ? intval($now->format('Y')) : intval($now->format('Y')) - 1);
    $ranges = [];

    // Main year
    $ranges['year'] = [
        'label' => "Year ($year-" . ($year+1) . ")", // Use hyphen-minus
        'start' => "$year-07-01",
        'end'   => ($year+1) . "-06-30"
    ];

    // Quarters
    $ranges['q1'] = [
        'label' => "Q1 (Jul–Sep $year)",
        'start' => "$year-07-01",
        'end'   => "$year-09-30"
    ];
    $ranges['q2'] = [
        'label' => "Q2 (Oct–Dec $year)",
        'start' => "$year-10-01",
        'end'   => "$year-12-31"
    ];
    $ranges['q3'] = [
        'label' => "Q3 (Jan–Mar " . ($year+1) . ")",
        'start' => ($year+1) . "-01-01",
        'end'   => ($year+1) . "-03-31"
    ];
    $ranges['q4'] = [
        'label' => "Q4 (Apr–Jun " . ($year+1) . ")",
        'start' => ($year+1) . "-04-01",
        'end'   => ($year+1) . "-06-30"
    ];

    return $ranges;
}

/**
 * Returns [$date_start, $date_end] based on input and ranges.
 * $input is typically $_GET or $_POST.
 */
function aio_get_selected_date_range($input, $ranges) {
    $selected = $input['aio_range_type'] ?? 'year';
    $custom_start = $input['aio_custom_start'] ?? '';
    $custom_end = $input['aio_custom_end'] ?? '';
    if ($selected === 'custom' && $custom_start && $custom_end) {
        return [$custom_start, $custom_end];
    } elseif (isset($ranges[$selected])) {
        return [$ranges[$selected]['start'], $ranges[$selected]['end']];
    } elseif ($selected === 'all') {
        return ['', ''];
    } else {
        return [null, null];
    }
}

/**
 * Returns a meta_query array for WP_Query if both dates are set, otherwise empty array.
 */
function aio_build_date_meta_query($date_start, $date_end) {
    if ($date_start && $date_end) {
        return [[
            'key' => 'employee_clock_in_time',
            'value' => [$date_start . ' 00:00:00', $date_end . ' 23:59:59'],
            'compare' => 'BETWEEN',
            'type' => 'DATETIME'
        ]];
    }
    return [];
}

function aio_render_date_range_filter($args = []) {
    $action = $args['action'] ?? '';
    $selected = $args['selected'] ?? 'year';
    $custom_start = $args['custom_start'] ?? '';
    $custom_end = $args['custom_end'] ?? '';
    $year = $args['year'] ?? null;
    $ranges = aio_get_year_ranges($year);
    ?>
    <form method="get" action="<?php echo esc_url($action); ?>" id="aio-date-range-filter" style="margin-bottom:20px; display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
        <label for="aio_range_type"><strong>Date Range:</strong></label>
        <select name="aio_range_type" id="aio_range_type">
            <option value="year" <?php selected($selected, 'year'); ?>><?php echo esc_html($ranges['year']['label']); ?></option>
            <option value="q1" <?php selected($selected, 'q1'); ?>><?php echo esc_html($ranges['q1']['label']); ?></option>
            <option value="q2" <?php selected($selected, 'q2'); ?>><?php echo esc_html($ranges['q2']['label']); ?></option>
            <option value="q3" <?php selected($selected, 'q3'); ?>><?php echo esc_html($ranges['q3']['label']); ?></option>
            <option value="q4" <?php selected($selected, 'q4'); ?>><?php echo esc_html($ranges['q4']['label']); ?></option>
            <option value="all" <?php selected($selected, 'all'); ?>>All</option>
            <option value="custom" <?php selected($selected, 'custom'); ?>>Custom</option>
        </select>
        <span id="aio-custom-range-fields" style="display:<?php echo ($selected == 'custom') ? 'inline' : 'none'; ?>">
            <input type="date" name="aio_custom_start" value="<?php echo esc_attr($custom_start); ?>" placeholder="Start Date" />
            <input type="date" name="aio_custom_end" value="<?php echo esc_attr($custom_end); ?>" placeholder="End Date" />
        </span>
        <button type="submit">Filter</button>
    </form>
    <script>
    // Show/hide custom date fields
    document.addEventListener('DOMContentLoaded', function() {
        var sel = document.getElementById('aio_range_type');
        var customFields = document.getElementById('aio-custom-range-fields');
        sel.addEventListener('change', function() {
            if (this.value === 'custom') {
                customFields.style.display = 'inline';
            } else {
                customFields.style.display = 'none';
            }
        });
    });
    </script>
    <?php
}
