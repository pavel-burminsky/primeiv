jQuery(document).ready(function ($) {
  $(document).on("click", ".njt-ggreviews-add-location-btn", function (event) {
    var $this = $(this);
    var html = njt_ggreviews_location_settings.location_html;
    $(html).insertBefore($(".nta-list-location .submit"));
  });

  $(document).on("click", ".njt-gg-review-remove-location", function (event) {
    if (!confirm(njt_ggreviews_location_settings.are_you_sure)) {
      return;
    }
    var $this = $(this);
    var parent = $this.closest(".njt_gg_reviews_add_location_wrap");
    var location_id = parent.data("location_id");

    if (location_id == "") {
      parent.remove();
      return false;
    }
    $this.addClass("updating-message");

    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "njt_gg_review_delete_reviews",
        location_id: location_id,
        nonce: njt_ggreviews_location_settings.nonce
      }
    })
      .done(function (json) {
        console.log(json);
        $this.removeClass("updating-message");
        if (json.success) {
          parent.remove();
        } else {
          alert(json.mess);
        }
      })
      .fail(function () {
        $this.removeClass("updating-message");
        alert(njt_ggreviews_location_settings.nonce_mess);
        console.log("error");
      });
    return false;
  });

  // GET REVIEWS
  $(document).on("click", ".njt_google_get_new_reviews", function (event) {
    if (!confirm(njt_ggreviews_location_settings.are_you_sure)) {
      return;
    }

    var $this = $(this);
    var parent = $this.closest(".njt_gg_reviews_add_location_wrap");
    var location_id = parent.data("location_id");

    if (location_id != "") {
      $this.addClass("updating-message");

      var data = {
        action: "njt_gg_review_get_new_reviews",
        location_id: location_id,
        nonce: njt_ggreviews_location_settings.nonce
      };

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: data
      })
        .done(function (json) {
          $this.removeClass("updating-message");
          console.log(json);

          if (json.success) {
            $this.text("Updated!");
            location.reload();
          } else {
            var value_result = json.data.mess;
            alert(value_result);
            return false;
          }
        })
        .fail(function () {
          $this.removeClass("updating-message");
          alert(njt_ggreviews_location_settings.nonce_mess);
          console.log("error");
        });
    } else {
      alert(njt_ggreviews_location_settings.location_invalid);
    }
    return false;
  });

  // EXCUTE ON_OFF REVIEWS
  valueOptionScheduleChange = function (option) {
    switch (option) {
      case "minute":
        return "Minutes";
        break;
      case "hour":
        return "Hours";
        break;
      case "day":
        return "Days";
        break;
      case "week":
        return "Weeks";
        break;
      case "month":
        return "month(s)";
        break;
      case "year":
        return "Years";
        break;
      default:
        return "";
    }
  };

  $(document).on("change", "input.google_reviews_schedule_on_off", function () {
    if (this.checked) {
      $(".ggreviews_value_schedule").show();
      $('select#googlereviews_schedule_update').val('month').trigger('change');
      $("input[name='googlereviews_schedule_value']").val('1');
      $(".show_option_schedule").text(valueOptionScheduleChange('month'));
    } else {
      $('select#googlereviews_schedule_update').val('').trigger('change');
      $("input[name='googlereviews_schedule_value']").val('');
      $(".ggreviews_show_hide_update_reviews").hide();
    }
  });
  if (!jQuery("input.google_reviews_schedule_on_off").is(":checked")) {
    $("select#googlereviews_schedule_update").val('').trigger('change');
    $("input[name='googlereviews_schedule_value']").val('');
    $(".ggreviews_show_hide_update_reviews").hide();
  }


  $(document).on("submit", "form.nta-list-location", function (e) {
    let has_empty = false;
    $(this).find('.js-add-new-location input[name="njt_gg_reviews_location[location_look][]"]').each(function () {
      if (!$(this).val()) {
        has_empty = true;
        return false;
      }
    });

    $(this).find('.js-add-new-placeid input[name="njt_gg_reviews_location[place_id][]"]').each(function () {
      if (!$(this).val()) {
        has_empty = true;
        return false;
      }
    });

    if (has_empty) {
      alert('Vui lòng nhập đủ thông tin')
      return false;
    }
  })

  //Event to click add new location
  $(".add-new-location .add-location-btn").click(function (e) {
    $(".add-new-location .dropdown-menu").toggle();
    e.stopPropagation();
  });

  $(document, '.add-new-location .dropdown-menu').click(function () {
    $(".add-new-location .dropdown-menu").hide();
  });
});
