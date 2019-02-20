function addScreenBlocker() {
   $('<div id="screenblocker"/>').appendTo(parent.$('body'));  
}

function removeScreenBlocker() {
   parent.$('#screenblocker').remove();
}

 
$(function() { 
 
   $('#show_official_email').click(function(e) {
       e.stopPropagation();
       if($('#official_email').hasClass('hidden')) {
           $('#official_email').removeClass('hidden');
           $('#show_official_email_cont').addClass('hidden');
       } else {
           $('#official_email').addClass('hidden');
           $('#show_official_email_cont').removeClass('hidden');
       }
       return false;
   });
   
   
   $('#cancel_official_email').click(function(e) {
       e.stopPropagation(); 
       $('#official_email').addClass('hidden');
       $('#show_official_email_cont').removeClass('hidden');
       return false;
   });

   
   $('#show_pay_email').click(function(e) {
       e.stopPropagation();
       if($('#pay_email').hasClass('hidden')) {
           $('#pay_email').removeClass('hidden');
           $('#show_pay_email_cont').addClass('hidden');
       } else {
           $('#pay_email').addClass('hidden');
           $('#show_pay_email_cont').removeClass('hidden');
       }
       return false;
   });
   
   $('#cancel_pay_email').click(function(e) {
       e.stopPropagation(); 
       $('#pay_email').addClass('hidden');
       $('#show_pay_email_cont').removeClass('hidden');
       return false;
   });
   
   // Send official email
   $('#send_official_email').click(function(e) {
       e.stopPropagation(); 
       
       
       var email = $('#email').val(), message = tinyMCE.get('official_email_message').getContent();
       var email_valid = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
       var error = false;
       
       
       // Validate Email
       if(!email_valid.test(email)) {
           error=true;
           
           bootbox.alert("<strong class='text-danger'>Payment Confirmation Email: email address missing or invalid.</strong>", function() {
            $('#info-tab').click();
            $('#email').focus();
          
           });
       } 
       
       if(!error) {
            addScreenBlocker();
            $('#send_official_email').find('.fa').removeClass('fa-send').addClass('fa-spinner fa-spin');
           
            // Send the email 
             $.ajax({
                    type: 'POST',
                    url: '/api/email_api/send_official_conf_email/',
                    data: {
                        email: email, 
                        message: message,
                        user_id: $('input[name=user_id]').val(),
                        title: $('#title').val(),
                        firstname: $('#firstname').val(),
                        lastname: $('#lastname').val()
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
                        removeScreenBlocker();
                         $('#send_official_email').find('.fa-spinner').removeClass('fa-spinner').removeClass('fa-spin').addClass('fa-send');
                        
                        if(j.errors.length==0) {
                            
                            bootbox.alert("Official confirmation Email sent.", function() {
                                 $('#official_email').addClass('hidden');
                                 $('#show_official_email_cont').removeClass('hidden');
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
       }
       
       return false;
    });
    
   
   
   // Send email
   $('#send_pay_email').click(function(e) {
       e.stopPropagation(); 
       
       // Validate Amount
       var amount = $('#amount_conf_email').val(), email = $('#email').val(), message = tinyMCE.get('amount_conf_message').getContent() ;
       var email_valid = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
       var error = false;
       
      
       
        
       // Validate Amount
       if(!$.isNumeric(amount)) {
            error=true;
           
           bootbox.alert("<strong class='text-danger'>Payment Confirmation Email: please enter a valid amount.</strong>", function() {
            $('#amount_conf_email').focus();
           
           });
       }  
       
       // Validate Email
       if(!email_valid.test(email)) {
            error=true;
           
           bootbox.alert("<strong class='text-danger'>Payment Confirmation Email: email address missing or invalid.</strong>", function() {
            $('#info-tab').click();
            $('#email').focus();
           
           });
       }  
       
       if(!error) {
           
            addScreenBlocker();
            $('#send_pay_email').find('.fa').removeClass('fa-send').addClass('fa-spinner fa-spin');
           
            // Send the email 
             $.ajax({
                    type: 'POST',
                    url: '/api/email_api/confirm_payment/',
                    data: {
                        email: email, 
                        amount:   amount,
                        message: message,
                        total_amount_confirmed: parseFloat($('#total_amount_confirmed').html())+parseFloat(amount),
                        user_id: $('input[name=user_id]').val(),
                        title: $('#title').val(),
                        firstname: $('#firstname').val(),
                        lastname: $('#lastname').val()
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
                        
                        
                        removeScreenBlocker();
                        $('#send_pay_email').find('.fa-spinner').removeClass('fa-spinner').removeClass('fa-spin').addClass('fa-send');
                        
                        if(j.errors.length==0) {
                            bootbox.alert("Confirmation Email sent.", function() {
                                $('#pay_email').addClass('hidden');
                                $('#show_pay_email_cont').removeClass('hidden');
                                
                                date = j.input['res_db'].email_date.split(' ');
                                amount = parseFloat(j.input['res_db'].amount);
                                
                                // Add row inside the edit participant form 
                                $('<tr><td>'+ date[0]+'</td><td>'+ amount.toFixed(2)+'&euro;</td></tr>').appendTo( $('#payment_sent_table'));
                                
                                // Update total
                                total_am = parseFloat($('#total_amount_confirmed').html())+amount;
                                $('#total_amount_confirmed').html(total_am.toFixed(2)+'&euro;');
                                $('input[name=total_amount_confirmed]').val(total_am.toFixed(2));
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
       }
        
       
       return false;
   });
    
});