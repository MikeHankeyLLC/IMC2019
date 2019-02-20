$(function() {
    
    $('input[name=pay_date]').datepicker({
        format: "yyyy-mm-dd"
   });
 
     // Table sorter
    $(".tablesorter").tablesorter(); 
    
    // Amount confirmation email
    $('#amount_conf_email').keyup(function(event){

       if(event.which != 8 && isNaN(String.fromCharCode(event.which)) && String.fromCharCode(event.which)!='.') {
           event.preventDefault(); //stop character from entering input
       } 
       $("#amount_conf_message_ifr").contents().find("#updated_amount").html($('#amount_conf_email').val()); 
       $("#amount_conf_message_ifr").contents().find("#updated_amount_total").html(parseFloat($('input[name=total_amount_confirmed]').val())+parseFloat($('#amount_conf_email').val()));
   
   }); 
   
   // Show text of confirmation email if "Yes" is selected
   $('input[name=conf_email]').change(function() {
        if($(this).attr('id')=='yes_conf_email') {
            $('#conf_email_text').removeClass("hidden");   
        } else {
            $('#conf_email_text').addClass("hidden");   
        }
       
   });
 
});