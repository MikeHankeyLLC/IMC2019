// Add a talk 
function add_talk(data) {
     var talk_template = $('#talk_template').html();
     $(Mustache.render(talk_template,data)).appendTo($('#contrib_holder')); 
     $('#talks_num').val(parseInt($('#talks_num').val())+1);
       
     // Select due date 
     if(typeof data.delivery_date != undefined) {
         $('#talk'+data.id+'_content').find('select.talk_delivery_date').val(data.delivery_date);
     }  
     
     // Select duration
     if(typeof data.duration != undefined) {
         $('#talk'+data.id+'_content').find('select.talk_dur').val(data.duration);
     }  
     
     // Select session
     if(typeof data.session != undefined) {
         $('#talk'+data.id+'_content').find('select.talk_session').val(data.session);
     } 
     
      // Close
      $('#talk'+data.id+'_content').on('closed.bs.alert', function () {
          $('#talks_num').val(parseInt($('#talks_num').val())-1);
      })
}   

// Add a poster
function add_poster(data) {
      var poster_template = $('#poster_template').html();
      $(Mustache.render(poster_template,data)).appendTo($('#contrib_holder')); 
      $('#posters_num').val(parseInt($('#posters_num').val())+1);
      
      // Select due date 
     if(typeof data.delivery_date != undefined) {
         $('#poster'+data.id+'_content').find('select.poster_delivery_date').val(data.delivery_date);
     }  
     
     // Select session
     if(typeof data.session != undefined) {
         $('#poster'+data.id+'_content').find('select.poster_session').val(data.session);
     } 
      
      // Close
      $('#poster'+data.id+'_content').on('closed.bs.alert', function () {
          $('#posters_num').val(parseInt($('#posters_num').val())-1);
      })
    
}

// Contributions 
function init_contributions() {
  
   $('input[name=prog]').click(function() {
      if($(this).val()=='no') {
           // Remove all contributions (posters & talks)
           if($('.contrib').length>0) {
               if(confirm("All your papers and talks will be removed. Do you want to continue?")) {
                    $('.contrib').remove();
               } else {
                    $('#yes_prog').click();    
                    return false;
               }
           }  
           $('#lectures_and_posters').addClass("hidden"); 
          } else {
           $('#lectures_and_posters').removeClass("hidden"); 
          }
   });
   
   // Init  Contribution
   if($('#yes_prog').is(":checked")) {
        $('#lectures_and_posters').removeClass("hidden");  
        
        // Add Previous Talks
        if(typeof talks_titles != 'undefined') {
            for(i=0;i<talks_titles.length;i++) {
                
                 var del_date = (talks_delivery_date !== null)?talks_delivery_date[i]:'UNKNWON';
                
                  add_talk({
                      id:i,
                      title: talks_titles[i],
                      authors: talks_authors[i],
                      abstract: talks_abstracts[i],
                      duration: (typeof talks_durations[i] !== null)?talks_durations[i]:0,
                      session: (typeof talks_sessions[i] !== null)?talks_sessions[i]:0,
                      delivery_date: del_date
                  });  
                  
                  
            }
        }
        
        // Add Previous Posters
        if(typeof posters_titles != 'undefined') {
            for(i=0;i<posters_titles.length;i++) {
                  var del_date = (posters_delivery_date !== null)?posters_delivery_date[i]:'UNKNWON';
                
                  add_poster({
                      id:i,
                      title: posters_titles[i],
                      authors: posters_authors[i],
                      abstract: posters_abstracts[i],
                      session: (typeof posters_sessions[i] !== null)?posters_sessions[i]:0,
                      delivery_date: del_date
                  });  
                  
                
            }
        }
   }
   
   
   // Add a talk 
   $('#add_talk').click(function(e) {
      var talk_template = $('#talk_template').html();
      e.stopImmediatePropagation();
      e.stopPropagation(); 
      add_talk({id: $('#talks_num').val()});
      return false; 
   });


   // Add a poster 
   $('#add_poster').click(function(e) {
      var poster_template = $('#poster_template').html();
      e.stopImmediatePropagation();
      e.stopPropagation(); 
      add_poster({id: $('#posters_num').val()});
      return false; 
   });           
}
