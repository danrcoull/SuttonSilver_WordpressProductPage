define([
    "jquery",
    'mage/template',
    'scrollnav'
], function($,template,scrollnav){


    var ajaxproduct = {
        options: {
            ajaxButton: '.open-simple-product',
            productModal: '#simple-product-modal',
            loading: '.custom-overlay'
        },
        _create: function () {


            ajaxproduct._loading();
            $(document).on('click', ajaxproduct.options.ajaxButton, ajaxproduct._runAjax);
            $(document).on('hidden.bs.modal', ajaxproduct.options.productModal, ajaxproduct._resetBox);
            //$(document).on('shown.bs.modal', ajaxproduct.options.productModal, ajaxproduct._runAjax);


        },
        _loading: function () {
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
        _resetBox: function () {
            $('.modal-body .main .page-title-wrapper').empty().text('<%= content.title %>');
            $('.modal-body .main .wp-content').empty().text('<%= content.main %>');
            $('.modal-body .sidebar').empty().text('<%= content.sidebar %>');
        },
        _runAjax: function (event) {
            //$('.modal-backdrop').hide();
            //$(ajaxproduct.options.productModal).hide();
            var catalogAjax = location.protocol + '//' + location.host + '/catalogajax/product/view';
            var id = $(this).data('simpleproduct');

            $.ajax({
                url: catalogAjax,
                data: {ajax: '1', productId: id},
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                var html = template(ajaxproduct.options.productModal, {content: data});
                $(ajaxproduct.options.productModal).html(html);
                $(ajaxproduct.options.productModal).bsmodal('show');

                scrollnav._init($('.modal .sidebar'), $('.modal .main'));

                //$('.modal-backdrop').fadeIn(3000);
                //$(ajaxproduct.options.productModal).fadeIn(3000);

            });
        }
    };

    return ajaxproduct;
});