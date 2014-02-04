$(function(){
  // milliseconds to wait between animations
  var DELAY = 5000;
  // animation speed in milliseconds
  var ANIM = 1000;
  
  // create divs with image backgrounds
  var div = $("#animation");
  for (var i=9; i>=0; i--) {
    div.html("<div id='an-0"+i+"'></div>");
    div = $("div", div);
    div.css("background-image", "url(animation/0"+i+".jpg)");
  }
  
  var fadeOutStep = function(id, callback) {
    $(id).fadeOut(ANIM, function() {
      setTimeout(callback, DELAY);
    });
  };
  
  var fadeOut = function() {
    fadeOutStep("#an-00", function() {
      fadeOutStep("#an-01", function() {
        fadeOutStep("#an-02", function() {
          fadeOutStep("#an-03", function() {
            fadeOutStep("#an-04", function() {
              fadeOutStep("#an-05", function() {
                fadeOutStep("#an-06", function() {
                  fadeOutStep("#an-07", function() {
                    fadeOutStep("#an-08", fadeIn);
                  });
                });
              });
            });
          });
        });
      });
    });
  };
  
  var fadeIn = function() {
    $("#animation #an-00").show();
    $("#animation #an-01").show();
    $("#animation #an-02").show();
    $("#animation #an-03").show();
    $("#animation #an-04").show();
    $("#animation #an-05").show();
    $("#animation #an-06").show();
    $("#animation #an-07").show();
    $("#animation #an-08").fadeIn(ANIM, function(){
      setTimeout(fadeOut, DELAY);
    });
  };
  
  setTimeout(fadeOut, DELAY);
  
});

