
(function($){
 	// Hello
    $.fn.extend({ 
         
        leanModal: function(options) {
 
            var defaults = {
                top: 100,
                overlay: 0.5,
                closeButton: null,
                cb : null
            }
            
            var overlay = $("<div id='lean_overlay'></div>");
            
            $("body").append(overlay);
                 
            options =  $.extend(defaults, options);
 
            return this.each(function() {
            
                var o = options;
               
                $(this).click(function(e) {
              
	              	var modal_id = $(this).attr("href");

					$("#lean_overlay").click(function() { 
	                     close_modal(modal_id);                    
	                });
	                
	                $(o.closeButton).click(function() { 
	                     close_modal(modal_id);                    
	                });
	                         	
	              	var modal_height = $(modal_id).outerHeight();
	        	  	var modal_width = $(modal_id).outerWidth();
	        	  	var modal_width = ( 60 * $(window).width() ) /100;

	        		$('#lean_overlay').css({ 'display' : 'block', opacity : 0 });

	        		$('#lean_overlay').fadeTo(200,o.overlay);

	        		$(modal_id).css({ 
	        		
	        			'display' : 'block',
	        			'position' : 'fixed',
	        			'opacity' : 0,
	        			'z-index': 11000,
	        			'left' : 20 + '%',
	        			// 'margin-left' : (modal_width/2) + "px",
	        			'top' : o.top + "px",
	        			'width' : modal_width + 'px',
	        		
	        		});

	        		$(modal_id).fadeTo(200,1);

	        		if ( options.cb ){
	        			options.cb(e);
	        		}
	        		

	                e.preventDefault();
                		
              	});
             
            });

			function close_modal(modal_id){

        		$("#lean_overlay").fadeOut(200);

        		$(modal_id).css({ 'display' : 'none' });
			
			}
    
        }
    });
     
})(jQuery);


(function( $ ) {
	'use strict';



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
					// alert("You must be logged in to do that");
					// if ( combunity.entrance_url ){
					// 	window.location = combunity.entrance_url
					// }
					jQuery(ele).find(".validation").text("Submission Failed").css('color', 'red');
					return;
				}
		    	var r = jQuery.parseJSON( response );
		    	cb(r);
		    }
		);
	}

	/**
	* Assign function to combunity
	*/
	combunity.performAPIcall = performAPIcall;
	combunity.editor = {};
	combunity.editor.temp = {};
	/**
	* Our loading image
	*/	
	var loadersrc = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0nMTIwcHgnIGhlaWdodD0nMTIwcHgnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIiBjbGFzcz0idWlsLWJhbGxzIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0ibm9uZSIgY2xhc3M9ImJrIj48L3JlY3Q+PGcgdHJhbnNmb3JtPSJyb3RhdGUoMCA1MCA1MCkiPgogIDxjaXJjbGUgcj0iNSIgY3g9IjMwIiBjeT0iNTAiPgogICAgPGFuaW1hdGVUcmFuc2Zvcm0gYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIiB0eXBlPSJ0cmFuc2xhdGUiIGJlZ2luPSIwcyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIGR1cj0iMXMiIHZhbHVlcz0iMCAwOzE5Ljk5OTk5OTk5OTk5OTk5NiAtMjAiIGtleVRpbWVzPSIwOzEiLz4KICAgIDxhbmltYXRlIGF0dHJpYnV0ZU5hbWU9ImZpbGwiIGR1cj0iMXMiIGJlZ2luPSIwcyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiICBrZXlUaW1lcz0iMDsxIiB2YWx1ZXM9IiNmZmY7Izk5OSIvPgogIDwvY2lyY2xlPgo8L2c+PGcgdHJhbnNmb3JtPSJyb3RhdGUoOTAgNTAgNTApIj4KICA8Y2lyY2xlIHI9IjUiIGN4PSIzMCIgY3k9IjUwIj4KICAgIDxhbmltYXRlVHJhbnNmb3JtIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgdHlwZT0idHJhbnNsYXRlIiBiZWdpbj0iMHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiBkdXI9IjFzIiB2YWx1ZXM9IjAgMDsxOS45OTk5OTk5OTk5OTk5OTYgLTIwIiBrZXlUaW1lcz0iMDsxIi8+CiAgICA8YW5pbWF0ZSBhdHRyaWJ1dGVOYW1lPSJmaWxsIiBkdXI9IjFzIiBiZWdpbj0iMHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiAga2V5VGltZXM9IjA7MSIgdmFsdWVzPSIjOTk5OyMwMDAiLz4KICA8L2NpcmNsZT4KPC9nPjxnIHRyYW5zZm9ybT0icm90YXRlKDE4MCA1MCA1MCkiPgogIDxjaXJjbGUgcj0iNSIgY3g9IjMwIiBjeT0iNTAiPgogICAgPGFuaW1hdGVUcmFuc2Zvcm0gYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIiB0eXBlPSJ0cmFuc2xhdGUiIGJlZ2luPSIwcyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIGR1cj0iMXMiIHZhbHVlcz0iMCAwOzE5Ljk5OTk5OTk5OTk5OTk5NiAtMjAiIGtleVRpbWVzPSIwOzEiLz4KICAgIDxhbmltYXRlIGF0dHJpYnV0ZU5hbWU9ImZpbGwiIGR1cj0iMXMiIGJlZ2luPSIwcyIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiICBrZXlUaW1lcz0iMDsxIiB2YWx1ZXM9IiMwMDA7I2ZmZiIvPgogIDwvY2lyY2xlPgo8L2c+PGcgdHJhbnNmb3JtPSJyb3RhdGUoMjcwIDUwIDUwKSI+CiAgPGNpcmNsZSByPSI1IiBjeD0iMzAiIGN5PSI1MCI+CiAgICA8YW5pbWF0ZVRyYW5zZm9ybSBhdHRyaWJ1dGVOYW1lPSJ0cmFuc2Zvcm0iIHR5cGU9InRyYW5zbGF0ZSIgYmVnaW49IjBzIiByZXBlYXRDb3VudD0iaW5kZWZpbml0ZSIgZHVyPSIxcyIgdmFsdWVzPSIwIDA7MTkuOTk5OTk5OTk5OTk5OTk2IC0yMCIga2V5VGltZXM9IjA7MSIvPgogICAgPGFuaW1hdGUgYXR0cmlidXRlTmFtZT0iZmlsbCIgZHVyPSIxcyIgYmVnaW49IjBzIiByZXBlYXRDb3VudD0iaW5kZWZpbml0ZSIgIGtleVRpbWVzPSIwOzEiIHZhbHVlcz0iI2ZmZjsjOTk5Ii8+CiAgPC9jaXJjbGU+CjwvZz48L3N2Zz4=';
	combunity.loadersrc = loadersrc
	/**	
	* Tick animation svg
	*/
	var ticksvg = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>'
	/**
	* Tinymce config
	*/
	var tinymceconfig = { 
		selector:'.combunity-rte', 
		menubar:false,
		statusbar: true,
		//menubar: '',
		toolbar: 'undo redo | bold italic strikethrough underline | emoticons  link image | blockquote | ',
		plugins: [
				'advlist autolink link image advlist textcolor emoticons wordcount'
			],
		advlist_bullet_styles: "square",
		init_instance_callback: function (editor) {
		    editor.on('keypress', function (e) {
		    	if ( e.key == '@' ){
		    		editor.windowManager.open({
					  title: 'Search user',
					  url: '/?combunity_custom&editor=frontend&type=userpicker',
					  width: 50,
					  height: 100,
					}, {
					  arg1: 42,
					  arg2: 'Hello world'
					});
					// var args = top.tinymce.activeEditor.windowManager.getParams();
					// console.log(args.arg1, args.arg2);
					e.preventDefault();

		    	}
		      // console.log('Element clicked:', e.target.nodeName);
		    });
		},
		setup: function (ed) {
	        ed.on('init', function(args) {

	            console.debug(args.target.id);
	            var i = tinymce.get(args.target.id).getParam('initiator');
	            if ( i ){
	            	hideloading(i.params.container);
	            	i.tempcontainer.show();
	            }

	        });
	    }

    }

	/**
	* Shows the loading image inside the container element
	* container: is a dom element
	*/
	var showloading = function( ctn ){
		var contents = $(ctn).contents();
		combunity.temporary = contents;
		var loader = jQuery("<div style='text-align:center'><img class='combunity-loader'  src='"+loadersrc+"'></img></div>");
		$(ctn).text('');
		$(ctn).append(loader);
	}
	combunity.showloading = showloading

	/**
	* Hides the loading image from the container element
	* container: is a dom element
	*/
	var hideloading = function( ctn ){
		$(ctn).find('.combunity-loader').remove();
		// $(ctn).html( combunity.temporary )
		
	}
	combunity.hideloading = hideloading

	/**	
	* Builds form using a schema obtained from a url
	*/
	var FormBuilder = function( data ){
		var that = this;
		this.params =  data;
		/**
		* Building the form save and cancel buttons
		*/
		this.btnsave = null
		this.btncancel = null
		/**
		* The validation dom element displays responses from the api after a submission
		*/
		this.validation = null;
		/**
		* Stores the original contents of the container before the form was loaded onto it
		*/
		this.containercontents = null;

		/**
		* This form structure will be obtained from the url
		*/
		this.formstruct = null;
		/**
		* Stores the nonce
		*/
		this.security = null;
		/**
		* Fetches the schema from the url
		*/
		this.fetch = function(){
			that.containercontents = that.params.container.contents();
			showloading( that.params.container );
			var submissionData = {};
			if ( that.params.id ){
				submissionData.id = that.params.id
			}
			if ( that.params.passdata ){
				console.log('I have pass data')
				submissionData.additional = {
					cforum : that.params.passdata.cforum
				}
			}
			var data = {
				'action': that.params.action,
				'data' : submissionData
			}
			var url = combunity.ajax_url
			performAPIcall( url, data, function(r){
				if ( !r["error"] ){
					if ( that.params.isdelete ){
						console.log( "Is delete section running ");
						hideloading(that.params.container)
						that.validation = jQuery("<div></div>")
						that.validation.html(r["info"])
						hideloading(that.params.container)
						that.params.container.append( that.validation )
					}else{
						that.formstruct = r["info"];
						that.security = r["security"]
						that.build();
					}

				}else{

					that.validation = jQuery("<div></div>")
					that.validation.attr('class' , 'combunity-validation-error')
					that.validation.text(r["info"])
					hideloading(that.params.container)
					that.params.container.append( that.validation )
	

				}
			});
		}
		this.build = function(){
			/**
			* Lets clear the container first
			*/
			// that.params.container.text('');
			var ctn = jQuery('<div></div>');
			for (var i = 0; i < that.formstruct.length; i++) {
				var ele = that.formstruct[i]
				/**
				* t is the element being constructed on the dom right now
				*/
				var t = null;
				switch( ele.etype ){
					case 'select':
						var t = jQuery('<select></select>');
						t.attr('name', ele.name)
						t.attr('value', ele.value)
						for (var j = ele.options.length - 1; j >= 0; j--) {
							var z = ele.options[j]
							var o = jQuery('<option></option>')
							if ( z.selected ){
								o.attr('selected', true );
							}
							o.text(z.name)
							o.attr('value', z.slug)
							t.append(o)
						}
						that.formstruct[ i ].element = t;
						break;
					case 'text':
						var t = jQuery( '<input>' );
						t.attr('type', 'text' )
						t.attr('name', ele.name)
						t.attr('value', ele.value)
						that.formstruct[ i ].element = t;
						break;
					case 'textarea':
						var t = jQuery( '<textarea></textarea>' );
						t.attr('name', ele.name)
						t.text(ele.value)
						that.formstruct[ i ].element = t;
						if ( ele.rte ){
							t.addClass('combunity-rte')
							// var simplemde = new SimpleMDE({ element: t });
							// that.formstruct.simplemdelement = simplemde;
						}
						break;
					case 'hidden':
						var t = jQuery( '<input>' );
						t.attr('type', 'hidden' )
						t.attr('name', ele.name)
						t.attr('value', ele.value)
						that.formstruct[ i ].element = t;
						break;
					case 'notice':
						var t = jQuery( '<p></p>' );
						t.attr('name', ele.name)
						if ( ele.class ){
							t.attr('class', ele.class);
						}
						t.html(ele.value)
						that.formstruct[ i ].element = t;
						break;
				}
				if ( that.params.labels ){
					if ( ele.etype != 'hidden' ){
						var l = jQuery('<label></label>');
						if ( !ele.label ){
							l.text(ele.name)
						}else{
							l.text(ele.label)
						}
						
						ctn.append(l)
					}
				}
				// Appending t to the container
				ctn.append( t )
				// tinymce.init({ target:t, menubar: '' });
			}
			that.validation = jQuery("<div></div>")
			that.validation.attr('class', 'combunity-form-validation')
			var savebtntext = "Save"
			if ( that.params.savebtntext ){
				savebtntext = that.params.savebtntext
			}
			that.btnsave = jQuery("<button>"+savebtntext+"</button>")
			that.btnsave.attr('class', 'btn btn-primary combunity-ajax-save')
			that.btncancel = jQuery("<button>Cancel</button>")
			var hiddenclass = '';
			if ( that.params.nocancel ){
				hiddenclass = ' hidden ';
			}
			that.btncancel.attr('class', 'btn btn-info combunity-ajax-cancel' + hiddenclass )
			that.btnsave.on('click', that.save)
			that.btncancel.on('click', that.cancel)
			var btncontainer = $('<div></div>');
			btncontainer.attr('class', 'combunity-ajax-btn-container')
			
			btncontainer.append( that.btncancel )
			btncontainer.append( that.btnsave )
			ctn.append( btncontainer )
			// that.params.container.append( that.btnsave )
			// that.params.container.append( that.btncancel )
			ctn.append( that.validation )
			// hideloading( that.params.container );
			tinymceconfig.selector = '.combunity-rte'
			console.log('Assigning intiator;')
			tinymceconfig.initiator = that;
			that.tempcontainer = ctn;
			ctn.hide();
			that.params.container.append( ctn )
			tinymce.init(
				tinymceconfig
				// formats: {
				// 	bold: {inline : 'span', 'classes' : 'bold'},
				//     italic: {inline : 'span', 'classes' : 'italic'},
				//     underline: {inline : 'span', 'classes' : 'underline', exact : true},
				//     strikethrough: {inline : 'del'},
				// },
				// toolbar: 'newdocument, bold, italic, underline, strikethrough, bullist, numlist, blockquote, link image',
				// block_formats: 'Paragraph=p;Header 1=h1;Header 2=h2;Header 3=h3' 
			);
		}
		this.init = function(){
			this.fetch();
		}
		this.cancel = function(){
			that.params.container.html( that.containercontents )
		}
		this.save = function(){
			that.containercontents = that.params.container.contents();
			showloading( that.params.container );
			// /**
			// * Building object to be sent back to the Combunity api
			// */
			var submissionData = {}
			for (var i = that.formstruct.length - 1; i >= 0; i--) {
				if ( that.formstruct[i].rte ){
					submissionData[ that.formstruct[i].name ] = tinyMCE.activeEditor.getContent();
				}else{
					submissionData[ that.formstruct[i].name ] = that.formstruct[i].element.val()
				}
			}
			var data = {
				'action': that.params.action_save,
				'data' : submissionData,
				'security' : that.security
			}
			performAPIcall( combunity.ajax_url, data, function(r){
				hideloading( that.params.container )
				that.params.container.text('')
				that.params.container.append( that.validation )
				if ( !r['error'] ){
					for (var i = that.formstruct.length - 1; i >= 0; i--) {
						that.formstruct[i].element.remove()
					}
					that.validation.html('');
					var tickanim = $('<div style="text-align:center" >'+ticksvg+'</div>');
					that.validation.append( tickanim )
					// $(".trigger").toggleClass("drawn")
					var response = r["info"]["validation"];
					if ( !that.params.align ){
						that.params.align = 'left';
					}
					that.validation.append('<div style="text-align:'+that.params.align+'" >'+response+'</div>')
					if ( !that.params.animatedtick ){
						setInterval(function(){
							tickanim.remove();
						},1200)
						
					}
				}else{
					// console.log("Updating validation")
					that.validation.html(r["info"])
				}
			});
		}
		this.init();
	}

	$(function() {
		if ( $("#comment").length > 0 && (typeof(tinymce) !== 'undefined') ){
			$('#commentform').attr('novalidate', '');
			tinymceconfig.selector = '#comment'
			var commenteditor = tinymce.init(
				tinymceconfig
			)
			$('#submit').on('click', function(ev){
				$('#comment').val( tinyMCE.activeEditor.getContent() );
			});
		}



		var detectMobile = function(){
			// return true;
			var checkThis = null;
			var info = [];
			if (window.screen) {
	            info['pixelDepth'] = window.screen.pixelDepth;
	            info['availWidth'] = window.screen.availWidth;
	            info['availHeight'] = window.screen.availHeight;
	            info['actWidth'] = window.screen.height;
	            info['actHeight'] = window.screen.width;
	            info['dppx'] = window.devicePixelRatio;
	        }
	        if (info['actWidth'] < info['actHeight'])
	        	checkThis = info['actWidth'];
	        if (info['actHeight'] < info['actWidth'])
	        	checkThis = info['actHeight'];
	        
	        if (checkThis<=414){
	        	return true;
	        }
	        return false;
		}

		combunity.detectMobile = detectMobile;

		$('#submitpost').on('click', function(ev){
			if (typeof(tinymce) === 'undefined') {
				$('#submitpost').attr('href', '#loginmodal')
			}
		});

		$("#submitpost").on("click", function(ev){
			// console.log("Click detected");
		})

		if ( !combunity.detectMobile() ){
			$("#submitpost").leanModal({ top : 100, closeButton: ".modal_close", cb:function(){
				console.log("Clicked on submit post")
				if (typeof(tinymce) === 'undefined') {

					return
				}
				// if ( !combunity.logged_in  ){
				// 	window.location = jQuery(this).data("guest-redirect")
				// 	return
				// }
				var container = jQuery("#submitpostform .submissionform")
				var submissionData = {};
				var form = new FormBuilder({
					action:'combunity_get_forum_posts_form_meta', 
					passdata : {
						cforum : $('#submitpost').data('forum')
					},
					action_save : 'combunity_forum_posts_post',
					// id: submissionData.id ,
					container : container,
					labels: true,
					animatedtick: true,
					align: 'center',
					nocancel: true,
					savebtntext: 'Post',
				});
			} });	
		}



		$(".newpost").on("click",function(){
			$("#submitpost").trigger('click');
		})
		
		$('.comment-reply-link').on('click', function(e){
			tinymceconfig.selector = '#comment'
			tinymce.remove();
			setInterval(function(){
				tinymce.init(tinymceconfig);
			},100);
			// tinymce.init(tinymceconfig);
		});

		$(".combunity-voting-bar-vote-comment").on("click", function(){
			var that = this;
			var postId = jQuery(this).parent().parent().data("postid");
			var postType = jQuery(this).data("posttype");
			var parentEle = jQuery(this).parent();
			var clickedEle = this;
			var submissionData = {
				id : postId,
				vote_type : postType
			}
			// console.log(submissionData);
			var data = {
				'action': 'combunity_post_comment_vote',
				'data' : submissionData,
			}
			$(this).css('position', 'relative');
			$(this).css('z-index', 1000);
			$(this).animate({
                fontSize: "30px",
                left:"-14px",
                top:"-20px"
            }, 1000);
            var prevfontsize = $(this).css('font-size');
            var prevtop = $(this).css('top');
            console.log(prevfontsize)
			performAPIcall( combunity.ajax_url, data, function(r){
				if (!r["error"]){
					var newclass = "combunity-vote-"+postType+"-highlight";
					$(clickedEle).addClass( newclass );
				}
				$(that).animate({
	                fontSize: prevfontsize,
	                left:"0px",
	                top:prevtop
	            }, 1000);
			});
		})

		$(".edit-comment").on("click", function( e ){
			var currentElement = this;
			console.log("Edit comment");
			e.preventDefault();
			var submissionData = {};
			submissionData.id = jQuery(this).data("id");
			var data = {
				'action': 'combunity_get_comment',
				'data' : submissionData
			}
			var container = jQuery(currentElement).closest('.combunity-comment-box').find(".comment-text")
			var form = new FormBuilder({
				action:'combunity_get_comment_form_meta', 
				action_save : 'combunity_post_comment',
				id: submissionData.id ,
				container : container,
				labels: false
			});
		})

		$(".combunity-edit-thread-link").on("click", function(e){
			var currentElement = this;
			console.log("Edit thread");
			e.preventDefault();
			var submissionData = {};
			submissionData.id = jQuery(this).data("id");
			var container = jQuery(currentElement).closest('.post-body').find(".post-container")
			var form = new FormBuilder({
				action:'combunity_get_edit_thread_form_meta', 
				action_save : 'combunity_post_thread',
				id: submissionData.id ,
				container : container,
				labels: false
			});
		})

		$(".combunity-delete-thread-link").on("click", function(e){
			var currentElement = this;
			console.log("Delete thread");
			e.preventDefault();
			var submissionData = {};
			submissionData.id = jQuery(this).data("id");
			var container = jQuery(currentElement).closest('.post-body').find(".post-container")
			var r = confirm("Are you sure you want to delete this thread?");
			if (r == true) {
				var form = new FormBuilder({
					action:'combunity_get_delete_thread', 
					action_save : 'combunity_post_thread',
					id: submissionData.id ,
					container : container,
					labels: false,
					isdelete : true
				});
			}
		})

		$("#commentform").on("submit", function(ev){
			// console.log(combunity.logged_in)


			if ( combunity.logged_in == "0"){
				window.location = combunity.entrance_url;
				ev.preventDefault();
				return;
			}
		})


		if ( jQuery(".submitthread").length > 0 ){
			if ( combunity.logged_in == 0 ){
				jQuery(".submitthread").html( jQuery( "#loginmodal" ).html() );
				return;
			}else{
				var container = jQuery(".submitthread")
				var submissionData = {};
				var form = new FormBuilder({
					action:'combunity_get_forum_posts_form_meta', 
					// passdata : {
					// 	cforum : $('#submitpost').data('forum')
					// },
					action_save : 'combunity_forum_posts_post',
					// id: submissionData.id ,
					container : container,
					labels: true,
					animatedtick: true,
					align: 'center',
					nocancel: true,
					savebtntext: 'Post',
				});
			}
			
		}

		if ( jQuery(".entrance").length > 0 ){
			jQuery(".entrance").html( jQuery( "#loginmodal" ).html() );
		}





	});

})( jQuery );
