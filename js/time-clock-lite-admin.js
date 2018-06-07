jQuery(function() {
  jQuery("#clock_in").datetimepicker({
    format: "Y/m/d g:i:s A",
    formatTime: "g:i A"
  });

  jQuery("#clock_out").datetimepicker({
    format: "Y/m/d g:i:s A",
    formatTime: "g:i A"
  });

  jQuery("#aio_generate_report").click(function(e) {
    e.preventDefault();
    var nonce = jQuery(this).attr("date-nonce");
    var report_action = "generate_report";
    jQuery("#aio-reports-results").html('<center><img src="/wp-admin/images/spinner-2x.gif"></center>').show();
    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: timeClockAdminAjax.ajaxurl,
      data: {
        action: "aio_time_clock_admin_js",
        report_action: report_action,
        aio_pp_start_date: jQuery("#aio_pp_start_date").val(),
        aio_pp_end_date: jQuery("#aio_pp_end_date").val(),
        employee: jQuery("#employee").val(),
        nonce: nonce
      },
      success: function(data) {
        if (data["response"] == "success") {
            var shiftRows = data["shifts"]["shift_array"];
            jQuery("#aio-reports-results").html("");
            var reportHtml =
                '<table class="widefat fixed" cellspacing="0">' +
                '<thead>' +
                '<tr>' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Name</strong></th>' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Clock In</strong></th>' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Clock Out</strong></th>' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Shift Total</strong></th>' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Options</strong></th>' +
                '</tr>' +
                '</thead>' +
                '<tfoot>' +
                '<tr >' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Name</strong></th>' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Clock In</strong></th>' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Clock Out</strong></th>' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Shift Total</strong></th>' +
                '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>Options</strong></th>' +
                '</tr>' +
                '</tfoot>' +
                '<tbody>';
            var count = 0;
            shiftRows.forEach(function (item) {
                count++;
                var alternate_class = "";
                if (isEven(count)) {
                    alternate_class = 'alternate';
                }
                reportHtml +=
                    '<tr class="' + alternate_class + '">' +
                    '<td>' + item["last_name"] + ', ' + item["first_name"] + '</td>' +
                    '<td>' + item["employee_clock_in_time"] + '</td>' +
                    '<td>' + item["employee_clock_out_time"] + '</td>' +
                    '<td>' + item["shift_sum"] + '</td>' +
                    '<td><a class="button" target="_blank" href="post.php?post=' + item["shift_id"] + '&action=edit" Edit Shift><span class="dashicons dashicons-welcome-write-blog vmiddle"></span></a></td>' +
                    '</tr>';
            });
            reportHtml += '</tbody>' +
                '</table>';
            reportHtml += '<div class="controlDiv">' +
                '<strong>Total Shifts: </strong>' + data["shifts"]["shift_count"] + '<br />' +
                '<strong>Total Shift Time: </strong>' + data["shifts"]["shift_total_time"] + '<br />' +
                '<hr>' +
                '</div>';
            jQuery("#aio-reports-results").html(reportHtml);
            jQuery("#aio-reports-results").show();
        }
      }
    });
  });
});

function editClockTime(type) {
  if (type == "in") {
    jQuery("#clock_in").show("fast");
  }
  if (type == "out") {
    jQuery("#clock_out").show("fast");
  }
}

function editEmployee() {
  jQuery("#employee_id").show("fast");
}

function isEven(number) {
    if (number % 2 == 0) {
        return true;
    }  // even
    else {
        return false;
    } // odd
}
