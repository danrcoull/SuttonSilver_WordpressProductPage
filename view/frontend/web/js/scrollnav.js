define([
    "jquery",
    "bootstrap/affix",
    "bootstrap/scrollspy"
], function($) {
    "use strict";

    var scrollNav = {
        options: {
            parPosition: [],
            stickyStopper : null,
            sticky : null

        },
        _init: function (sticky = $('#maincontent .sidebar'), stickyStopper = $('#maincontent .main') ) {

            scrollNav.options.sticky = sticky;
            scrollNav.options.stickyStopper = stickyStopper;

            $('.wrapper-block').each(function () {
                scrollNav.options.parPosition.push($(this).offset().top);
            });

            scrollNav._onClick();
            //scrollNav._onScroll();
            //scrollNav._stick();
            var footerHeight = ($('footer').outerHeight(true) + $('.footer.awards').outerHeight(true)) + 125;

            $('#maincontent .siderbar-cart').affix( {
                offset: {
                    top: 100,
                    bottom: (footerHeight + $('#maincontent #productScroll').outerHeight(true))
                }
            })
                .on('affix.bs.affix', function() {
                    $(this).css('width',$(this).parent().width());
                    $(this).css('top',  120);
                })
                .on('affix-top.bs.affix',function(){
                    $(this).css('width','auto');
                    $(this).css('top',  'auto');
                });

            $('#maincontent #productScroll').affix({
                offset: {
                    top:  100,
                    bottom: footerHeight + 50
                }
            })
                .on('affix.bs.affix', function() {
                    $(this).css('width',$(this).parent().width());
                    $(this).css('position','fixed');
                    $(this).css('top',  ($('#maincontent .siderbar-cart').outerHeight(true) + 125));
                })
                .on('affix-top.bs.affix',function(){
                    $(this).css('width','auto');
                    $(this).css('top',  'auto');
                    $(this).css('position','relative');
                });

            $('body').scrollspy({target: "#productScroll", offset: scrollNav.options.sticky.offset().top});

        },
        _onClick: function (event) {
            $('#productScroll a').on('click', function (event) {
                /** $('html, body').animate({
                    scrollTop: $($.attr(this, 'href')).offset().top
                }, 500);
                 $('#productScroll a').removeClass('active');
                 $(this).addClass('active');

                 event.preventDefault();**/

                // Make sure this.hash has a value before overriding default behavior
                if (this.hash !== "") {

                    // Prevent default anchor click behavior
                    event.preventDefault();

                    // Store hash
                    var hash = this.hash;

                    // Using jQuery's animate() method to add smooth page scroll
                    // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top
                    }, 800, function(){

                        // Add hash (#) to URL when done scrolling (default click behavior)
                        window.location.hash = hash;
                    });

                } // End if
            });


            return this;
        },
        _stick: function() {
            var $sticky = scrollNav.options.sticky;
            var $stickyrStopper =  scrollNav.options.stickyStopper;
            if (!!$sticky.offset()) { // make sure ".sticky" element exists

                var generalSidebarHeight = $sticky.innerHeight();
                var stickyTop = $sticky.offset().top;
                var stickOffset = 50;
                var stickyStopperPosition = $stickyrStopper.offset().top + $stickyrStopper.height();
                var stopPoint = stickyStopperPosition - generalSidebarHeight;
                var diff = stopPoint + stickOffset ;

                $(document).on('scroll', function () { // scroll event
                    var windowTop = $(document).scrollTop(); // returns number
                    if (windowTop <=  diff  && windowTop >= stickyTop ) {

                        $sticky.css({"margin-top": (windowTop +stickOffset)});
                    } else if(windowTop == stickyTop){
                        $sticky.css({"margin-top" : 0});
                    }
                });
            }
        },
        _onScroll: function (event) {
            $(document).on('scroll', function () {
                var position = $(document).scrollTop(),
                    index = -1;
                for (var i = 0; i < scrollNav.options.parPosition.length; i++) {
                    if (position <= scrollNav.options.parPosition[i]) {
                        index = i;
                        break;
                    }
                }
                if (index == -1) {
                    index = 0;
                }

                $('#productScroll a').removeClass('active');
                $('#productScroll a:eq(' + index + ')').addClass('active');
            });
            return this;
        }
    };

    return scrollNav;
});
