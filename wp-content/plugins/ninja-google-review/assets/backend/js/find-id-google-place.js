(function ($) {
  function googleplace() {
    var input = jQuery(".njt-autocomplete");

    input.each(function (index) {
      var autocomplete = new google.maps.places.Autocomplete(input[index]);
      setPlaceId(autocomplete, input[index]);
    });
  }

  function setPlaceId(autocomplete, element) {
    google.maps.event.addListener(autocomplete, "place_changed", function () {
      var place = autocomplete.getPlace();
      return place.place_id
        ? (jQuery(element)
          .parentsUntil("form")
          .find(".location")
          .val(place.name),
          jQuery(element)
            .parentsUntil("form")
            .find(".place_id")
            .val(place.place_id))
        : (alert("No place reference found for this location."), !1);
    });
  }

  function googleplace2() {
    var input = jQuery(".njt-autocomplete-snippet");

    input.each(function (index) {
      var autocomplete = new google.maps.places.Autocomplete(input[index]);
      setPlaceId2(autocomplete, input[index]);
    });
  }

  function setPlaceId2(autocomplete, element) {
    google.maps.event.addListener(autocomplete, "place_changed", function () {
      var place = autocomplete.getPlace();
      return place.place_id
        ? (jQuery(element)
          .parentsUntil("form")
          .find(".location-rich")
          .val(place.name),
          jQuery(element)
            .parentsUntil("form")
            .find(".njt_google_place_id")
            .val(place.place_id))
        : (alert("No place reference found for this location."), !1);
    });
  }

  jQuery(document).ready(function () {
    googleplace2();
    googleplace();
    // ==== UPDATE 22/02/2019 ===
    // $(document).on("input", ".njt-location-autocomplete-snippet", function(
    //   event
    // ) {
    $(document).on("click", ".njt-ggreviews-add-location-btn", function () {
      setTimeout(() => {
        var input = jQuery(".njt-location-autocomplete-snippet");
        input.each(function (index) {
          var autocomplete = new google.maps.places.Autocomplete(input[index]);
          setPlaceId2(autocomplete, input[index]);
        });
      }, 300);
    });
    // // ==== UPDATE 22/02/2019 ===
    $(document).on("click", ".njt-ggreviews-add-placeID-btn", function () {
      var $this = $(".njt-ggreviews-add-location-btn");
      var html = njt_ggreviews_location_settings.placeID_html;
      $(html).insertBefore($(".nta-list-location .submit"));
    });
    $(document).on("input", "input.njt_ggrv_placeID", function () {
      var _this = $(this);
      $('input[type="submit"]').prop("disabled", true);
      //setTimeout(() => {
      var locationID = _this.val();
      var data = {
        action: "njt_gg_review_get_name_by_place_id",
        place_id: locationID,
        nonce: njt_ggreviews_location_settings.nonce
      };

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: data
      })
        .done(function (json) {
          if (json.success) {
            _this
              .parent()
              .find(".njt_ggrv_find_name_span")
              .text(json.data.name);
            _this
              .parent()
              .find(
                ".njt_ggrv_location_name_by_placeID,.njt_google_look_name_by_place_id"
              )
              .val(json.data.name);
          } else {
            _this
              .parent()
              .find(".njt_ggrv_location_name_by_placeID")
              .val("Location Name Not Found!!!");
          }
          $('input[type="submit"]').prop("disabled", false);
        })
        .fail(function () {
          console.log("error");
          $('input[type="submit"]').prop("disabled", false);
        });
      //}, 300);
    });
  });

  jQuery(document).ajaxSuccess(function () {
    googleplace2();
    googleplace();
  });
})(jQuery);
