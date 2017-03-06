define([
    "jquery",
    'mage/template',
    "bootstrapjs"
], function($,template){
    "use strict";

    var scrollNav = {
        options : {
            parPosition: []

        },
        _init : function() {
            $('.wrapper-block').each(function() {
                scrollNav.options.parPosition.push($(this).offset().top);
            });

            console.log(scrollNav.options.parPosition);

            scrollNav._onClick()._onScroll();
        },
        _onClick : function(event) {
            $('#productScroll a').on('click', function(event) {
                $('html, body').animate({
                    scrollTop: $($.attr(this, 'href')).offset().top
                }, 500);
                $('#productScroll a').removeClass('active');
                $(this).addClass('active');

                event.preventDefault();
            });
            return this;
        },
        _onScroll : function(event)
        {
            $(window).on('scroll', function() {
                var position = $(document).scrollTop() + 100,
                    index = -1;
                for (var i=0; i<scrollNav.options.parPosition.length; i++) {
                    if (position  <= scrollNav.options.parPosition[i]) {
                        index = i;
                        break;
                    }
                }
                if(index == -1)
                {
                    index = 0;
                }

                $('#productScroll a').removeClass('active');
                $('#productScroll a:eq('+index+')').addClass('active');
            });
            return this;
        }
    };

    scrollNav._init();

    var ajaxproduct = {
        options: {
            ajaxButton: '.open-simple',
            productModal: '#simple-product-modal',
            loading: '.loading-overlay'
        },
        _create: function() {
            ajaxproduct._loading();
            $(document).on('click',ajaxproduct.options.ajaxButton, ajaxproduct._runAjax);
            $(document).on('hidden.bs.modal',ajaxproduct.options.productModal, ajaxproduct._resetBox);


        },
        _loading : function() {
            var $loading = $(ajaxproduct.options.loading).removeClass('in');
            $(document)
                .ajaxStart(function () {
                    $('body').addClass('modal-open');
                    $loading.addClass('in');
                })
                .ajaxComplete(function () {
                    $loading.removeClass('in');
                });
        },
        _resetBox : function() {
            $('.modal-body .main .page-title-wrapper').empty().text('<%= content.title %>');
            $('.modal-body .main .wp-content').empty().text('<%= content.main %>');
            $('.modal-body .sidebar').empty().text('<%= content.sidebar %>');
        },
        _runAjax: function (event) {
            event.preventDefault();
            var catalogAjax = 'http://local.cilex.co.uk/catalogajax/product/view';
            var id =  $(this).data('id');


            $.ajax({
                showLoader: true,
                url: catalogAjax,
                data: {ajax: '1', productId: id},
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                var html = template(ajaxproduct.options.productModal, {content:data});
                $(ajaxproduct.options.productModal).html(html);
                $(ajaxproduct.options.productModal).modal('show');
            });
        }
    };

    return ajaxproduct;

});