<?php

class participant_api_controller extends Template_Controller {

    function __construct($cont, $func) {
        parent::__construct($cont, $func);
    }


        
    public function get_paypal_price($price) {
        return round($price+(0.034*$price +0.35)/0.966,2,PHP_ROUND_HALF_UP);
    }
    
    /*
    * Delete Participant
    */
    public function delete_participant() {
       if(!empty($this->input['user_id'])) {
            Participants_model::delete_participants(array('user_id'=>$this->input['user_id']));
            
            $file = LOG_DIR.'/participants.txt';
            $text = date("Y-m-d H:i:s") . " >> PARTICIPANT  (ID: ". $this->input['user_id'] .") DELETED\n";
            file_put_contents($file, $text, FILE_APPEND | LOCK_EX);
       } else {
            $errors['user_id'] = "Invalid User Id ";   
       }
       
       	$json = new JSON_Response(); 
        $json->input      = $this->input;
        $json->errors     = !empty($errors)?$errors:'';
	    $json->print_response();
    }   
    

    /*
     * Edit Participant 
     */
    public function edit_participant() {
        
        
        if(!empty($this->input['user_id'])) {
        
            // Strip tags
            foreach($this->input as $k=>$v) :
               if(is_string($v)): $this->input[$k] = strip_tags($v); endif;
            endforeach;
        
            // Validation 
                // Firstname
                if(empty($this->input['firstname'])) { $errors['firstname'] = 'Please, enter your first name'; }
                
                // Lastname
                if(empty($this->input['lastname'])) { $errors['lastname'] = 'Please, enter your last name';  }
                
                // Address
                if(empty($this->input['address'])) { $errors['address'] = 'Please, enter an address';
                }
                
                // Email
                if(empty($this->input['email']) || !filter_var($this->input['email'], FILTER_VALIDATE_EMAIL)) {
                       $errors['email'] = 'Please, enter a valid email';
                }
                
                // Country
                if(empty($this->input['country'])) {
                       $errors['country'] = 'Please, select a country';
                }
                
                // Phone
                if(empty($this->input['phone'])) {
                       $errors['phone'] = 'Please, enter a phone number';
                }
                
                // Dob
                if(empty($this->input['dob'])) {
                       $errors['dob'] = 'Please, enter your date of birth';
                } else {
                    $d = DateTime::createFromFormat('Y-m-d', $this->input['dob']);
                    if(!($d && $d->format('Y-m-d') == $this->input['dob'])) {     
                       $errors['dob'] = 'Please, enter a valid date of birth (Y-m-d)';
                    }
                }
                
                // Tshirt 
                if(!empty($this->input['tshirt-q']) && empty($this->input['tshirt'])) {
                       $errors['tshirt'] = 'Please, select a t-shirt size or answer "No" to the question about the IMC' . CONF_YEAR. ' official t-shirt.';
                }
                
                // reg_type
                if(empty($this->input['reg_type'])) {
                       $errors['reg_type'] = 'Please, choose a registration type';
                }
                
                 // Arrival Date
                if(!empty($this->input['arrival_date'])) {
                    $d = DateTime::createFromFormat('Y-m-d', $this->input['arrival_date']);
                    if(!($d && $d->format('Y-m-d') == $this->input['arrival_date'])) { 
                        $errors['arrival_date'] = 'Please, enter a valid date of arrival';
                    }
                }
                
                // Departure Date
                if(!empty($this->input['departure_date'])) {
                    $d = DateTime::createFromFormat('Y-m-d', $this->input['departure_date']);
                    if(!($d && $d->format('Y-m-d') == $this->input['departure_date'])) { 
                        $errors['arrival_date'] = 'Please, enter a valid date of arrival';
                    }
                }
                
                // Payment method 
                if(empty($this->input['payment_meth'])) {
                       $errors['payment_meth'] = 'Please,  select your payment method';
                }
                
                // Meteoroids shuttle
                /*
                if(empty($this->input['meteoroid_shuttle'])) {
                       $errors['meteoroid_shuttle'] = 'Please, answer the question about the Meteoroids shuttle.';
                }
                */
                
                   
                if(!empty($this->input['amount']) && empty($this->input['pay_date'])) {
                         $errors['pay_date'] = 'Please, enter a payment date.';
                }
                
                if(!empty($this->input['pay_date']) && empty($this->input['amount'])) {
                         $errors['amount'] = 'Please, enter an amount.';
                }

                if(!empty($this->input['conf_email']) && $this->input['conf_email']=='yes' && !empty($errors)) {
                        $errors['conf_email'] = 'Correct all the errors first - we cannot send the confirmation email.';
                }
            
                 
                // Talks (all talks need title & abstract
                if(!empty($this->input['talk'])): 
                     foreach($this->input['talk'] as $key=>$val):
                        if(empty($val)):
                               $errors['talk'.$key] = "Talks require a title.";
                        endif;
                        if(empty($this->input['talk_abstract'][$key])):
                               $errors['talk_abstract'.$key] = "Talks require an abstract.";
                        endif;
                        if(empty($this->input['talk_authors'][$key])):
                               $errors['talk_authors'.$key] = "Talks require at least one author.";
                        endif;
                        if(empty($this->input['talk_session'][$key])):
                               $errors['talk_session'.$key] = "Talks require a session";
                        endif;
                        if(empty($this->input['talk_delivery_date'][$key])): 
                             $errors['talk_delivery_date'.$key] = "Talks require a paper delivery date";
                        endif; 
                     endforeach;
                endif; 
                
                                 
                // Posters (all talks need title & abstract
                if(!empty($this->input['poster'])): 
                     foreach($this->input['poster'] as $key=>$val):
                       if(empty($val)):
                               $errors['poster'.$key] = "Posters require a title.";
                        endif;
                        if(empty($this->input['poster_abstract'][$key])):
                               $errors['poster_abstract'.$key] = "Posters require an abstract.";
                        endif;
                         if(empty($this->input['poster_authors'][$key])):
                               $errors['poster_authors'.$key] = "Posters require at least one author.";
                        endif;
                        if(empty($this->input['poster_session'][$key])):
                               $errors['poster_session'.$key] = "Posters require a session";
                        endif;
                        if(empty($this->input['poster_delivery_date'][$key])): 
                                $errors['poster_delivery_date'.$key] = "Posters require a paper delivery date";
                        endif; 
                     endforeach;
                endif; 
                  
                // COMPUTE NEW AMOUNT BASED ON SELECTED OPTIONS 
                $this->input['amount_due'] = $this->input[$this->input['reg_type'].'_price'];
                
                 
                if(!empty($this->input['tshirt_q']) && $this->input['tshirt_q']=='yes'): 
                    $this->input['amount_due']+= TSHIRT_PRICE;
                 endif;
                
                if($this->input['proceedings']=='print'):
                    $this->input['amount_due']+= PROCEEDINGS_PRICE;
                    $this->input['print']     += PROCEEDINGS_PRICE; // PROCEEDINGS PRINT PRICE
                endif;

                $this->input['amount_due_paypal'] = $this::get_paypal_price($this->input['amount_due']);     
                
                /*
                // MARC wants to add any amounts
                if(!empty($this->input['amount'])): 
                    if($this->input['payment_meth']=='1'):       
                        if($this->input['amount']!=$this->input['amount_due_paypal']): 
                            $errors['amount'] =  'The amount you entered doesn`t correspond to the options selected. Please, save your modifications first and then re-open the participant to confirm it. Note that you selected Paypal payment and Paypal fees must be included.';   
                        endif;
                    else: 
                        if($this->input['amount']!=$this->input['amount_due']): 
                            $errors['amount'] =  'The amount you entered doesn`t correspond to the options selected. Please, save your modifications first and then re-open the participant to confirm it.';   
                        endif;                          
                    endif;
                endif;
                */
                
                $amount_ok = !empty($this->input['amount'])?true:false;
                if($amount_ok && !filter_var($this->input['amount'], FILTER_VALIDATE_FLOAT)): 
                    $errors['amount'] =  'Please enter a correct amount.';    
                    $amount_ok = false;
                endif;
                
                // Test errors
                if(empty($errors)) :
            
                    if($amount_ok && (!empty($this->input['pay_date']) || $this->input['pay_date']!='0000-00-00')  
                     ) {
                           $this->input['paid'] =  1;
                     } else {
                           $this->input['paid'] = -1;
                     }
            
                    // First remove all talks and posters from the user 
                    Talks_model::remove_talks_from_user(array('user_id'=>$this->input['user_id']));
                    Posters_model::remove_posters_from_user(array('user_id'=>$this->input['user_id']));
                      
                    // Arrival Time
                    $this->input['arrival_time'] = $this->input['arrival_time_h'].':'.$this->input['arrival_time_m'].':00';
    
                    // Departure Time
                    $this->input['departure_time'] = $this->input['departure_time_h'].':'.$this->input['departure_time_m'].':00';
                    
 					
                    // Update Participant
                    Participants_model::update_participants($this->input);
                    
                    // Update participants Travel info
                    Travels_model::update_travelInfo($this->input);
                    
                    // Update participants Details
                    Details_model::update_details($this->input);
                      
                    // Update registration
                    Registrations_model::update_registration($this->input);
                     
                    // Add papers if any 
                    if(!empty($this->input['talk'])): 
                         foreach($this->input['talk'] as $key=>$val):
                            Talks_model::add_talk(array(
                                'year'      => $this->input['year'],
                                'title'     => $val,
                                'authors'  => $this->input['talk_authors'][$key],
                                'abstract'  => $this->input['talk_abstract'][$key],
                                'session'   => $this->input['talk_session'][$key],
                                'duration'  => $this->input['talk_duration'][$key],
                                'delivery_date' => $this->input['talk_delivery_date'][$key],
                                'user_id'   => $this->input['user_id']
                             ));
                        endforeach;
                    endif;
                    
                    // Add poster if any
                    if(!empty($this->input['poster'])): 
                        foreach($this->input['poster'] as $key=>$val):
                            Posters_model::add_poster(array(
                                'year'      => $this->input['year'],
                                'title'     => $val,
                                'authors'   => $this->input['poster_authors'][$key],
                                'abstract'  => $this->input['poster_abstract'][$key],
                                'session'   => $this->input['poster_session'][$key],
                                'delivery_date' => $this->input['poster_delivery_date'][$key],
                                'user_id'   => $this->input['user_id']
                              ));
                        endforeach;
                    endif;
             
                    
                    if(empty($errors) && !empty($this->input['conf_email']) && $this->input['conf_email']=='yes') : 
                    
                        /*
                        Marc wants to be able to send confirmation emails to people who didn't pay yet
                        if($this->input['paid'] != 1):
                            $errors['amount'] = 'You need to enter a valid amount and a valid payment date to be able to send a confirmation email.';
                        endif;  
                        */ 
                        if(empty($this->input['confirmed']) || $this->input['confirmed']==-1):
                            $errors['yes_confirmed'] = 'You can send confirmation email only to confirmed participants. Please, check "Yes" for confirmed.';
                        endif;
                        
                        if(empty($errors)): 
                        
                            // Update DB
                            Registrations_model::payment_confirmation_email_sent($this->input);
                            
                           
                            /*
                            // Send payment confirmation email 
                             $message  =  "<p><strong>Dear " . $this->input['title'] . " " . $this->input['firstname']. " " . $this->input['lastname'] . ",</strong></p><p>Your participation to the <a href='".BASE_URL."'>IMC ". CONF_YEAR ."</a> has now been confirmed.<br/>Should your plans change, please notify <a href='mailto:".IMC_EMAIL."'>". IMC_EMAIL ."</a> immediately. Notice that in such case the cancellation policy of the <a href='".BASE_URL."/disclaimer'>Disclaimer and service agreement</a> applies.</p>";
                            $message .=  "<p>Thank you,<br/>We look forward to meeting you at the conference!</p>";
                            $message .=  "<p><strong>The IMC".CONF_YEAR." Team.</strong></p>";
                         */
                             $subject  = "IMC ".CONF_YEAR." - Confirmation Participant #" . $this->input['user_id'] . "  " . $this->input['firstname'] . "  " . $this->input['lastname'];
                             
                             
                             $message = $this->input['confirm_payemnt_email'];
                            
                            Mail::send(
                                array('from'        => IMC_EMAIL,
                                      'from_name'   =>'IMC ' . CONF_YEAR . ' Org. Committee',
                                      'to'          => $this->input['email'],
                                      'to_name'     => $this->input['firstname'] . ' ' . $this->input['lastname'],
                                      'subject'     => $subject,
                                      'message'     => $message
                                )
                            );
                              
                             
                        endif;  // Test errors before sending email
                    endif;  // Test for sending email
 
                      
            endif; // Errors
        } else {
            $errors['user_id'] = "Invalid User Id ";   
        }

		$json = new JSON_Response(); 
        $json->input      = $this->input;
        $json->errors     = !empty($errors)?$errors:'';
	    $json->print_response();
    }
}
