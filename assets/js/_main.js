/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can 
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function($) {

// Use this variable to set up the common and page specific functions. If you 
// rename this variable, you will also need to rename the namespace below.
var Shoestrap = {
  // All pages
  common: {
    init: function() {
      // JavaScript to be fired on all pages
      
      // Offcanvas dropdown effect
      var $offCanvas = $('#offcanvas'),
          $dropdown  = $offCanvas.find('.dropdown');
      $dropdown.on('show.bs.dropdown', function() {
          $(this).find('.dropdown-menu').slideDown(350);
      }).on('hide.bs.dropdown', function(){
          $(this).find('.dropdown-menu').slideUp(350);
      });
      
      $(".entry-content").fitVids();
      $(".fitvids").fitVids();
      //$(".dslc-module-DSLC_Html").fitVids();
      
      $('.flexslider').flexslider({
        animation: "fade",              //String: Select your animation type, "fade" or "slide"
        slideshowSpeed: 7000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
        animationSpeed: 600,            //Integer: Set the speed of animations, in milliseconds
        controlNav: false,              //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
        directionNav: true,             //Boolean: Create navigation for previous/next navigation? (true/false)
        smoothHeight: false,            //{NEW} Boolean: Allow height of the slider to animate smoothly in horizontal mode  
        smoothHeight: true,
      });

      /**
       * Live search
       */
      jQuery.Autocomplete.prototype.suggest = function () {
        
          if (this.suggestions.length === 0) {
              this.hide();
              return;
          }

          var that = this,
              formatResult = that.options.formatResult,
              value = that.getQuery(that.currentValue),
              className = that.classes.suggestion,
              classSelected = that.classes.selected,
              container = $(that.suggestionsContainer),
              html = '';
          // Build suggestions inner HTML
          $.each(that.suggestions, function (i, suggestion) {
              //html += '<div class="' + className + suggestion.css + '" data-index="' + i + '"><p class="ls-'+suggestion.type_color+'">'+suggestion.type_label+'</p> <h4>'+suggestion.icon + formatResult(suggestion, value) + '</h4></div>';
              html += '<div class="' + className + suggestion.css + '" data-index="' + i + '">' +suggestion.icon+ '<h4>' + formatResult(suggestion, value) + '</h4></div>';
          });

          container.html(html).show();
          that.visible = true;

          // Select first value by default:
          if (that.options.autoSelectFirst) {
              that.selectedIndex = 0;
              container.children().first().addClass(classSelected);
          }
      };
      
      // Initialize ajax autocomplete:
      $('.searchajax').autocomplete({
          serviceUrl: _url,
          params: {'action':'search_title'},
          minChars: 1,
          maxHeight: 450,
          onSelect: function(suggestion) {
          //  $('#content').html('<h2>Redirecting ... </h2>');
              window.location = suggestion.data.url;
          }
      });
      
    }
  },
  // Home page
  home: {
    init: function() {
      // JavaScript to be fired on the home page
    }
  },
  // About us page, note the change from about-us to about_us.
  about_us: {
    init: function() {
      // JavaScript to be fired on the about us page
    }
  },
  // Single post page
  single_post: {
    init: function() {

      $("#toc").toc();

      $( '.toc a' ).each( function () {

        var destination = '';
        $( this ).click( function( e ) {
          e.preventDefault();
          go_to_elm = true;
          var elementClicked = $( this ).attr( 'href' );
          var elementOffset = jQuery( 'body' ).find( elementClicked ).offset();
          destination = elementOffset.top;
          jQuery( 'html,body' ).animate( { scrollTop: destination - 80 }, 300 );
          setTimeout(function(){
            go_to_elm = false;
          }, 800);

        } );

      });

      // Voting
      jQuery('a.like-btn').click(function(){
        response_div = jQuery(this).parent().parent();
        jQuery.ajax({
          url         : PAAV.base_url,
          data        : {'vote_like':jQuery(this).attr('post_id')},
          beforeSend  : function(){},
          success     : function(data){
            response_div.hide().html(data).fadeIn(400);
          },
          complete    : function(){}
        });
      });
      
      jQuery('a.dislike-btn').click(function(){
        response_div = jQuery(this).parent().parent();
        jQuery.ajax({
          url         : PAAV.base_url,
          data        : {'vote_dislike':jQuery(this).attr('post_id')},
          beforeSend  : function(){},
          success     : function(data){
            response_div.hide().html(data).fadeIn(400);
          },
          complete    : function(){}
        });
      });

    }
  }
};

// The routing fires all common scripts, followed by the page specific scripts.
// Add additional events for more control over timing e.g. a finalize event
var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = Shoestrap;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {
    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
  }
};

$(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.


