jQuery(document).ready(function($) {
	 
        $('.bsp-quote-link').click(function() {

                var id = $(this).attr("href").substr(1);

                var data = {
                        'action' : 'get_status_by_ajax',
                        'id' : id,
                        'security': bsp_ajax_object.quote
                }
                $.post(bsp_ajax_object.ajax_url, data, function(response) {

                        // if tinymce editor currently visible
                        if( $('.mce-tinymce.mce-container.mce-panel').is(':visible') ) {
                                tinymce.get("bbp_reply_content").execCommand("mceInsertContent", false, response); 
                        }

                        // if default text editor currently visible
                        if( $('textarea#bbp_reply_content').is(':visible') ) {
                                quote = response;
                                replyTextfield = $("#bbp_reply_content");
                                text = replyTextfield.val();
                                if(jQuery.trim(text) != ''){
                                        text += "\n\n";
                                }
                                text += quote;
                                replyTextfield.val(text);
                        }
						
						// if we are in buddyboss
						var buddyboss = document.getElementById("bbp_editor_reply_content_forums_editor_3");
						if(buddyboss) {
                                quote = response;
                                replyTextfield = $("#bbp_editor_reply_content_forums_editor_3");
                                text = replyTextfield.val();
                                if(jQuery.trim(text) != ''){
                                        text += "\n\n";
                                }
                                text += quote;
                                document.getElementById("bbp_editor_reply_content_forums_editor_3").innerHTML = text;
                       }
						
						

                        // scroll to new_post form
                        location.hash = "#new-post" ;

                });	        
        });  
 });
