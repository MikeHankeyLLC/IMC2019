// Date pickers
function init_datepickers() {
   $('input[name=dob]').datepicker({
      format: "yyyy-mm-dd",
      defaultViewDate: { year: 1980, month: 08, day: 10 },
   }).on('changeDate', function() { 
      showHideUnderage();
   });
}

// Reset Form
function reset_submission_form() {
    $(':input','#reg_form')
     .not(':button, :submit, :reset, :hidden, :input[type=radio]')
     .val(''); 
     $('#no_tshirt').click(); 
     $('.contrib').remove(); 
     $('#no_prog').click();
     $('input[name=talks_num]').val(0); 
     $('input[name=posters_num]').val(0);
}


// Adjust left menu width
function adjustWidth() {
   $("#affix-lm").width($(".col-menu").width());
 }

$(document).ready(function(){
      

    var waypoint ;
    
   // Error Handler
   if(typeof error_field != 'undefined') {
        $.each(error_field,function(i,v){$('#'+v).parents('.form-group').addClass('has-error');});
   }

    // Menu
    $('.toggle-menu').jPushMenu({closeOnClickLink: false});
    $('.dropdown-toggle').dropdown();
    
    if($('#top-bar').length>0 && $('#wrap').length>0 ) { 
         waypoint = new Waypoint({
            element: $('#wrap'),
            handler: function(direction) {
                $('#top-bar').toggleClass('z');
            },
            offset: 60 
        })
    }

    // Scrollfollow left menu
    if($('#affix-lm').length>0 && $('#wrap').length>0) {
         adjustWidth();
         waypoint_menu = new Waypoint({
         element: $('#wrap'),
         handler: function(direction) {
             $('#affix-lm').toggleClass('affix-f');
         },
         offset: 20 
        })

        $(window).resize(
         function() {
           adjustWidth();
        })
    }
    
    // Datepickers
    init_datepickers();
    
    // Contributions
    init_contributions();
    
    // T-shirts
    init_tshirts();
    
    // Roomates
    init_roomates();
    
    // Food
    init_food_requirement();
    
    
});
   