function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

$(document).ready(function() {


// функция, которая будет выполняться при событии window.onerror. 
// на входе она имеет три параметра:
// - сообщение об ошибке;
// - файл, в котором произошла ошибка;
// - номер строки с ошибкой.
function myErrHandler(message, url, line)
{
//   alert ('Error: '+message+' at '+url+' on line '+line);



	$.ajax({
		url: 'index.php?route=tool/logs/AllerJSError',
		type: 'get',
		data: '&message='+message+'&url='+url+'&line='+line ,
		dataType: 'json',
		success: function(json) {


			if (json) {

				console.log('#success');


			}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	
	


// что бы не выскочило окошко с сообщение об ошибке - 
// функция должна возвратить true
   return true;
}

// назначаем обработчик для события onerror
window.onerror = myErrHandler;


	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	$('#cart').on('click', function() { 
			clikcAction('cart', document.title);		
	});

	$('a').on('click', function() { 

		var link = this.href; 

		 if (link.indexOf('tel:') >= 0) {

			clikcAction('telephone', document.title);
		 } else if (link.indexOf('viber') >= 0) {

			clikcAction('viber', document.title);
		 } else if (link.indexOf('wa.me') >= 0) {

			clikcAction('whatsapp', document.title);
		 } else if (link.indexOf('mailto') >= 0) {

			clikcAction('mailto', document.title);
		 } else {

		 }
		
	});

	// Currency
	$('#form-currency .currency-select').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').val($(this).attr('name'));

		$('#form-currency').submit();
	});

	// Location
	$('#form-location .location-select').on('click', function(e) {
		e.preventDefault();

		$('#form-location input[name=\'location_id\']').val($(this).attr('name'));

		$('#form-location').submit();
	});

	// Language
	$('#form-language .language-select').on('click', function(e) {
		e.preventDefault();

		$('#form-language input[name=\'code\']').val($(this).attr('name'));

		$('#form-language').submit();
	});

	/* Search */
	$('#search input[name=\'search\']').parent().find('button').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=product/search';

		var value = $('header #search input[name=\'search\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	$('#search input[name=\'search\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('header #search input[name=\'search\']').parent().find('button').trigger('click');
		}
	});

	// Menu
	$('#menu .dropdown-menu').each(function() {
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();

		var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 10) + 'px');
		}
	});

	// Product List
	$('#list-view').click(function() {
		$('#content .product-grid > .clearfix').remove();

		$('#content .row > .product-grid').attr('class', 'product-layout product-list col-xs-12');
		$('#grid-view').removeClass('active');
		$('#list-view').addClass('active');

		localStorage.setItem('display', 'list');
	});

	// Product Grid
	$('#grid-view').click(function() {
		// What a shame bootstrap does not take into account dynamically loaded columns
		var cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		$('#list-view').removeClass('active');
		$('#grid-view').addClass('active');

		localStorage.setItem('display', 'grid');
	});

	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
		$('#list-view').addClass('active');
	} else {
		$('#grid-view').trigger('click');
		$('#grid-view').addClass('active');
	}

	// Checkout
	$(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
		if (e.keyCode == 13) {
			$('#collapse-checkout-option #button-login').trigger('click');
		}
	});

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});
});

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				$('.alert-dismissible, .text-danger').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {
						$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
					}, 100);

					$('html, body').animate({ scrollTop: 0 }, 'slow');

					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'reload': function() {
		
		$.ajax({
			url: 'index.php?route=checkout/cart/reload',
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {

			document.getElementById("cart").innerHTML = json;

			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
		
	}

}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				$('#wishlist-total span').html(json['total']);
				$('#wishlist-total').attr('title', json['total']);

				$('html, body').animate({ scrollTop: 0 }, 'slow');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					$('#compare-total').html(json['total']);

					$('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				html = '';

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
})(window.jQuery);

function clikcAction(name, title ) {
	  			
	$.ajax({
		url: 'index.php?route=tool/logs/getAction',
		type: 'get',
		data: 'name=' + name+'&title='+title ,
		dataType: 'json',
		success: function(json) {
			console.log(name);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

}

setTimeout("clikcAction('start_time', document.title  )", 500);
setTimeout("clikcAction('last_time', document.title  )", 60500);
setTimeout("clikcAction('last_time', document.title  )", 300500);

document.addEventListener("DOMContentLoaded", function(event) {
    cart.reload();
});

function plus_quantity( product_id, package_id ) {
    	
	let input_value = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).value) ;
	let input_step = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).step) ;
	let input_ratio = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).dataset.ratio) ; 
	let input_parent = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).dataset.parent) ; 
	var input_parent_value = input_value;

	if ( typeof(document.getElementById("package_"+product_id+"_"+input_parent)) != "undefined" && document.getElementById("package_"+product_id+"_"+input_parent) !== null  ) {

		let input_parent_value = parseFloat(document.getElementById("package_"+product_id+"_"+input_parent).value) ;

	}

	document.getElementById("package_"+product_id+"_"+package_id).value = input_value+input_step;
	document.getElementById("package_"+product_id+"_"+input_parent).value = input_parent_value+input_ratio;
	
	if ( input_value+input_step > 0 ) {
	document.getElementById("package_"+product_id+"_"+package_id).value = input_value+input_step;
	document.getElementById("package_"+product_id+"_"+input_parent).value = ((input_value+input_step)*input_ratio);
	} else {
	document.getElementById("package_"+product_id+"_"+package_id).value = 0;
	document.getElementById("package_"+product_id+"_"+input_parent).value = 0;
	zero_quantity(product_id);
	}
  }

function minus_quantity( product_id, package_id ) {
	console.log(document.getElementById("package_"+product_id+"_"+package_id).value) ;
	let input_value = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).value) ;
	let input_step = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).step) ;
	let input_ratio = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).dataset.ratio) ; 
	let input_parent = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).dataset.parent) ; 
	let input_parent_value = input_value;

	if ( typeof(document.getElementById("package_"+product_id+"_"+input_parent)) != "undefined" && document.getElementById("package_"+product_id+"_"+input_parent) !== null  ) {

		let input_parent_value = parseFloat(document.getElementById("package_"+product_id+"_"+input_parent).value) ;

	}

	if ( input_value-input_step > 0 ) {
	document.getElementById("package_"+product_id+"_"+package_id).value = input_value-input_step;
	document.getElementById("package_"+product_id+"_"+input_parent).value = ((input_value+input_step)*input_ratio);
	} else {
	document.getElementById("package_"+product_id+"_"+package_id).value = 0;
	document.getElementById("package_"+product_id+"_"+input_parent).value = 0;
	zero_quantity(product_id);
	}
 }
  function update_quantity( product_id, package_id ) {
let input_value = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).value) ;
let input_step = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).step) ;
let input_ratio = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).dataset.ratio) ; 
let input_parent = parseFloat(document.getElementById("package_"+product_id+"_"+package_id).dataset.parent) ; 
let input_parent_value = input_value;

if ( typeof(document.getElementById("package_"+product_id+"_"+input_parent)) != "undefined" && document.getElementById("package_"+product_id+"_"+input_parent) !== null  ) {

	let input_parent_value = parseFloat(document.getElementById("package_"+product_id+"_"+input_parent).value) ;

}

if ( input_value > 0 ) {
  document.getElementById("package_"+product_id+"_"+package_id).value = input_value;
  document.getElementById("package_"+product_id+"_"+input_parent).value = input_ratio*input_value;
} else {
  document.getElementById("package_"+product_id+"_"+package_id).value = 0;
  document.getElementById("package_"+product_id+"_"+input_parent).value = 0;
  zero_quantity(product_id);
}
  }
function zero_quantity(product_id) {
document.getElementById('error_quantity_'+product_id).innerHTML = '<span class="bg-warning row"><strong>Значение меньше 0 нельзя указывать!</strong></span>';

}


//  configurator
function backToElements( layout_image_id, image ) {


	$('.panel-conf-materials').removeClass("hidden");


	$('.panel-conf-items').remove();
	
}

function getitems(view_id, house_id , element_id) {

	$.ajax({
		url: '/index.php?route=configurator/configurator/item',
		type: 'get',
		data: 'house_id=' + house_id+'&catalog_id='+view_id ,
		dataType: 'json',
		success: function(json) {

			$('#element_'+element_id).addClass("hidden");

			if (json['success']) {
				$('.panel-conf-items').remove();

				$('#views').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				$('#element_materials_block_'+element_id).after(json['html']);
			}

		},
		
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

}
function setImages( layout_image_id, image, item_id ) {

	$("#layout_image_"+layout_image_id).attr("src", image );
	$("#layout_image_"+layout_image_id).data( "item_id", item_id );
	$("#form_layout_image_"+layout_image_id).val( item_id );
	

	$.ajax({
		url: 'index.php?route=configurator/configurator/iteminfo',
		type: 'get',
		data: document.location.search+'&item_id='+item_id+'&layout_id='+layout_image_id ,
		dataType: 'json',
		success: function(json) {


			if (json['success']) {
				$('.material_info_block_'+layout_image_id).remove();
				$('#element_materials_'+layout_image_id).after(json['html']);

			}
			if (json['cokol']) {


				var paramsString = document.location.search; // ?page=4&limit=10&sortby=desc
				var searchParams = new URLSearchParams(paramsString);
					
				house_id = searchParams.get("house_id"); // 4


		//		alert(json['cokol']);
				material_cokol(json['cokol'], 5, house_id);


			}
			

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

	
}
function material_cokol( material_id, element_id, house_id ) {

	$.ajax({
		url: 'index.php?route=configurator/configurator/materials',
		type: 'get',
		data: 'house_id=' + house_id+'&material_id='+material_id ,
		dataType: 'json',
		success: function(json) {

			if (json['success']) {
				$('#element_materials_5').after(json['html']);
			}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

}

function material_select( material_id, element_id, house_id ) {

//	$('.configurator-series-view' ).css( "display", "none" );
//	$('.'+material_id ).css( "display", "" );
$('.panel-conf-materials').remove();

	$.ajax({
		url: 'index.php?route=configurator/configurator/materials',
		type: 'get',
		data: 'house_id=' + house_id+'&material_id='+material_id ,
		dataType: 'json',
		success: function(json) {

			if (json['success']) {
				$('.panel-conf-items').remove();
				$('#element_materials_'+element_id).after(json['html']);
			}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

}

function form_save_conf( ) {


	$.ajax({
		url: 'index.php?route=configurator/configurator/saveImage',
		type: 'get',
		data: $("#form_save_image").serialize(), 
		dataType: 'json',
		success: function(json) {

			if (json['success']) {
			//	alert(json['text']);

				download_link = document.getElementById("download_link");
				open_link = document.getElementById("open_link");

				download_link.setAttribute("href", json['text']);
				open_link.setAttribute("href", json['text']);		  

				download_link.classList.remove("hidden");
				open_link.classList.remove("hidden");

			}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

	

}
