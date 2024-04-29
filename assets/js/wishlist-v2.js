(function ($) {
    'use strict';

    var cix_wishlist = {


        add_to_wishlist: function () {
            $(".jvm_add_to_wishlist").on("click", function (e) {
                console.log('clicked');
                // get data-product-id from the button
                var product_id = $(this).data('product-id'),
                    remove_product = $(this).data('remove');
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
                        nonce: cix_wishlist_args.nonce,
                        remove_product: remove_product,
                    },
                    success: function (res) {
                        wishlist_btn.removeClass('loading');
                        wishlist_btn.addClass('in_wishlist');
                        wishlist_btn.addClass('wishlist_added');

                        // Redirect to the wishlist page
                        if (res.data.redirect && !res.data.removed && !res.data.already_in_wishlist){
                            window.location.href = res.data.redirect_url;
                        }
                        
                        if (res.data.removed) {
                            wishlist_btn.removeClass('in_wishlist');
                        }

                        if (res.data.popup){
                            $('#wishlist-modal').html(res.data.template);
                            console.log(res.data.template);
                            $('#wishlist-modal').modal({
                                fadeDuration: 200

                            });
                        }
                       
                        console.log(res.data);



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