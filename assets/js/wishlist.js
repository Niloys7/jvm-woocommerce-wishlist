/*!
 * JVM WooCommerce Wishlist 1.0.0
 */

var JVMWooCommerceWishlist = JVMWooCommerceWishlist || {},
	JVMWooCommerceWishlistJSParams = JVMWooCommerceWishlistJSParams || {},

	JVMWooCommerceWishlist = function ($) {

		'use strict';

		return {

			clickEventFlag: false,
			wishlistArray: [],
			cookieName: '',
			cookie: null,

			/**
			 * Init UI
			 */
			init: function () {

				this.cookieName = JVMWooCommerceWishlistJSParams.siteSlug + '_wc_wishlist';
				this.cookie = Cookies.get(this.cookieName); // get raw cookie value set by PHP
				this.wishlistArray = this.cookie ? this.cookie.split(/,/) : []; // set default wishlist as array
				this.wishlistArray = this.arrayUnique(this.wishlistArray); // remove duplicates

				this.build();

				// Avoid firing click event several times and mess up everyting
				if (!this.clickEventFlag) {
					this.clickEvent();
					this.removeButton();
				}

				this.clickEventFlag = true;
			},

			/**
			 * Set class and button text
			 */
			build: function () {
				var _this = this,
					$button,
					productId,
					addText,
					removeText;

				$('.jvm_add_to_wishlist').each(function () {

					$button = $(this),
						productId = $button.data('product-id'),
						addText = JVMWooCommerceWishlistJSParams.l10n.addToWishlist,
						removeText = JVMWooCommerceWishlistJSParams.l10n.removeFromWishlist;

					if (productId) {

						// in list
						if (-1 !== $.inArray(productId.toString(), _this.wishlistArray)) {

							$button.addClass('in_wishlist')
								.attr('title', removeText);

						} else {

							$button.removeClass('in_wishlist')
								.attr('title', addText);
						}
					}
				});
			},

			/**
			 * Action on click
			 */
			clickEvent: function () {

				var _this = this,
					$button,
					productTitle,
					productId;

				$(document).on('click', '.jvm_add_to_wishlist', function (event) {

					event.preventDefault();

					$button = $(this);
					productId = $button.data('product-id');
					productTitle = $button.data('product-title');

					if (productId) {

						if ($button.hasClass('in_wishlist')) {

							_this.removeFromWishlist($button, productId, productTitle);

						} else {
							_this.addToWishlist($button, productId, productTitle);
						}
					}
				});
			},

			/**
			 * Add product to wishlist
			 */
			addToWishlist: function ($button, productId, productTitle) {

				var text = JVMWooCommerceWishlistJSParams.l10n.removeFromWishlist;

				$button.addClass('in_wishlist');

				if (-1 === $.inArray(productId, this.wishlistArray)) {

					this.wishlistArray.push(productId.toString());

					this.wishlistArray = this.arrayUnique(this.wishlistArray);
					this.updateDataBase(this.wishlistArray);

					Cookies.set(this.cookieName, this.wishlistArray.join(','), { path: '/', expires: 7 });

					$button.attr('title', text);

					$.event.trigger({
						type: "add.JVMWooCommerceWishlist",
						id: productId,
						title: productTitle
					});
				}
			},

			/**
			 * Remove product from wishlist
			 */
			removeFromWishlist: function ($button, productId, productTitle) {

				$button.removeClass('in_wishlist');

				var index = this.wishlistArray.indexOf(productId.toString()),
					text = JVMWooCommerceWishlistJSParams.l10n.addToWishlist;

				if (-1 !== index) {
					this.wishlistArray.splice(index, 1);
				}

				this.wishlistArray = this.arrayUnique(this.wishlistArray);

				this.updateDataBase(this.wishlistArray);

				//console.log(this.wishlistArray.join( ',' ));
				Cookies.set(this.cookieName, this.wishlistArray.join(','), { path: '/', expires: 7 });

				$button.attr('title', text);

				$.event.trigger({
					type: "remove.JVMWooCommerceWishlist",
					id: productId,
					title: productTitle
				});
			},

			/**
			 * Remove wishlist button on wishlist page
			 */
			removeButton: function () {
				var _this = this,
					$button,
					$tableCell,
					productId;

				$(document).on('click', '.www-remove', function (event) {
					event.preventDefault();

					var $button = $(this),
						$tableCell = $button.parent().parent(),
						productId = $button.data('product-id'),
						productTitle = $button.data('product-title');

					if (productId) {
						_this.removeFromWishlist($button, productId, productTitle);
						$tableCell.addClass('hidden');

						if (_this.wishlistArray.length == 0) {
							$('table.jvm-woocommerce-wishlist-table').addClass('hidden');
							$('.empty-wishlist').removeClass('hidden');
						}
					}
				});
			},

			/**
			 * Update database through AJAX
			 */
			updateDataBase: function (wishlistArray) {

				var data = {
					wishlistIds: wishlistArray,
					userId: JVMWooCommerceWishlistJSParams.userId,
					action: 'jvm_woocommerce_wishlist_ajax_update_wishlist'
				};

				$.event.trigger({
					type: "beforeupdate.JVMWooCommerceWishlist",
					wishlist: wishlistArray,
				});

				$.post(JVMWooCommerceWishlistJSParams.ajaxUrl, data, function (response) {
					$.event.trigger({
						type: "afterupdate.JVMWooCommerceWishlist",
						wishlist: wishlistArray,
					});
				});
			},

			/**
			 * Remove duplicate from array
			 */
			arrayUnique: function (array) {
				var result = [];
				$.each(array, function (i, e) {
					if (-1 == $.inArray(e, result)) {
						result.push(e);
					}
				});
				return result;
			}
		};

	}(jQuery);

(function ($) {

	'use strict';

	$(document).ready(function () {
		JVMWooCommerceWishlist.init();
	});

})(jQuery);