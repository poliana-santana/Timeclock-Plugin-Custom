<div class="wrap aio_admin_wrapper">
    <?php $logo = plugins_url('/images/logo.png', __FILE__);?>
    <a href="https://codebangers.com" target="_blank"><img src="<?php echo $logo; ?>" style="width:15%;"></a>
    <hr>
    <?php
    echo '<div >';
    echo '<h1 style="padding-left: 10px;">Employees Currently Working</h1>';
    echo '<hr>';
    echo '</div>';
    //echo '<i>Do not make add shifts as an admin.  Make a test employee account for yourself and open another browser and clockin on the timeclock page there.  </i>';		?>
    <table class="widefat fixed aio_datatable display" cellspacing="0">
        <thead>
            <tr>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Employee Name'); ?></strong></th>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Department'); ?></strong></th>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Clock In Time'); ?></strong></th>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('IP Address'); ?></strong></th>
                <?php 
                    if (get_option("aio_timeclock_show_shift_notes") == "enabled"){
                        echo '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>'.__( 'Shift Note' ).'</strong></th>';
                    }
                ?>
                <?php do_action("aio_new_report_column_heading"); ?>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Status'); ?></strong></th>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Options'); ?></strong></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Employee Name'); ?></strong></th>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Department'); ?></strong></th>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Clock In Time'); ?></strong></th>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('IP Address'); ?></strong></th>
                <?php 
                    if (get_option("aio_timeclock_show_shift_notes") == "enabled"){
                        echo '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Shift Note</strong></th>';
                    }
                ?>
                <?php do_action("aio_new_report_column_heading"); ?>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Status'); ?></strong></th>
                <th class="manage-column column-columnname" scope="col"><strong><?php _e('Options'); ?></strong></th>
            </tr>
            
        </tfoot>
        <tbody>
        <?php $count = 0; ?>
        <?php $loop = new WP_Query(array('post_type' => 'shift', 'posts_per_page' => -1)); ?>

        <?php while ($loop->have_posts()) : $loop->the_post(); ?>
            <?php
            $custom = get_post_custom($loop->post->ID);
            $employee_clock_in_time = $custom["employee_clock_in_time"][0];
            $employee_clock_out_time = $custom["employee_clock_out_time"][0];
            $ip_address_in = $custom["ip_address_in"][0];
            $shift_note = $custom["shift_notes"][0];
            $location = $custom["location"][0];
            if ($location){
                $location_name = get_the_title($location);
            }
            $clock_in = "";
            $clock_out = "";
            $alternate_class="";
            
            if ($count % 2 == 0) {
                $alternate_class="alternate";
            }
            if ($employee_clock_out_time == null){
            ?>
            <tr class="<?php echo $alternate_class; ?>">
                <?php
                $last_name = get_the_author_meta('last_name');
                $first_name = get_the_author_meta('first_name');
                ?>
                <td scope="row"><?php echo ucfirst($last_name) . ", " . ucfirst($first_name); ?></td>
                <td scope="row">
                <?php 
                echo aioDepartmentColumn(get_the_author_meta( 'ID' ), $loop->post->ID);
                ?>
                </td>

                <td>
                    <?php if ($employee_clock_in_time) :
                        $clock_in = $employee_clock_in_time;?>                        
                    <?php endif; ?>
                    <?php 
                    if ($clock_in) {
                        $newDate = date('Y/m/d h:i:s A', strtotime($clock_in));                            
                        echo $newDate;
                    } else {
                        echo 'Empty';
                    } 
                    ?>
                </td>
                <td>
                    <?php echo $ip_address_in; ?>
                </td>
                <?php 
                    if (get_option("aio_timeclock_show_shift_notes") == "enabled"){
                        echo '<td>'.$shift_note.'</td>';
                    }
                ?>
                <?php do_action("aio_new_report_column"); ?>
                <td>
                    <span style="background-color: #FFFF00"><strong><?php _e('Currently Working'); ?></strong></span>
                </td>
                <td>
                    <a href="post.php?post=<?php echo $loop->post->ID; ?>&action=edit" class="button" title="Edit Shift"><span class="dashicons dashicons-admin-generic vmiddle"></span></a>
                </td>
            </tr>
            <?php $count++; 
            }
            ?>
        <?php endwhile;
        wp_reset_query(); 
        ?>
    </tbody>
    </table>
    <div class="totalRowDiv">
        <hr>
        <strong><?php _e('Total Clocked In:'); ?> </strong><?php echo "" . $count . ""; ?>
        <hr>
    </div>
</div>