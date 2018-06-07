jQuery(document).ready(function () {

  if (jQuery("#aio_time_clock").length > 0) {
    employee = jQuery("#aio_clock_button").attr("data-employee");
    nonce = jQuery(this).attr("data-nonce");
    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: timeClockAjax.ajaxurl,
      data: {
        action: "aio_time_clock_js",
        clock_action: "check_shifts",
        employee: employee,
        nonce: nonce
      },
      success: function (response) {
        //console.log(response);
        var o_shift = response["open_shift_id"];
        var is_clocked_in = response["is_clocked_in"];
        var new_clock_action = "";
        if (is_clocked_in){
          new_clock_action = "clock_out";
          jQuery("#open_shift_id").val(o_shift);
          jQuery("#clock_action").val(new_clock_action);
          jQuery("#aio_clock_button").html("Clock Out");
          jQuery("#clockMessage").html(
            'You are currently clocked in.<br /> <strong>Clock In Time:</strong> ' + response['employee_clock_in_time']
          );

        }
        else{
          new_clock_action = "clock_in";
          jQuery("#clock_action").val(new_clock_action);
          jQuery("#open_shift_id").val("");
          jQuery("#aio_clock_button").html("Clock In");
          jQuery("#clockMessage").html('Click "CLOCK IN" to <strong>START</strong> your shift.');
        }
      }
    });
  }

  if( jQuery('#jsTimer').length ) {
    var myTimerVar = setInterval(myTimer, 1000);
  }

  jQuery(".aioUserButton").click(function (e) {
    e.preventDefault();
    var aio_link = jQuery(this).attr("href");
    window.location = aio_link;
  });

  jQuery("#aio_clock_button").click(function (e) {
    e.preventDefault();
    jQuery("#aio_clock_button").html('<div class="aio-spinner"></div>');
    employee = jQuery(this).attr("data-employee");
    nonce = jQuery(this).attr("data-nonce");
    clock_action = jQuery("#clock_action").val();
    open_shift_id = jQuery("#open_shift_id").val();

    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: timeClockAjax.ajaxurl,
      data: {
        action: "aio_time_clock_js",
        clock_action: clock_action,
        open_shift_id: open_shift_id,
        employee: employee,
        nonce: nonce
      },
      success: function (response) {
        console.log(response);
        var open_shift_id = response["open_shift_id"];
        var is_clocked_in = response["is_clocked_in"];
        var new_clock_action = "";
        if (is_clocked_in){
          new_clock_action = "clock_out";
          jQuery("#open_shift_id").val(open_shift_id);
          jQuery("#clock_action").val(new_clock_action);
          jQuery("#aio_clock_button").html("Clock Out");
          jQuery("#clockMessage").html(
            'You are currently clocked in.<br /> <strong>Clock In Time:</strong> ' + response['employee_clock_in_time']
          );

        }
        else{
          new_clock_action = "clock_in";
          jQuery("#open_shift_id").val("");
          jQuery("#clock_action").val(new_clock_action);
          jQuery("#aio_clock_button").html("Clock In");
          jQuery("#clockMessage").html('Click "CLOCK IN" to <strong>START</strong> your shift.');
        }
      }
    });
  });
});

function myTimer() {
  var d = new Date();
  document.getElementById("jsTimer").innerHTML = "<strong>Current Time: </strong>" + d.toLocaleTimeString();
}