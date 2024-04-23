(function ($) {
    'use strict';

    var cix_wishlist = {


        add_to_wishlist: function () {
            $(".jvm_add_to_wishlist").on("click", function (e) {
                console.log('clicked');
                // get data-product-id from the button
                var product_id = $(this).data('product-id');
                e.preventDefault();
                var wishlist_btn = $(this);
                wishlist_btn.addClass('loading');
                var child_res_form = jQuery("#registration-fee-form").serializeArray();
                // console.log(child_res_form);
                // console.log(camp_addon_price);
                $.ajax({
                    url: cix_wishlist_args.ajax_url,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'cix_update_wishlist',
                        product_id: product_id,
                        nonce: cix_wishlist_args.nonce
                    },
                    success: function (res) {
                        wishlist_btn.removeClass('loading');
                        wishlist_btn.addClass('in_wishlist');
                        if (res.data.show_icon == 0) {
                            wishlist_btn.hide();
                        }
                        console.log('Added');
                        console.log(res.data.show_icon);



                    },
                    error: function () {
                        console.log('Ajax Error: cix_update_wishlist ');
                    }
                });

            });


        },

        misc: function () {



        },

    };

    $(document).ready(function () {

        cix_wishlist.add_to_wishlist();

    });



})(jQuery);

// Other code using $ as an alias to the other library