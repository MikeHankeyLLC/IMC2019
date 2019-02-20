 $.fn.extend({
	
    // Delete poster
    delete_poster:function(poster_id) {
            
            $.ajax({
                    type: 'POST',
                    url: '/api/poster_api/delete_poster/',
                    data: {poster_id: poster_id},
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
                            $('#poster_' + poster_id).fadeOut(150,function() {$(this).remove(); });
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
    edit_poster_modal: function(poster_id, poster_title) {
        var dH = $(window).height()-150, $modal;
	
		if($('#poster_modal').length!=0)  $('#poster_modal').remove();

		$modal = $('<div id="poster_modal" class="modal fade"><div class="modal-dialog  modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Poster #'+poster_id+' | ' + poster_title +'</h4></div><iframe id="edit_poster_iframe" frameborder="0"  class="ui-dialog-content ui-widget-content" style="display:block; height:'+dH+'px !important;width:100% !important" src="/admin/edit_poster?poster_id='+poster_id+'"></iframe> <div class="modal-footer" style="margin-top:0px"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button> <button type="button" class="btn btn-primary" id="save_poster_modal">Save</button> </div></div></div></div>');
		
		$modal.appendTo($('body'));
		
		$modal.on("show.bs.modal", function() {
			  $(this).find(".modal-body").css({"max-height": $(window).height() - 150});
		});
		
		$modal.modal('show');
  		
		$('#save_poster_modal').click(function(e) {
            $.ajax({
                    type: 'POST',
                    url: '/api/poster_api/edit_poster/',
                    data: $("#edit_poster_iframe").contents().find('form').serialize(),
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
                           bootbox.alert("Poster updated", function() {  
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
		
        // Delete poster
        $('button[data-action=delete_poster]').click(function(e) {
                var id = $(this).attr('data-poster_id');
                bootbox.prompt('<strong>This poster will be erased from the database</strong><br/>Are you sure you want to delete it ?<span><strong>Title: </strong><em>'+ $(this).attr('data-poster_title')+'</em><br/>Type "<strong>YES</strong>" below to confirm</span>', function(result) {                
                  if (result === 'YES') {    
                    $(this).delete_poster(id);
                        e.stopPropagation(e);                                         
                  }
                });
		});
        
        // Edit poster
        $('button[data-action=edit_poster]').click(function(e) {
	        $(this).edit_poster_modal($(this).attr('data-poster_id'), $(this).attr('data-poster_title'));
			e.stopPropagation(e);
		});       
 });