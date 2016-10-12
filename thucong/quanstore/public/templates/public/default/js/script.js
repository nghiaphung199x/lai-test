$(document).ready(function() {
	//menu
	$("#showmenu").click(function(e){
		e.preventDefault();
		$("#menu-mobile").toggleClass("show");
	});
	$('#click-out-menu').click(function(){
		$('.header-moible-left .visible-xs').removeClass('show');
	});
	$('.submenu-click').click(function(e){
		e.preventDefault();
		ul = $(this).next('ul');
		if(ul.is(':visible'))$(this).next('ul').slideUp();
		else $(this).next('ul').slideDown();

	})

	// search cart button
	var flagtable = 'hide';
	var flag2 = 'search-hide';
	$('#cart-click').click(function(){
		if (flagtable == 'hide'){
			$('.table-cart-home').addClass('show-table');
			flagtable = 'show';
		}
		else if (flagtable == 'show'){
			$('.table-cart-home').removeClass('show-table');
			flagtable = 'hide';
		}		
	});
	$('#click-out-cart').click(function(){
		if (flagtable == 'show'){
			$('.table-cart-home').removeClass('show-table');
			flagtable = 'hide';
		}
	});
	
	$('.search-home a#search').click(function(){
		if (flag2 == 'search-hide'){
			$('.wrap-search-home').addClass('show-search');
			$('.search-home #search i').removeClass('fa-search');
			$('.search-home #search i').addClass('fa-times');
			flag2 = 'search-show';
		}
		else if (flag2 == 'search-show'){
			$('.wrap-search-home').removeClass('show-search');
			$('.search-home #search i').removeClass('fa-times');
			$('.search-home #search i').addClass('fa-search');
			flag2 = 'search-hide';
		}
	});	
	
	//fix height
	var h = 220;
	$('.img-product').each(function(){
		if($(this).height() > h){
			h = $(this).height();
		}
	})
	$('.item-product .img-product').height(h);
	
	//scroll
	$(window).scroll(function() {
		if ($(this).scrollTop() > 100) {
			$('.scrollToTop').fadeIn();
		} else {
			$('.scrollToTop').fadeOut();
		}
	});

	//Click event to scroll to top
	$('.scrollToTop').click(function() {
		$('html, body').animate({
			scrollTop: 0
		}, 600);
		return false;
	});
	
	
	//add cart
	$( ".block_product .item-product .buy-now" ).click(function() {
		var urlAjax = base_url + '/shopping/cart/add';
		var id = $(this).attr('data-variantid');

		$.ajax({
			type: "POST",
			url: urlAjax,
			data: {
				id : id,
				quantity: 1,
				type: 'muangay'
			},
			success: function(html){
				if(html == 'ok')
					window.location = base_url + '/cart';
		    }
		});
	});
	
	//redirect checkout
	$( ".checkout_btn" ).click(function(e) {
		e.preventDefault();
		window.location = base_url + '/checkout';
	});
	
	
	//footer
	setFooter();
	$( window ).resize(function() {
		setFooter();
	});
	
});


function setFooter() {
	var body_height = $('body').height();
	var window_height = $(window).height();
	var footerHegiht = $('#footer').outerHeight();
	var body_padding_bottom = footerHegiht + 20;
	
	$("#footer").css({
		      "position": "absolute",
		      "width": "100%",
		      "margin-top": "0px",
		      "bottom": "0px"
	   		});
	
	if(body_height < footerHegiht){
		$("body").css({
		      "position": "relative",
		      "padding-bottom": body_padding_bottom+"px"
		     
	 		});
	}else {
		window_height = $(window).height();
		$("body").css({
		      "position": "relative",
		      "padding-bottom": body_padding_bottom+"px",
		      "min-height" : window_height+"px"
	 		});
	}
		
}
