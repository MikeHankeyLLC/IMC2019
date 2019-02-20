 $.fn.extend({
	
    // Delete talk
    delete_talk:function(talk_id) {
            
            $.ajax({
                    type: 'POST',
                    url: '/api/talk_api/delete_talk/',
                    data: {talk_id: talk_id},
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
                            $('#talk_' + talk_id).fadeOut(150,function() {$(this).remove(); });
                        } else {
                            error= '';
                            $.each(j.errors,function(i) {
                                error+=j.errors[i] + '\n';
                            });
                            alert('ERROR\n' + error);
                        }
                    }
            });
    },
    
    // Edit Talk
    edit_talk_modal: function(talk_id, talk_title) {
        var dH = $(window).height()-150, $modal;
	
		if($('#talk_modal').length!=0)  $('#talk_modal').remove();

		$modal = $('<div id="talk_modal" class="modal fade"><div class="modal-dialog  modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Talk #'+talk_id+' | ' + talk_title +'</h4></div><iframe id="edit_talk_iframe" frameborder="0"  class="ui-dialog-content ui-widget-content" style="display:block; height:'+dH+'px !important;width:100% !important" src="/admin/edit_talk?talk_id='+talk_id+'"></iframe> <div class="modal-footer" style="margin-top:0px"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button> <button type="button" class="btn btn-primary" id="save_talk_modal">Save</button> </div></div></div></div>');
		
		$modal.appendTo($('body'));
		
		$modal.on("show.bs.modal", function() {
			  $(this).find(".modal-body").css({"max-height": $(window).height() - 150});
		});
		
		$modal.modal('show');
  		
		$('#save_talk_modal').click(function(e) {
            $.ajax({
                    type: 'POST',
                    url: '/api/talk_api/edit_talk/',
                    data: $("#edit_talk_iframe").contents().find('form').serialize(),
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
                           bootbox.alert("Talk updated", function() {  
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
    }
    
 });


$(function() {
		
        // Delete talk
        $('button[data-action=delete_talk]').click(function(e) {
                var id = $(this).attr('data-talk_id');
                bootbox.prompt('<strong>This talk will be erased from the database</strong><br/>Are you sure you want to delete it ?<span><strong>Title: </strong><em>'+ $(this).attr('data-talk_title')+'</em><br/>Type "<strong>YES</strong>" below to confirm</span>', function(result) {                
                  if (result === 'YES') {    
                    $(this).delete_talk(id);
                        e.stopPropagation(e);                                         
                  }
                });
		});
        
        // Edit talk
        $('button[data-action=edit_talk]').click(function(e) {
	        $(this).edit_talk_modal($(this).attr('data-talk_id'), $(this).attr('data-talk_title'));
			e.stopPropagation(e);
		});       
 });