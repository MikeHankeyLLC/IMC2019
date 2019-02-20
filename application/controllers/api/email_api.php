<?php

class email_api_controller extends Template_Controller {

    function __construct($cont, $func) {
        parent::__construct($cont, $func);
    }


    /*
    * Send Official Confirmation Email 
    */
    public function send_official_conf_email() {
         
       if(!empty($this->input['user_id']) &&  !empty($this->input['message']) && !empty($this->input['email'])) {
           
           Registrations_model::payment_confirmation_email_sent($this->input);
                  
           if(empty($errors)):       
                    
               // Send email 
               $message  =  "<html><body>";
               $message .=  $this->input['message'];
               $message .=  "</body></html>";
                                
               $from     =  MAIN_CONTACT;
               $subject  =  'IMC ' . CONF_YEAR;
                
               Mail::send(
                    array(  'from'      => MAIN_CONTACT,
                            'from_name' => 'IMC ' . CONF_YEAR . ' Org. Committee',
                            'to'        => $this->input['email'],
                            'to_name'   => $this->input['firstname'] . ' ' . $this->input['lastname'],
                            'subject'   => $subject,
                            'message'   => $message,
                            'cc_name'   => CONF_YEAR . ' Financial Committee',
                            'cc_email'  => FINANCE_ADMIN,
                     )
               );
 
            
               // Add to DB (not the content - just the # of emails sent)
               Emails_model::add_email($this->input);
           
           endif;
            
       } else {
            $errors['user_id'] = "Missing info - impossible to send an email to this participant.";   
       }
       
       $json = new JSON_Response(); 
       $json->input      = $this->input;
       $json->errors     = !empty($errors)?$errors:'';
       $json->print_response();
    }
        
        
        
    
   


    /*
    * Send Email
    */
    public function send_email() {
        
       if(!empty($this->input['user_id']) && !empty($this->input['object'])  && !empty($this->input['message']) && !empty($this->input['email'])) {
           
           // Get CC if any
           $cc = $this->input['cc'];      
           if(!empty($cc)): 
                // Explode
                $ccs = explode(',',$cc);
                
                // Validate each email 
                foreach($ccs as $email_to_cc):
                    if(!filter_var($email_to_cc, FILTER_VALIDATE_EMAIL)):
                        $errors[] = $email_to_cc . ' is not a valid email address. Please, use a comma separated list of valid emails.';
                    endif;
                endforeach;
           endif;
          
           if(empty($errors)):       
                    
               // Send email 
               $message  =  "<html><body>";
               $message .=  $this->input['message'];
               $message .=  "</body></html>";
                                
               $from     =  MAIN_CONTACT;
               $subject  =  'IMC ' . CONF_YEAR;
                
               Mail::send(
                    array(  'from'      => MAIN_CONTACT,
                            'from_name' => 'IMC ' . CONF_YEAR . ' Org. Committee',
                            'to'        => $this->input['email'],
                            'to_name'   => $this->input['firstname'] . ' ' . $this->input['lastname'],
                            'subject'   => $subject,
                            'message'   => $message,
                            'cc_name'   => CONF_YEAR . ' Financial Committee',
                            'cc_email'  => FINANCE_ADMIN,
                            'ccs'       => $cc
                     )
               );
             
                           
               
            
               // Add to DB (not the content - just the # of emails sent)
               Emails_model::add_email($this->input);
           
           endif;
            
       } else {
            $errors['user_id'] = "Missing info - impossible to send an email to this participant.";   
       }
       
       $json = new JSON_Response(); 
       $json->input      = $this->input;
       $json->errors     = !empty($errors)?$errors:'';
       $json->print_response();
    }   
    
    
    
    /*
     Send a payment confirmation email (the JS takes care of the validation for amount & email)
     */
    public function confirm_payment() {
        
          if(   !empty($this->input['user_id'])  && 
                !empty($this->input['amount'])  && 
                !empty($this->input['email']) && 
                !empty($this->input['message'])) {
                
                $subject  = "IMC ".CONF_YEAR." - Receipt -  Participant #" . $this->input['user_id'] . "  " . $this->input['firstname'] . "  " . $this->input['lastname'];
              
                 
                Mail::send(
                    array(  'from'      => FINANCE_ADMIN,
                            'from_name' => 'IMC ' . CONF_YEAR . ' Financial Committee',
                            'to'        => $this->input['email'],
                            'to_name'   => $this->input['firstname'] . ' ' . $this->input['lastname'],
                            'subject'   => $subject,
                            'message'   => $this->input['message'],
                            'cc_name'   => CONF_YEAR . ' Financial Committee',
                            'cc_email'  => FINANCE_ADMIN
                     )
                );
              
                // Update `imc_payment_confirm`  table
                $this->input['res_db'] = Payment_confirm_model::add_paymentEmail($this->input);
                
          } else {
                $errors['404'] =  'Impossible to send the email - an required information is missing.';   
              
          }
          
           $json = new JSON_Response(); 
           $json->input      = $this->input;
           $json->errors     = !empty($errors)?$errors:'';
           $json->print_response();
    }



}


