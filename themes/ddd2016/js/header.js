(function ($, Drupal) {
// var controller = new ScrollMagic.Controller({addIndicators: false});
  var controller = new ScrollMagic.Controller({
    globalSceneOptions: {
      triggerHook: "onLeave"
    }
  });
  General();


// ********************************************************************************************************************
  function General() {

    new ScrollMagic.Scene({triggerElement: ".l-page", offset: 10, duration: "100"})
      .setTween("#cta-btn", {scale: 0.953, ease: Linear.easeNone})
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".l-page", offset: 10, duration: "100"})
      .setTween(".meanmenu-reveal", {scale: 1, ease: Linear.easeNone})
      .addTo(controller);

    var $header = $("header.l-header");
    var headerTween = TweenMax.to($header, 1, {
      paddingBottom: "0px",
      paddingTop: "0px",
      ease: Power0.easeOut
    });
    new ScrollMagic.Scene({duration: "100"})
      .setTween(headerTween)
      .triggerElement($(".l-page")[0])
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".l-page", offset: 60})
      .on("enter", function (event) {
        TweenMax.to($(".logo-regular"), .25, {opacity: 0, ease: Power4.easeOut});
        TweenMax.to($(".logo-white"), .25, {opacity: 1, ease: Power4.easeOut});
        TweenMax.to($(".l-header"), .7, {css: {backgroundColor: "#821005"}, ease: Power4.easeOut});
        TweenMax.to($(".mean-nav .menu li a:link"), 0, {css: {color: "#ffffff"}, ease: Power4.easeOut, delay: .25});
      })
      .on("leave", function (event) {
        TweenMax.to($(".logo-regular"), .25, {opacity: 1, ease: Power4.easeOut});
        TweenMax.to($(".logo-white"), .25, {opacity: 0, ease: Power4.easeOut});
        TweenMax.to($(".l-header"), .7, {css: {backgroundColor: "transparent"}, ease: Power4.easeOut});
        TweenMax.to($(".mean-nav .menu li a:link"), 0, {css: {color: "#921004"}, ease: Power4.easeOut, delay: 0});
      })
      .addTo(controller);

    var MenuAnim = TweenMax.to($('.l-region--branding'), 0.5, {ease: Power4.easeInOut, css: {right: 0}, paused: true});
    $('#menu-btn').click(function () {
      MenuAnim.play()
    });
    var MenuAnimClose = TweenMax.to($(".l-region--branding"), 0.5, {
      css: {right: -340},
      ease: Power4.easeInOut,
      paused: true
    });
    $('#menu-btn-close').click(function () {
      MenuAnimClose.play()
    });

  }
})(jQuery, Drupal);

