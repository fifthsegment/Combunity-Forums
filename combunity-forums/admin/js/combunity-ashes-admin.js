(function( $ ) {
	'use strict';
	$(document).ready(function(){


		/**
		 * A wrapper to perform API calls
		 *
		 */
		 var performAPIcall=function(url, data, cb, ele){
			jQuery.post(
		    url, 
		    data,
			function(response){
					if ( response == 0){
						alert("You must be logged in to do that");
						jQuery(ele).find(".validation").text("Submission Failed").css('color', 'red');
						return;
					}
			    	var r = jQuery.parseJSON( response );
			    	cb(r);
			    }
			);
		}

		$("#combunitysubscribeno").on("click", function(e){

			var that = this
			var xxdata = {}

			xxdata.no = "true"

			var data = {
				'action': 'combunity_admin_subscribe',
				'data' : xxdata
			}

			var url = combunity.ajax_url

			performAPIcall( url, data, function(r){

				$(that).parent().parent().remove();
				
			});

			e.preventDefault();


		});

		$("#combunitysubscribeyes").on("click", function(e){

			var that = this
			var xxdata = {}

			xxdata.email = $("#subscribeemail").val();

			var data = {
				'action': 'combunity_admin_subscribe',
				'data' : xxdata
			}

			var url = combunity.ajax_url

			performAPIcall( url, data, function(r){

				$(that).parent().find(".validation").remove();

				var validation = jQuery("<div class='validation'></div>");
				
				validation.html(r["info"]);

				$(that).parent().append(validation)
				
			});

			e.preventDefault();


		});


		$('#install_pages').on('click', function(e){
			var that = this
			var data = {
				'action': 'combunity_install_pages',
				'data' : {}
			}

			var url = combunity.ajax_url
			performAPIcall( url, data, function(r){

				$(that).parent().find(".validation").remove();

				var validation = jQuery("<div class='validation'></div>");
				
				validation.html(r["info"]);

				$(that).parent().append(validation)
				
			});

			

			e.preventDefault();
		})
	})
	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );
