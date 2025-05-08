<div>
    <?php
    global $post;
    $custom = get_post_custom($post->ID);
    $orig_clock_in = null;
    $clock_in = null;
    if (isset($custom["employee_clock_in_time"][0]) && $this->isValidDate($custom["employee_clock_in_time"][0])){
        $orig_clock_in = date($this->prettyDateTime, strtotime(sanitize_text_field($custom["employee_clock_in_time"][0])));
        $clock_in = date($this->getDateTimeFormat(), strtotime(sanitize_text_field($custom["employee_clock_in_time"][0])));
    }
    $orig_clock_out = null;
    $clock_out = null;
    if (isset($custom["employee_clock_out_time"][0]) && $this->isValidDate($custom["employee_clock_out_time"][0])){
        $orig_clock_out = date($this->prettyDateTime, strtotime(sanitize_text_field($custom["employee_clock_out_time"][0])));
        $clock_out = date($this->getDateTimeFormat(), strtotime(sanitize_text_field($custom["employee_clock_out_time"][0])));
    }
    $location = null;
    if (isset($custom["location"][0])){
        $location = sanitize_text_field($custom["location"][0]);
    }
    $ip_address_in = null;
    if (isset($custom["ip_address_in"][0])){
        $ip_address_in = sanitize_text_field($custom["ip_address_in"][0]);
    }
    $ip_address_out = null;
    if (isset($custom["ip_address_out"][0])){
        $ip_address_out = sanitize_text_field($custom["ip_address_out"][0]);
    }    
    $break_in = null;
    if (isset($custom["break_in_time"][0]) && $this->isValidDate($custom["break_in_time"][0])){
        $break_in = date($this->getDateTimeFormat(), strtotime(sanitize_text_field($custom["break_in_time"][0])));
    }
    $break_out = null;
    if (isset($custom["break_out_time"][0]) && $this->isValidDate($custom["break_out_time"][0])){
        $break_out = date($this->getDateTimeFormat(), strtotime(sanitize_text_field($custom["break_out_time"][0])));
    }
    $employee_id = get_post_field( 'post_author', $post->ID );
    $recent_author = get_user_by( 'ID', $employee_id );
    $employee_name = sanitize_text_field($recent_author->last_name) . ", " . sanitize_text_field($recent_author->first_name);
    $selected_employee = "";
    ?>
     <table class="widefat fixed" cellspacing="0">
        <tr class="alternate">
            <th scope="col" class="manage-column column-columnname"><strong><?php echo esc_attr_x('Employee', 'aio-time-clock-lite'); ?>: </strong></th>
            <td>
                <?php 
                    if ($employee_id != null){
                        $selected_employee = intval($employee_id);
                        echo esc_attr($this->getEmployeeName($employee_id));
                    }
                ?>
            </td>
            <td>
                <a class="button" onclick="editEmployee()" title="<?php echo esc_attr_x('Edit Employee', 'aio-time-clock-lite'); ?>"><span class="dashicons dashicons-admin-users vmiddle"></span></a>
                <select id="employee_id" name="employee_id" style="display:none;">
                    <?php 
                        $this->getEmployeeSelect($selected_employee);
                    ?>
                </select>
            </td>
        </tr>
        <tr class="">
            <th scope="col" class="manage-column column-columnname"><strong><?php echo esc_attr_x('IP Address', 'aio-time-clock-lite'); ?>: </strong></th>
            <td>
                <strong><?php echo esc_attr_x('In', 'aio-time-clock-lite'); ?>: </strong>
                <?php 
                    if ($ip_address_in != null){
                        echo esc_attr($ip_address_in); 
                    }
                ?>
                <br />
                <strong><?php echo esc_attr_x('Out', 'aio-time-clock-lite'); ?>: </strong>
                <?php 
                    if ($ip_address_out != null){
                        echo esc_attr($ip_address_out);
                    }
                ?>
            </td>
            <td>
            </td>
        </tr>
        <tr class="alternate">
            <th scope="col" class="manage-column column-columnname"><strong><?php echo esc_attr_x('Clock In', 'aio-time-clock-lite'); ?>: </strong></th>
            <td>
                <?php 
                    if ($clock_in != null){
                        echo esc_attr($clock_in); 
                    }
                ?>
            </td>
            <td>
                <a class="button" onclick="editClockTime('in')" title="<?php echo esc_attr_x('Edit Clock In', 'aio-time-clock-lite'); ?>"><span class="dashicons dashicons-clock vmiddle"></span></a>                
                <input type="text" class="adminInputDate" id="clock_in" name="clock_in" style="display:none;" value="<?php echo esc_attr($orig_clock_in); ?>" autocomplete="off"/>
            </td>
        </tr>
        <tr class="">
            <th scope="col" class="manage-column column-columnname"><strong><?php echo esc_attr_x('Clock Out', 'aio-time-clock-lite'); ?>: </strong></th>
            <td>
                <?php 
                    if ($clock_out != null){
                        echo esc_attr($clock_out);
                    }
                ?>
            </td>
            <td>
                <a class="button" onclick="editClockTime('out')" title="<?php echo esc_attr_x('Edit Clock Out', 'aio-time-clock-lite'); ?>"><span class="dashicons dashicons-clock vmiddle"></span></a>                
                <input type="text" class="adminInputDate" id="clock_out" name="clock_out" style="display:none;" value="<?php echo esc_attr($orig_clock_out); ?>" autocomplete="off"/>
            </td>
        </tr>
        <tr class="">
            <th scope="col" class="manage-column column-columnname"><strong><?php echo esc_attr_x('On Break', 'aio-time-clock-lite'); ?>: </strong></th>
            <td>
                <?php 
                if ($break_in != null) {
                    echo esc_attr($break_in); 
                }
                ?>
            </td>
            <td>
                <a class="button" onclick="editClockTime('break_in')" title="<?php echo esc_attr_x('Edit On Break', 'aio-time-clock-lite'); ?>"><span class="dashicons dashicons-clock vmiddle"></span></a>                
                <input type="text" class="adminInputDate" id="break_in" name="break_in" style="display:none;" value="<?php echo esc_attr($break_in); ?>" autocomplete="off"/>
            </td>
        </tr>
        <tr class="alternate">
            <th scope="col" class="manage-column column-columnname"><strong><?php echo esc_attr_x('Off Break', 'aio-time-clock-lite'); ?>: </strong></th>
            <td>
                <?php 
                if ($break_out != null) {
                    echo esc_attr($break_out);
                }
                ?>
            </td>
            <td>
                <a class="button" onclick="editClockTime('break_out')" title="<?php echo esc_attr_x('Edit Off Break', 'aio-time-clock-lite'); ?>"><span class="dashicons dashicons-clock vmiddle"></span></a>                
                <input type="text" class="adminInputDate" id="break_out" name="break_out" style="display:none;" value="<?php echo esc_attr($break_out); ?>" autocomplete="off"/>
            </td>
        </tr>
        <tr class="alternate">
            <th scope="col" class="manage-column column-columnname"><strong><?php echo esc_attr_x('Total Shift Time', 'aio-time-clock-lite'); ?>: </strong></th>
            <td>
                <?php 
                if ($clock_in != null && $clock_out != null) {
                    $total_shift_time = $this->dateDifference($clock_in, $clock_out);

                    // Subtract break duration if both break_in and break_out exist
                    if ($break_in != null && $break_out != null) {
                        $break_duration = $this->dateDifference($break_in, $break_out);
                        $total_shift_time -= $break_duration;
                    }

                    echo esc_attr($this->secondsToTime($total_shift_time));
                } else {
                    echo esc_attr($this->secondsToTime(0));
                }
                ?>
            </td>
            <td>
                <i></i>
            </td>
        </tr>        
    </table>
</div>