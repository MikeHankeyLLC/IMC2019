 // T-Shirt
function init_tshirts() {
     // Init
     if($('#yes_tshirt').is(":checked")) {
        $('#tshirt-holder').removeClass('hidden');  
     } else {
        $('#tshirt-holder').addClass('hidden');
         $('#tshirt').val('');  
     }
  
     // Click
     $('input[name=tshirt_q]').click(function() {
          if($(this).val()=='no') {
           $('#tshirt-holder').addClass('hidden');  
            $('#tshirt').val('');
          } else {
           $('#tshirt-holder').removeClass('hidden');  
          }
     });
}   
 