/*
* Core Ajax for application
*/
var coreAjax = {
	call : function (url, data, callBackOK, callBackKO, type, isAsync) {
		type = typeof type !== 'undefined' ? type: 'POST';
		$.ajax(
			{
				async : isAsync ? false : true,
				type: type,
				url: url,
				dataType: "json",
				data: data,
				beforeSend: function() {
					ajaxEventHandle.showMask();
				},
				success: function(response) {
					ajaxEventHandle.hideMask();

					if( callBackOK ){
						callBackOK(response);
					}
				},
				error: function(xhr, status, error) {
					ajaxEventHandle.hideMask();

					if( callBackKO ) {
						callBackKO();
					}
				}
			});
	},

	callWithoutMask : function (url, data, callBackOK, callBackKO, type, isAsync) {
		type = typeof type !== 'undefined' ? type: 'POST';
		$.ajax(
			{
				async : isAsync ? false : true,
				type: type,
				url: url,
				dataType: "json",
				data: data,
				success: function(response) {
					if( callBackOK ){
						callBackOK(response);
					}
				},
				error: function(xhr, status, error) {
					if( callBackKO ) {
						callBackKO();
					}
				}
			});
	},

}

var ajaxEventHandle = {
		maskDiv		: null,
		
		init : function () {
			// $.ajaxSetup({
			// 	headers: {
			// 		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			// 	}
			// });

			
			// $( document ).ajaxStart( ajaxEventHandle.ajaxStart );
			// $( document ).ajaxStop( ajaxEventHandle.ajaxStop );
			

			$('<div class="mask"><div class="ajax_spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>').appendTo(document.body).hide();
			ajaxEventHandle.maskDiv = $('div.mask');
		},
		
		ajaxStart : function() {
			// ajaxEventHandle.showMask();
		},
		
		ajaxStop : function() {
			// ajaxEventHandle.hideMask();
		},
		
		showMask : function() {
			ajaxEventHandle.maskDiv.show();
		},
		
		hideMask : function() {
			ajaxEventHandle.maskDiv.hide();
		}
}

ajaxEventHandle.init();