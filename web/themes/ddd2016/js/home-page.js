(function ($, Drupal) {
// var controller = new ScrollMagic.Controller({addIndicators: false});
  var controller = new ScrollMagic.Controller({
    globalSceneOptions: {
      triggerHook: "onLeave"
    }
  });
  Section0();
  Section1();
  Section3();

  $('.owl-carousel').owlCarousel({
    loop:true,
    margin:10,
    nav:true,
    responsive:{
      0:{
        items:1
      },
      600:{
        items:3
      },
      1000:{
        items:5
      }
    }
  });


// INTRO ********************************************************************************************************************
  function Section0() {
    TweenMax.set(".logo-hero-text", {x: 40, opacity: 0});
    TweenMax.set(".logo", {y: -20, opacity: 0});
    TweenMax.set(".l-region--branding", {y: -20, opacity: 0});
    TweenMax.set(".mean-nav .menu li", {x: -10, opacity: 0});
    TweenMax.set(".buy-tickets", {y: -20, opacity: 0});
    TweenMax.set(".meanmenu-reveal", {y: -20, scale: 1.04, opacity: 0});
    // TweenMax.set(".front-page .logo", {opacity:0, y:-50});

    new ScrollMagic.Scene({triggerElement: ".section0", offset: 0, duration: "50%"})
      .setTween(".section0 .section-inner", {y: "-50%", opacity: 0, ease: Linear.easeNone})
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".section0", offset: 0})
      .on("enter", function (event) {
        TweenMax.to($(".logo-hero-text"), .7, {opacity: 1, x: 0, ease: Power4.easeOut, delay: 0.7});
        TweenMax.to($(".logo"), .7, {opacity: 1, y: 0, ease: Power2.easeOut, delay: 1});
        TweenMax.to($(".l-region--branding"), .7, {opacity: 1, y: 0, ease: Power2.easeOut, delay: 0});
        TweenMax.to($(".buy-tickets"), .7, {opacity: 1, y: 0, ease: Power2.easeOut, delay: 1.7});
        TweenMax.to($(".meanmenu-reveal"), .7, {opacity: 1, y: 0, ease: Power2.easeOut, delay: 2});
        TweenMax.staggerTo($(".mean-nav .menu li"), 0.3, {x: "0", opacity: 1, delay: 1, ease: Power2.easeOut}, 0.2);
        TweenMax.to($(".svg-gear"), 3, {rotation: 40, ease: Expo.easeOut, delay: 0.7});
        TweenMax.to($(".svg-gear.back"), 3, {rotation: -90, ease: Expo.easeOut, delay: 0.7});
      })
      .on("leave", function (event) {
      })
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".l-main", offset: 10, duration: "200%"})
      .setTween($(".gear1-container .svg-gear"), {rotation: "90deg", ease: Linear.easeNone})
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".l-main", offset: 10, duration: "200%"})
      .setTween($(".gear2-container .svg-gear"), {rotation: "-45deg", ease: Linear.easeNone})
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".l-main", offset: 10, duration: "200%"})
      .setTween($(".gear3-container img"), {rotation: "-180deg", ease: Linear.easeNone})
      .addTo(controller);
  }


//  ********************************************************************************************************************
  function Section1() {
    TweenMax.set("#plane", {y: 350, opacity: 1});

    new ScrollMagic.Scene({triggerElement: ".section1", offset: 0, duration: "80%"})
      .setTween("#plane", {y: "-900px", x: "600px", opacity: 1, ease: Linear.easeNone})
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".section0", offset: 0})
      .on("enter", function (event) {
      })
      .on("leave", function (event) {
      })
      .addTo(controller);
  }

//  ********************************************************************************************************************
  function Section3() {
    TweenMax.set("#plane2", {css: {left: "80%", top: "100%"}, opacity: 1});
    TweenMax.set(".section3 .bg-img", {y: 300, opacity: 1});
    TweenMax.set(".section3 #triangle", {y: 300, opacity: 1});

    new ScrollMagic.Scene({triggerElement: ".section3", offset: 0, duration: "60%"})
      .setTween("#plane2", {css: {left: "-500px", top: "-100%"}, ease: Linear.easeNone})
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".section3", offset: -300, duration: "80%"})
      .setTween(".section3 .bg-img", {y: -300, ease: Linear.easeNone})
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".section3", offset: -300, duration: "80%"})
      .setTween("#triangle", {y: 0, ease: Linear.easeNone})
      .addTo(controller);

    new ScrollMagic.Scene({triggerElement: ".section3", offset: 0})
      .on("enter", function (event) {
      })
      .on("leave", function (event) {
      })
      .addTo(controller);
  }
})(jQuery, Drupal);
