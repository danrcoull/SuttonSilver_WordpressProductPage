define([
    "jquery",
    'mage/template',
    'scrollnav'
], function($,template,scrollnav){
    "use strict";



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
                scrollnav._init($('.modal .sidebar'), $('.modal .main'));
            });
        }
    };

    return ajaxproduct;

});