define([
    "jquery",
    'mage/template',
    'scrollnav'
], function($,template,scrollnav){


    var ajaxproduct = {
        showloader: false,
        options: {
            ajaxButton: '.open-simple-product',
            productModal: '#simple-product-modal',
            loading: '.custom-overlay'
        },
        _create: function () {

            ajaxproduct._loading();
            $(document).on('click', ajaxproduct.options.ajaxButton,  ajaxproduct._runAjax );
            $(document).on('hidden.bs.modal', ajaxproduct.options.productModal, ajaxproduct._resetBox);

        },
        _loading: function () {
            var $loading = $(ajaxproduct.options.loading);
            $loading.removeClass('in');
            if (ajaxproduct.showloader) {
                $(document)
                    .ajaxStart(function () {
                        $('body').addClass('modal-open');
                        $loading.addClass('in');
                    })
                    .ajaxComplete(function () {
                        $loading.removeClass('in');
                        ajaxproduct.showloader = false;
                    });
            }
        },
        _resetBox: function () {

            if($('#addtocartmodal').hasClass('in')) {
                $('#addtocartmodal').css('opacity', 1).show();
                if ($('body').hasClass('modal-open') == false) {
                    $('body').addClass('modal-open');
                };
            }

            $(ajaxproduct.options.productModal).css('opacity', 0);

            $('#simple-product-modal .modal-body .main .page-title-wrapper').empty().text('<%= content.title %>');
            $('#simple-product-modal .modal-body .main .wp-content').empty().text('<%= content.main %>');
            $('#simple-product-modal .modal-body .sidebar').empty().text('<%= content.sidebar %>');
        },
        _runAjax: function (event) {

            this.showloader = true;

            if($('#addtocartmodal').hasClass('in')) {
                $('#addtocartmodal').css('opacity', 0).hide();
            }

            var catalogAjax = location.protocol + '//' + location.host + '/catalogajax/product/view';
            var id = $(this).data('simpleproduct');
            $.ajax({
                url: catalogAjax,
                data: {ajax: '1', productId: id},
                type: "POST",
                dataType: 'json',
                async: true
            }).done(function (data) {
                var html = template(ajaxproduct.options.productModal, {content: data});
                $(ajaxproduct.options.productModal).html(html);
                $(ajaxproduct.options.productModal).bsmodal('show').css('opacity', 1);
                ajaxproduct.showloader = false;
            });
        }
    };

    return ajaxproduct;
});