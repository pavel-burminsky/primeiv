var njt_corejs = (function ($) {
  function widget_clear_cache() {
    jQuery(".njt-clear-cache").on("click", function () { });
  }

  function sunriseStrpos(haystack, needle, offset) {
    var i = haystack.indexOf(needle, offset);
    return i >= 0 ? i : false;
  }

  function create_shortcode() {
    jQuery("#btn-create-shortcode").on("click", function ($) {
      var location,
        place_id,
        review_filter,
        cache,
        review_limit,
        hide_out_of_rating,
        review_characters,
        text_write,
        show_btn_write;

      //location = jQuery(".njt-creat-shortcode .location").val();
      //place_id = jQuery(".njt-creat-shortcode .place_id").val();
      place_id = jQuery("#location_id").val();
      location = jQuery("select#location_id")
        .find(":selected")
        .data("location");

      review_filter = jQuery(".njt-creat-shortcode .review_filter").val();
      review_limit = jQuery(".njt-creat-shortcode .review_limit").val();
      if (review_limit == "") review_limit = 5;

      cache = jQuery(".njt-creat-shortcode .cache").val();
      column = jQuery(".njt-creat-shortcode  input[name='column_shortcode']:checked").val();
      text_write = jQuery(
        ".njt-creat-shortcode #write-a-review-name-shortcode"
      ).val();

      review_characters = jQuery(
        ".njt-creat-shortcode .review_characters"
      ).val();

      var html;
      if (place_id !== "") {
        html = "[njt-gpr ";
        html += ' location="' + location + '"';
        html += ' place_id="' + place_id + '"';
        html += ' review_filter="' + review_filter + '"';
        html += ' review_limit="' + review_limit + '"';
        html += ' review_characters="' + review_characters + '"';
        html += ' column="' + column + '"';
        html += ' btn_write="' + text_write + '"';
        if (jQuery(".njt-creat-shortcode #hide_out_of_rating").is(":checked")) {
          html += ' hide_out_of_rating="1"';
        }

        if (
          jQuery(".njt-creat-shortcode #write-a-review-shortcode").is(
            ":checked"
          )
        ) {
          html += ' show_write_btn="yes"';
        }

        if (jQuery(".njt-creat-shortcode #carousel").is(":checked")) {
          html += ' carousel="yes"';
          if (jQuery(".njt-creat-shortcode #carousel-autoplay").is(":checked")) {
            html += ' carousel_autoplay="yes"';

            var carousel_speed = jQuery(".njt-creat-shortcode #carousel-speed").val()
            html += ' carousel_speed="' + carousel_speed + '"'
          }
        }

        if (jQuery(".njt-creat-shortcode #shadow").is(":checked")) {
          html += ' shadow="yes"';
        }

        if (jQuery(".njt-creat-shortcode #hide_header").is(":checked")) {
          html += ' hide_header="1"';
        }

        html += ' cache="' + cache + '"';

        html += " ]";
        jQuery(".shorecode-content").html(html);
      } else {
        alert("please enter location");
      }
    });
  }

  function create_badge() {
    jQuery("#btn-create-badge").on("click", function ($) {
      var location,
        place_id,
        review_filter,
        cache,
        review_limit,
        hide_out_of_rating;
      //location = jQuery(".njt-create-badge .location").val();
      //place_id = jQuery(".njt-create-badge .place_id").val();
      place_id = jQuery("#place_id").val();

      location = jQuery("select#place_id")
        .find(":selected")
        .data("location");
      cache = jQuery(".njt-create-badge .cache").val();
      var html;
      if (place_id !== "") {
        html = "[njt-gpr-badge ";
        html += ' location="' + location + '"';
        html += ' place_id="' + place_id + '"';
        html += ' cache="' + cache + '"';
        if (jQuery(".njt-create-badge #shadow-badge").is(":checked")) {
          html += ' shadow="yes"';
        }

        html += " ]";
        jQuery(".badge-content").html(html);
      } else {
        alert("please enter location");
      }
    });

    //Shortcode rendered html on Ajax
    jQuery('#place_id').on('change', function ($) {
      var location,
        place_id,
        cache;
      place_id = jQuery("#place_id").val();

      location = jQuery("select#place_id")
        .find(":selected")
        .data("location");
      var html;
      html = "[njt-gpr-badge ";
      html += ' location="' + location + '"';
      html += ' place_id="' + place_id + '"';
      html += " ]";
      //Shortcode rendered html on Ajax
      if (html) {
        const dataShortcode = {
          'action': 'get_shortcode_place_review',
          'nonce': njt_ggreviews_location_settings.nonce,
          'shortcode': html.trim()
        }
        jQuery.post(njt_ajax_object.ajax_url, dataShortcode, function (response) {
          jQuery('.badge-place-review-shortcode').html(response);
          if (jQuery('input[name="shadow"]').is(":checked")) {
            jQuery(".njt-google-places-reviews.njt-google-places-reviews-wap.njt-badge").removeClass("njt-disabled-shadow");
            jQuery(".badge-place-review .njt-header").css({ 'border': 'none', 'margin-bottom': '0', 'padding-bottom': '0' })
            jQuery(".njt-google-places-reviews-wap").css({ 'padding-bottom': '16px' })
          }
          jQuery('.badge-place-review .nta-browser').show()
        });
      }
    });
    jQuery(".form-njt-create-badge #shadow-badge").on("click", function ($) {
      if (jQuery('input[name="shadow"]').is(":checked")) {
        jQuery(".njt-google-places-reviews.njt-google-places-reviews-wap.njt-badge").removeClass("njt-disabled-shadow");
        jQuery(".badge-place-review .njt-header").css({ 'border': 'none', 'margin-bottom': '0', 'padding-bottom': '0' })
        jQuery(".njt-google-places-reviews-wap").css({ 'padding-bottom': '16px' })
      } else {
        jQuery(".njt-google-places-reviews.njt-google-places-reviews-wap.njt-badge").addClass("njt-disabled-shadow");
        jQuery(".badge-place-review .njt-header").css({ 'padding-bottom': '15px', 'border-bottom': '1px solid #e0e0e0' })
        jQuery(".badge-place-review .njt-google-places-reviews-wap").css({ 'padding-bottom': '0px' })
      }
    })
  }

  function sunriseCreateCookie(name, value, days) {
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      var expires = "; expires=" + date.toGMTString();
    } else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
  }
  function sunriseReadCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  function configCarousel() {
    jQuery('#carousel').change(function () {
      if (this.checked) {
        jQuery('#carousel-autoplay').closest('tr').show()
        jQuery('#carousel-speed').closest('tr').show()
      } else {
        jQuery('#carousel-autoplay').closest('tr').hide()
        jQuery('#carousel-speed').closest('tr').hide()
      }
    })
  }

  jQuery(document).ready(function ($) {
    $(document).on("click", ".njt-clear-cache", function (e) {
      e.preventDefault();
      var $this = $(this);

      $this.next(".cache-clearing-loading").fadeIn("fast");
      var data = {
        action: "njt_clear_cache",
        transient_id_1: $(this).data("transient-id-1"),
        transient_id_2: $(this).data("transient-id-2")
      };

      $.post(njt_ajax_object.ajax_url, data, function (response) {
        console.log(response);
        $(".cache-clearing-loading").hide();
        $this
          .prev(".cache-message")
          .text(response)
          .fadeIn("fast")
          .delay(2000)
          .fadeOut();
      });
    });

    var pagenow = "ninjagoogleplacereviewspro";

    $("#njt-plugin-tabs a").click(function (event) {
      $("#njt-plugin-tabs a").removeClass("nav-tab-active");
      $(".njt-plugin-pane").hide();
      $(this).addClass("nav-tab-active");
      // Show current pane
      $(".njt-plugin-pane:eq(" + $(this).index() + ")").show();

      sunriseCreateCookie(pagenow + "_last_tab", $(this).index(), 365);
    });

    // Auto-open tab by link with hash
    if (sunriseStrpos(document.location.hash, "#tab-") !== false)
      $(
        "#njt-plugin-tabs a:eq(" +
        document.location.hash.replace("#tab-", "") +
        ")"
      ).trigger("click");
    //Auto-open tab by cookies
    else if (sunriseReadCookie(pagenow + "_last_tab") != null)
      $(
        "#njt-plugin-tabs a:eq(" +
        sunriseReadCookie(pagenow + "_last_tab") +
        ")"
      ).trigger("click");
    // Open first tab by default
    else $("#njt-plugin-tabs a:eq(0)").trigger("click");
    create_shortcode();
    create_badge();
    configCarousel();
    //
    jQuery(document).on("change", "select.widget_gg_place_id", function (e) {
      var value = jQuery(this).val();
      var $this = jQuery(this);
      var parent = $this.closest(".set-business");
      var location_name = jQuery(this)
        .find(":selected")
        .data("location");
      parent.find(".njt-gg-widget-place-id").val(value);
      parent.find(".njt-gg-widget-location-name").val(location_name);
    });

    //Get value real time from input display    
    jQuery('#njt_google_rich_name').on("change keyup paste", function () {
      jQuery('.nta-searchresult .name').text(jQuery(this).val());
    });

    jQuery('#njt_google_rich_descritions').on("change keyup paste", function () {
      jQuery('.nta-searchresult .nta-descriptions').text(jQuery(this).val());
    });
    // Google Rich Snippet
    jQuery(document).on("change", ".njt-google-rich-snippet select#location_id", function (e) {
      var value = jQuery(this).val();
      const dataPlaceId = {
        'action': 'get_options_by_locationid',
        'placeId': value,
        'nonce': njt_ggreviews_location_settings.nonce
      }
      jQuery.post(njt_ajax_object.ajax_url, dataPlaceId, function (response) {
        jQuery('.snippet-rate-vote .star-rating').html(response.data.startRating);
        jQuery('.snippet-rate-vote .nta-rating').html(response.data.placeInfor ? response.data.placeInfor : 0);
        jQuery('.snippet-rate-vote .nta-ratings_count').html(response.data.ratings_count);
        //set value for input google rick
        jQuery('#njt_google_rich_image_site').val(response.data.place_avatar ? response.data.place_avatar : '')
        jQuery('#njt_google_rich_phone').val(response.data.international_phone_number ? response.data.international_phone_number : '')
        jQuery('#njt_google_rich_weburl').val(response.data.website ? response.data.website : '')
        jQuery('#njt_google_rich_address').val(response.data.formatted_address ? response.data.formatted_address : '')
        // remove notification error
        jQuery('.nta-input-required').removeClass('nta-input-error')
        jQuery('.nta-text-required').hide()
      });
    });
  });
})(jQuery());
