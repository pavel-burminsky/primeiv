;(function($) {
  $(document).ready(function () {
    let faqHeaders = $('.question-header')
    if (
      faqHeaders.length
      && typeof $.fn.slideToggle === "function"
    ) {
      faqHeaders.each(function () {
        $(this).on('click', function () {
          let parent = jQuery(this).parent()
          parent.toggleClass('active')
          parent.find('.question-content').slideToggle();
        })
      })
    }


    var tooltips = $('.primeiv-tooltip')
    if (
      tooltips.length
      && typeof $.fn.tooltipster === "function"
    ) {

      const options = {
        contentAsHTML: true,
        interactive: true,
        maxWidth: 372,
        offsetY: 20,
        animation: 'grow',
      }

      if (isTouchDevice()) {
        options.trigger = 'click'
      }

      tooltips.tooltipster(options)
    }

    function isTouchDevice() {
      return (('ontouchstart' in window) ||
        (navigator.maxTouchPoints > 0) ||
        (navigator.msMaxTouchPoints > 0));
    }

    
    $('[primeiv-footer-regions-title]').click(function (){
      const $this = $(this),
        list = $this.next('ul')
      list.toggle()
      $this.toggleClass('opened')
    })

    // this fixes slow scroll up on .fusion-top-top-link button click
    setTimeout(function (){
      $(".fusion-top-top-link").unbind("click")
    }, 500)
    
    
    $('[primeiv-go-to-memberships]').click(function (e){
        if(window.innerWidth <= 1250) {
          e.preventDefault()
          const section = $('[data-section="services-prices-wrap"]')

          if( section.length !== 0 ) {
            if( ! section.hasClass('active') ) {
              section.click()
            }
            const $anchor = $("#memberships");
            if( $anchor.length !== 0 ) {
              $anchor[0].scrollIntoView()
            }
          }
        }
    })
  })
})(jQuery)
