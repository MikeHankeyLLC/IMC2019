 $.fn.extend({
     
      
    // Send email from modal
    email_participant_modal: function($button) {
        var dH = $(window).height()-250, $modal;
	
		if($('#send_email').length!=0)  $('#send_email').remove();

		$modal = $('<div id="send_email" class="modal fade"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Email: '+$button.attr('data-participant_name')+'</h4></div><iframe id="send_email_iframe" frameborder="0"  class="ui-dialog-content ui-widget-content" style="display:block; height:'+dH+'px !important;width:100% !important" src="/admin/send_email?user_id='+$button.attr('data-participant_id')+'"></iframe> <div class="modal-footer" style="margin-top:0px"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>');
		
		$modal.appendTo($('body'));
		
		$modal.on("show.bs.modal", function() {
			  $(this).find(".modal-body").css({"max-height": $(window).height() - 150});
		});
		
		$modal.modal('show');
    		
     }
    
 });


$(function() {
        
        // Send email
        $('button[data-action=email_participant]').click(function(e) {
	        $(this).email_participant_modal($(this));
			e.stopPropagation(e);
		});       
        
        // Send the actual Email
        $('#send_email_modal').click(function(e) {
            var  message = tinyMCE.get('message').getContent();
             
            $.ajax({
                    type: 'POST',
                    url: '/api/email_api/send_email/',
                    data: {
                        submit: 1, 
                        user_id: $('input[name=user_id]').val(),
                        email: $('input[name=email]').val(),
                        cc: $('input[name=cc]').val(),
                        object: $('#object').val(),
                        message:message
                    },
        		    statusCode: {
                        404: function() { 
                            alert('404 Error - Please, check your internet connection and try again later. '); 
                        },
                        500: function() { 
                            alert('500 Error - We are currently updating our website. Please, try again later. ');
                        }
                    },	
                    success: function(j) {
                        if(j.errors.length==0) {
                           bootbox.alert("Email sent", function() {  
                            $modal.modal('hide');
                            window.location.reload();
                           });
                        } else {
                            error= '';
                            $.each(j.errors,function(i) {
                                error+=j.errors[i] + '\n';
                            });
                            alert('ERROR\n' + error);
                        }
                    }
            });
 		});
 });