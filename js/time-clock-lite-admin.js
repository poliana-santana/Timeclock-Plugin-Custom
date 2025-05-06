var Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});

jQuery(function () {
  jQuery('.adminInputDate').datetimepicker({
    format: 'Y-m-d h:i A',
    formatTime: 'h:i A'
  });

  if (jQuery("#aio_tc_news").length > 0) {
    jQuery("#aio_tc_news").steps({
      headerTag: "h3",
      bodyTag: "section",
      transitionEffect: "slideLeft",
      stepsOrientation: "vertical",
      autoFocus: true,
      onFinished: function () {
        window.location = "?page=aio-tc-lite";
      }
    });
  }


  jQuery("#aio_generate_report").click(function (e) {
    e.preventDefault();
    var wage_enabled = jQuery("#wage_enabled").val()
    var report_action = "generate_report";
    var admin_action = 'report';
    jQuery("#aio-reports-results").html('<center><img src="/wp-admin/images/spinner-2x.gif"></center>').show();
    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: timeClockAdminAjax.ajaxurl,
      data: {
        action: "aio_time_clock_lite_admin_js",
        admin_action: admin_action,
        report_action: report_action,
        aio_pp_start_date: jQuery("#aio_pp_start_date").val(),
        aio_pp_end_date: jQuery("#aio_pp_end_date").val(),
        employee: jQuery("#employee").val(),
        nonce: timeClockAdminAjax.Nonce
      },
      success: function (data) {
        if (data["response"] == "success") {
          var shiftRows = data["shifts"]["shift_array"];
          jQuery("#aio-reports-results").html("");
          var reportHtml =
            '<table class="widefat fixed" cellspacing="0">' +
            '<thead>' +
            '<tr>' +
            '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>' + timeClockAdminAjax.Name + '</strong></th>' +
            '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>' + timeClockAdminAjax.clockIn + '</strong></th>' +
            '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>' + timeClockAdminAjax.clockOut + '</strong></th>' +
            '<th id="columnname" class="manage-column column-columnname" scope="col"><strong>' + timeClockAdminAjax.ShiftTotal + '</strong></th>' +
            '</tr>' +
            '</thead>' +
            '<tbody>';
          var count = 0;
          shiftRows.forEach(function (item) {
            count++;
            var alternate_class = "";
            if (isEven(count)) {
              alternate_class = 'alternate';
            }
            var employee_clock_in_time = "";
            if (item["employee_clock_in_time"] != null) {
              employee_clock_in_time = item["employee_clock_in_time"];
            }
            var employee_clock_out_time = "";
            if (item["employee_clock_out_time"] != null) {
              employee_clock_out_time = item["employee_clock_out_time"];
            }
            var shift_sum = "";
            if (item["shift_sum"]) {
              shift_sum = item["shift_sum"];
            }

            reportHtml +=
              '<tr class="' + alternate_class + '">' +
              '<td>' + item["last_name"] + ', ' + item["first_name"] + '</td>' +
              '<td>' + employee_clock_in_time + '</td>' +
              '<td>' + employee_clock_out_time + '</td>' +
              '<td>' + shift_sum + '</td>' +
              '</tr>';
          });
          reportHtml += '</tbody>' +
            '</table>';

          var shift_total_time = "0";
          if (data["shifts"]["shift_total_time"]) {
            shift_total_time = data["shifts"]["shift_total_time"];
          }

          var wage_total = "0";
          var wage_total_row = "";
          if (data["shifts"]["wage_total"]) {
            wage_total = data["shifts"]["wage_total"];
          }

          if (wage_enabled == "enabled") {
            wage_total_row = '<strong>' + timeClockAdminAjax.WageTotal + ': </strong>' + wage_total + '<br />'
          }

          reportHtml += '<div class="controlDiv">' +
            '<strong>' + timeClockAdminAjax.TotalShifts + ': </strong>' + data["shifts"]["shift_count"] + '<br />' +
            '<strong>' + timeClockAdminAjax.TotalShiftTime + ': </strong>' + shift_total_time + '<br />' +
            wage_total_row +
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

function getProPopup(element) {
  jQuery(".getProButton").hide("fast")
  var count = jQuery(element).attr("data-count")
  var title = jQuery(" #featureTitle-" + count).html()
  var img = jQuery(" #featureImage-" + count).attr("src")
  var description = jQuery(" #featureDesc-" + count).html()

  jQuery("#aio_modal_image").html('<img class="aioModalImage" src="' + img + '">')
  jQuery("#aio_modal_title").html('<h2>' + title + '</h2>')
  jQuery("#aio_modal_description").html('<p>' + description + '</p>')
  tb_show("", "#TB_inline?width=1000&height=650&inlineId=aio-modal-window-id");
  jQuery(".getProButton").show("fade")
}

function createTimeClockPage(element) {
  var admin_action = jQuery(element).attr("data-admin-action");
  var nonce = jQuery('input[name="time-clock-nonce"]').val()
  jQuery.ajax({
    type: "post",
    dataType: "json",
    url: timeClockAdminAjax.ajaxurl,
    data: {
      action: "aio_time_clock_lite_admin_js",
      admin_action: admin_action,
      nonce: nonce
    },
    success: function (data) {
      console.log(data)
      if (data['response'] == "success") {
        Swal.fire({
          icon: 'success',
          title: 'Success',
          html: data['message'],
          showCancelButton: true,
          confirmButtonText: timeClockAdminAjax.ViewPage,
          cancelButtonText: timeClockAdminAjax.Close,
          customClass: {
              actions: 'my-actions',
              confirmButton: 'order-1 right-gap' + (!data['page'] ? ' hidden-item' : ''),
              cancelButton: 'order-2'
          },
        }).then((result) => {
          var page_id = data['page_id'] ? data['page_id'] : null;
          var page_link = data['link'] ? data['link'] : null;
          if (page_link && page_id){
            var page_html = '<a href="' + page_link + '" class="button small_button" target="_blank"><i class="dashicons dashicons-search vmiddle"></i>&nbsp;' + timeClockAdminAjax.ViewPage + '</a>&nbsp;' +
            '<a href="/wp-admin/post.php?post=' + page_id + '&action=edit" class="button small_button" target="_blank"><i class="dashicons dashicons-edit vmiddle"></i>&nbsp; ' + timeClockAdminAjax.EditPage + '</a>';
            if (jQuery('#' + admin_action + '_td')){
              jQuery('#' + admin_action + '_td').html(page_html)
            }
          }
          
          if (result.isConfirmed) {
            if (page_link){
              window.location.href = page_link;
            }
            else{
              Toast.fire({
                icon: 'error',
                title: timeClockAdminAjax.PageLinkEmpty
              })
            }
          } else if (result.isDenied) {
            
          }
        })
      } else {
        Swal.fire({
          title: '<span class="time-clock-swal-header">' + data['message'] + '</span>',
          html: (data['response_html'] ? data['response_html'] : ''),
          icon: "error"
        })
      }
    },
    error: function (data) {
      Swal.fire({
        title: '<span class="time-clock-swal-header">' + data['message'] + '</span>',
        html: (data['response_html'] ? data['response_html'] : ''),
        icon: "error"
      })
    },
  });
}