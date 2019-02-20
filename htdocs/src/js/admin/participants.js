 $.fn.extend({
	
    // Delete participant
    delete_participant:function(participant_id) {
     
            $.ajax({
                    type: 'POST',
                    url: '/api/participant_api/delete_participant/',
                    data: {user_id: participant_id},
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
                            window.location.reload();
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
    
    
	// Edit modal
	 edit_participant_modal:function(participant_id, participant_name) {
		var dH = $(window).height()-150, $modal;
	
		if($('#participant_modal').length!=0)  $('#participant_modal').remove();

		$modal = $('<div id="participant_modal" class="modal modal-wide fade"><div class="modal-dialog" style="width:90%"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Participant #'+participant_id+' | ' + participant_name +'</h4></div><iframe id="edit_participant_iframe" frameborder="0"  class="ui-dialog-content ui-widget-content" style="display:block; height:'+dH+'px !important;width:100% !important" src="/admin/edit_participant?user_id='+participant_id+'"></iframe> <div class="modal-footer" style="margin-top:0px"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button> <button type="button" class="btn btn-primary" id="save_participant_modal">Save</button> </div></div></div></div>');
		
		$modal.appendTo($('body'));
		
		$modal.on("show.bs.modal", function() {
			  $(this).find(".modal-body").css({"max-height": $(window).height() - 150});
		});
		
		$modal.modal('show');
        
 		
		$('#save_participant_modal').click(function(e) {
            $.ajax({
                    type: 'POST',
                    url: '/api/participant_api/edit_participant/',
                    data: $("#edit_participant_iframe").contents().find('form').serialize(),
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
                            bootbox.alert("Participant Updated", function() {
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
        // Edit participant
		$('button[data-action=edit_participant]').click(function(e) {
			$(this).edit_participant_modal($(this).attr('data-participant_id'), $(this).attr('data-participant_name'));
			e.stopPropagation(e);
		});
        
        // Delete participant
		$('button[data-action=delete_participant]').click(function(e) {
            var $t = $(this), id = $t.attr('data-participant_id'), name = $t.attr('data-participant_name');
            bootbox.prompt('<strong>Everything related to ' + name + ' (participant #'+id+')<br/>(talks, posters, details, etc.) will be erased from the database.</strong><br/><br/>Are you sure you want to delete it ?<span>Type "<strong>YES</strong>" below to confirm</span>', function(result) {                
              if (result === 'YES') {    
                $(this).delete_participant(id);
                    e.stopPropagation(e);                                         
              }
            });
		});
});