define([
    "jquery"
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
            scrollNav._onScroll();
            scrollNav._stick();
        },
        _onClick: function (event) {
            $('#productScroll a').on('click', function (event) {
                $('html, body').animate({
                    scrollTop: $($.attr(this, 'href')).offset().top
                }, 500);
                $('#productScroll a').removeClass('active');
                $(this).addClass('active');

                event.preventDefault();
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
