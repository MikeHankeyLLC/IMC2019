<?php

class index_controller extends Template_Controller {
    function __construct($cont, $func) {
        parent::__construct($cont, $func);
    }
     
    public function get_paypal_price($price) {
        return round($price+(0.034*$price +0.35)/0.966,2,PHP_ROUND_HALF_UP);
    }
    
    public function index($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Disclaimer';
        $content = new View('/index.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->no_row = true;
        $this->template->header->slider = true;
        $this->template->header->active = "index";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
        $this->template->footer->no_row = true;
    }
    

     
    public function opensession($vars = array()) { 
      $this->page_title = 'IMC ' . CONF_YEAR .' - Open Session';
        $content = new View('/opensession.html');
        
        if(!empty($this->input['ask_question'])): 
            
            // Load Captcha 
            require_once __DIR__ . '/../system/vendors/autoload.php'; 
        
             // captcha response
            $recaptcha = new \ReCaptcha\ReCaptcha(reCaptcha_Secret_Key);
            $resp = $recaptcha->verify($this->input['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
			
			if (!$resp->isSuccess()) {
				// Invalid CAPTCHA 
				$this->input['errors']['captcha'] = _("The reCAPTCHA wasn't entered correctly. Please, try it again.");
			}
            
            // Validate Email
            if (!filter_var($this->input['email'], FILTER_VALIDATE_EMAIL)) :
                $this->input['errors']['email'] = 'Please, enter a valide email address.';
            endif;
            
            // Validate Name
            if(empty($this->input['name'])) :
                $this->input['errors']['name'] = 'Please, enter your name.';
            endif;
            
           
            // Validate Question
            if(empty($this->input['question'])) :
                $this->input['errors']['question'] = 'Please, enter a question';
            endif;   
                     
            
            if(empty($this->input['errors'])): 
            
                // Add Question to db
                Opensession_model::add_question($this->input);
                
                $message  =  "<html><body>";
                $message .= '<p>We just received a new from for the IMC'.CONF_YEAR.' Open Session from ' . $this->input['name']. ' ('. $this->input['email'] . ')</p>';
                $message .= '<p><strong>The question:</strong><br> <em>' . $this->input['question']. '</em></p>';
                $message .= '<br/><p>You can review and print all the questions <a href="http://imc2017.imo.net/admin/open_session" target="_blank">here</a></p>';
                $message .=  "</body></html>";
                
                // Send email to Admin
                $headers = "From: ".$this->input['email']."\r\n"
                         . 'Reply-To: ' .$this->input['email'] . "\r\n"
                         . 'X-Mailer: PHP/' . phpversion() . "\r\n"
                         . "MIME-Version: 1.0\r\n"
                         . 'Content-type: text/html; charset=ISO-8859-1' . "\r\n";
                Mail::send(MAIN_CONTACT,$this->input['email'], "IMO Open Session - New Question",$message,$headers); 
                
                /****************************
                * Send email to participant
                ****************************/
                $message  =  "<html><body>";
                $message  .= '<p>Dear ' . $this->input['name']. ',</p>';
                $message  .= '<p><strong>Thank your for your questions for the IMC'.CONF_YEAR.' Open Session</strong></p>';
                $message  .= '<p><strong>Your question:</strong><br><em>' . $this->input['question']. '</em></p>';
                $message .=  '<p>We look forward to meeting you at the conference!</p>';
                $message .=  "</body></html>";
                        
                $from     = MAIN_CONTACT;
                $subject  = "IMC ". CONF_YEAR ." - Open Session Question " . date("F d, Y");
                $headers  = "From: IMC  <".MAIN_CONTACT.">\r\n";
                $headers .= "Return-Path: $from\r\n";
                $headers .= "Reply-To:" . MAIN_CONTACT . "\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                   
                Mail::send($this->input['email'],MAIN_CONTACT, $subject, $message, $headers);
                
                unset($message, $this->input);
              
                $this->input['success'] = '<span class="fa fa-check"></span> Your question has been sent. A panel of experts will answer your question during the IMC Open Session. Thank you.';
            endif;         
                     
                     
        endif;
    
        
        $content->input = $this->input;
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "open_session";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
     
    
    public function contact($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Contact';
        $content = new View('/contact.html');
        
        if(!empty($this->input['send_message'])): 
            
            // Load Captcha 
            require_once __DIR__ . '/../system/vendors/autoload.php'; 
        
            // captcha response 
            $recaptcha = new \ReCaptcha\ReCaptcha(reCaptcha_Secret_Key);
            $resp = $recaptcha->verify($this->input['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
			
			if (!$resp->isSuccess()) {
				// Invalid CAPTCHA 
				$this->input['errors']['captcha'] = _("The reCAPTCHA wasn't entered correctly. Please, try it again.");
			} 
            
            // Validate Email
            if (!filter_var($this->input['email'], FILTER_VALIDATE_EMAIL)) :
                $this->input['errors']['email'] = 'Please, enter a valide email address.';
            endif;
            
            // Validate Name
            if(empty($this->input['name'])) :
                $this->input['errors']['name'] = 'Please, enter your name.';
            endif;
            
            // Validate Subject
            if(empty($this->input['subject'])) :
                $this->input['errors']['subject'] = 'Please, enter a subject (ex: Extra-accommodation, Accessibility, etc.)';
            else:
                $this->input['subject'] = $this->input['subject'] . ' - Contact from IMC'. CONF_YEAR . 'Website'; 
            endif;   
            
            
            // Validate Message
            if(empty($this->input['message'])) :
                $this->input['errors']['message'] = 'Please, enter a message';
            endif;   
                     
            
            if(empty($this->input['errors'])): 
                // Send email 
               
                $res = Mail::send(array(
                    "to"            => MAIN_CONTACT,
                    "to_name"       => "IMC".CONF_YEAR,
                    "from"          => $this->input['email'],
                    "from_name"     => $this->input['name'],
                    "subject"        => $this->input['subject'],
                    "message"       => $this->input['message']
                )); 
                 
                unset($this->input);
                if($res):
                    $this->input['success'] = '<span class="fa fa-check"></span> Your message has been sent. Thank you.';
                else:
                    $this->input['errors']['system'] = '<span class="fa fa-time"></span> Impossible to send your message - please try again later.';
                endif;
            endif;         
                     
                     
        endif;
        
        $content->input = $this->input;
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "contact";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    } 
    
    /**
    * Login to access reg details 
    */
    public function my_registration($vars = array()) {
        $content = new View('/my_registration.html');
        $this->page_title = 'IMC ' . CONF_YEAR .' - Registration';
        
        // Access page
        if(!empty($vars) && !empty($vars[0]) && empty($input['edit_link'])):
            $this->input['edit_link'] = $vars[0];
        else:
            $this->input['errors'] = _('Unknown registration code.');
        endif;
        
        // Login
        if(!empty($this->input['access_test']) && !empty($this->input['email'])):
           
            // Test email & edit_link
            if(Participants_model::test_edit_link($this->input)) {
                 redirect('/myreg_details?edit_link='.$this->input['edit_link'].'&email='.$this->input['email']);
            } else {
                 $this->input['login_error'] = _('Unknown combination email/registration code. Please, <a href="/contact">contact</a> us if necessary.');     
            }
             
        else:
           // Nothing here - just reload the page
        endif;
        
        $content->input = $this->input;
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "pra";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    /***
    * Here the participant has the possibility to update some info
    */
    public function myreg_details() {
        $content = new View('/my_registration_details.html');
        $this->page_title = 'IMC ' . CONF_YEAR .' - Registration Details';
        
        $this->input['user_id'] = Participants_model::test_edit_link($this->input);
         
         
        // WE UPDATE THE RECORD 
        if(!empty($this->input['submit'])) {
        
                // Strip tags
                foreach($this->input as $k=>$v) :
                   if(is_string($v)): $this->input[$k] = strip_tags($v); endif;
                endforeach;
             
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
                
                 // City
                if(empty($this->input['city'])) {
                       $errors['city'] = 'Please, enter a city (+ state/province/region)';
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
                
                
                // Talks (all talks need title & abstract
                if(!empty($this->input['talk'])) {
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
                }
                
                                 
                // Posters (all talks need title & abstract
                if(!empty($this->input['poster'])) { 
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
                } 
                
             
                // Test errors
                if(empty($errors)) {
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
                    
                     unset($this->input['poster'],$this->input['talk']);
                     $this->input['update_success'] = _('Your record has been updated. Thank you');
                    
                } else {
                    
                     $this->input['update_errors'] = $errors;
                    
                }
         
        } 
                
          
        // WE GET THE INFO (AND TEST EMAIL & EDIT_LINK ONE MORE TIME)
        if(!empty($this->input['email']) && !empty($this->input['edit_link']) && $this->input['user_id']) {
            
            $tmpinput['email'] =  $this->input['email'];
            $tmpinput['edit_link'] =  $this->input['edit_link'];
            $tmpinput['user_id'] =  $this->input['user_id'];    
            
            if(!empty($this->input['update_success'])):
                $tmpinput['update_success']=  $this->input['update_success'];    
            endif;
            
            if(!empty($this->input['update_errors'])):
                $tmpinput['update_errors']=  $this->input['update_errors'];    
            endif;         
            
            unset($this->input);
            
            $this->input = $tmpinput;
              
             
            // Get Participants (and Registration)
            $res = Participants_model::get_participants($this->input); 
          
            if(!empty($res)) :
                if($res[0]['pay_date']=='0000-00-00'): 
                    $res[0]['pay_date']=''; 
                endif;
                $this->input =  array_merge($this->input, $res[0]);
            endif; 
            unset($res);
            
            // Get Details
            $res = Details_model::get_details($this->input);
            if(!empty($res)) :
                $this->input =  array_merge($this->input, $res[0]);
            endif;
            unset($res);
            
            // Get Travels
            $res = Travels_model::get_travels($this->input);
            if(!empty($res)) :
                $this->input =  array_merge($this->input, $res[0]);
                
                // Split arrival/depart time
                if(!empty($this->input['arrival_time'])): 
                    $tmp_time =  explode(':',$this->input['arrival_time']);
                    $this->input['arrival_time_h'] = $tmp_time[0];
                    $this->input['arrival_time_m'] = $tmp_time[1];
                    unset($tmp_time);
                endif;
                
                 // Split arrival/depart time
                if(!empty($this->input['departure_time'])): 
                    $tmp_time =  explode(':',$this->input['departure_time']);
                    $this->input['departure_time_h'] = $tmp_time[0];
                    $this->input['departure_time_m'] = $tmp_time[1];
                    unset($tmp_time);
                endif;
                
            endif;
            unset($res);
            
          
            // Get Talks
            $this->input['talks'] = Talks_model::get_talks_from_users($this->input);
          
            if(!empty($this->input['talks'])) :
                // Build Array for js (see contributions.js)
                foreach($this->input['talks'] as $talk) {
                       $this->input['talk'][]               = $talk['title'];
                       $this->input['talk_authors'][]       = $talk['authors'];
                       $this->input['talk_abstract'][]      = $talk['abstract'];
                       $this->input['talk_session'][]       = $talk['session'];
                       $this->input['talk_duration'][]      = $talk['duration'];
                       $this->input['talk_delivery_date'][] = $talk['delivery_date'];
                 }
            endif; 
            unset($this->input['talks']);
            
             // Get Posters
            $this->input['posters'] =  Posters_model::get_posters_from_users($this->input);
            if(!empty($this->input['posters'])) :
                // Build Array for js (see contributions.js)
                foreach($this->input['posters'] as $poster) {
                       $this->input['poster'][] = $poster['title'];
                       $this->input['poster_authors'][] = $poster['authors'];
                       $this->input['poster_abstract'][] = $poster['abstract'];
                       $this->input['poster_session'][] = $poster['session'];
                       $this->input['poster_delivery_date'][] = $poster['delivery_date'];
                  }
            endif; 
            
            
            $this->input['prog'] = (!empty( $this->input['talk'] )) || (!empty( $this->input['poster'] )) ?"yes":"no";
            
             // Get accomodation
            $this->input['accomodations'] = unserialize(ACCOMODATIONS); // see config.php
            
            // Get Bill details
            $this->input['bill'] = "<table class='table table-striped'><tbody>";
            $this->input['bill'] .= "<tr><td>". $this->input['accomodations'][$this->input['reg_type']]['details'] ."</td><td class='text-right'>".number_format($this->input['accomodations'][$this->input['reg_type']]['price'], 2, '.', '') ."&euro;</td></tr>";
            
            // Add Tshirt price if any
            if(!empty($this->input['tshirt'])): 
                $this->input['bill'] .= "<tr><td>T-shirt</td><td class='text-right'>".number_format(TSHIRT_PRICE, 2, '.', '')."&euro;</td></tr>";
            endif;
            
            // Add Proceedings price if any
            if($this->input['proceedings']=='print'):
                $this->input['bill'] .= "<tr><td>Print Proceedings</td><td class='text-right'>".number_format(PROCEEDINGS_PRICE, 2, '.', '')."&euro;</td></tr>";
            endif;
            
            if($this->input['payment_meth']=='1'):
                 $pay_pal_fees = $this->input['amount_due_paypal'] - number_format( $this->input['amount_due'], 2, '.', '');
                 $this->input['bill'] .= "<tr><td>Paypal Fees</td><td class='text-right'>".number_format($pay_pal_fees, 2, '.', '')."&euro;</td></tr>";
                 $this->input['bill'] .= "</tbody><tfoot><tr><td><strong>TOTAL</strong></td><td class='text-right'><strong>". number_format($this->input['amount_due_paypal'], 2, '.', '')."&euro;</strong></td></tr></tfoot>";      
                 unset($pay_pal_fees);                  
             else:
                 $this->input['bill'] .= "</tbody><tfoot><tr><td><strong>TOTAL</strong></td><td class='text-right'><strong>".number_format($this->input['amount_due'], 2, '.', '')."&euro;</strong></td></tr></tfoot>";
            endif;
            
            $this->input['bill']  .= '</table>';
            
           
        }
            
        
        
        /* 
           elseif(empty($this->input['user_id'])):
        
                redirect('/my_registration');
        
        endif;
        */
        
     
        
        // Get sessions
        $this->input['sessions'] = unserialize(SESSIONS); // see config.php
            
        // Get countries 
        $this->input['countries'] = Countries_model::get_countries();
            
        // Get payment methods
        $this->input['payment_methods']	 = 	 unserialize(PAYMENT_METHODS); // see config.php
            
        // Get currencies 
        $this->input['currencies'] =  unserialize(CURRENCIES); // see config.php
            
        // Get food requirements
        $this->input['food_requirements']  =  unserialize(FOOD_REQUIREMENTS); // see config.php 
            
        // Get proceedings
        $this->input['proceeding']	 = 	 unserialize(PROCEEDINGS); // see config.php
             
        // Get delivery paper dates
        $this->input['delivery_dates']  = unserialize(DELIVERY_DATES); // see config.php
        
        
        $content->input = $this->input;
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "pra";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function gdpr($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Data Protection Policy';
        $content = new View('/gdpr.html');
        $this->template->header = new View('/shared/header.html'); 
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }	
	public function praticalinfo($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Practical Information';
        $content = new View('/praticalinfo.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "pra";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
     
    public function venue($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Venue';
        $content = new View('/venue.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "ven";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function posteraward($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Poster Award';
        $content = new View('/posteraward.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "post_award";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function photocompetition($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Photo competition';
        $content = new View('/photocompetition.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "photo_comp";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function topics($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Venue';
        $content = new View('/topics.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "topics";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }

    public function guideline($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Venue';
        $content = new View('/guideline.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "guideline";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }    
    
     public function excursion($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Excursion';
        $content = new View('/excursion.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "ex";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function program($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Travel Information';
        $content = new View('/program.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "prog";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function travel($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Travel Information';
        $content = new View('/travel.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "trav";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    
    public function acc($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Accomodation';
        $content = new View('/accomodation.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "acc";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function disclaimer($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Disclaimer';
        $content = new View('/disclaimer.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function bollmannsruh($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Modra';
        $content = new View('/bollmannsruh.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "bollmannsruh";
         $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    
        
    public function workshop1($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Workshops';
        $content = new View('/workshop1.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "work1";
         $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }    

    public function workshop1_day1($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Workshops';
        $content = new View('/workshop1_day1.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "work1";
         $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }


    public function workshop1_day2($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Workshops';
        $content = new View('/workshop1_day2.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "work1";
         $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }

        
    public function workshop2($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Workshops';
        $content = new View('/workshop2.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "work2";
         $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function participants($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Participants';
        $content = new View('/participants.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "participants";
        $content->input['participants'] = Participants_model::get_confirmed_participants();
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    
    
    public function soc($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Scientific Organizing Committee';
        $content = new View('/soc.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "soc";
        $content->input['participants'] = Participants_model::get_confirmed_participants();
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    public function proceedings($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Proceedings';
        $content = new View('/proceedings.html');
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "proceedings";
       
        $content->input['talks']   = Talks_model::get_all_confirmed_talks();
        $content->input['posters'] = Posters_model::get_all_confirmed_posters();
        
        // Get delivery paper dates
        $content->input['delivery_dates']  = unserialize(DELIVERY_DATES); // see config.php
        
        // Get sessions
        $content->input['sessions'] = unserialize(SESSIONS); // see config.php
        
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    
     public function payment($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Payment';
        $content = new View('/payment.html');
        $content->input = $this->input;
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "payment";
        
        // Get accomodation
        $content->input['accomodations'] = unserialize(ACCOMODATIONS); // see config.php
        
        // Get payment methods
        $content->input['payment_methods'] = unserialize(PAYMENT_METHODS); // see config.php

        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
    
     public function imo($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - IMO (International Meteor Organization)';
        $content = new View('/imo.html');
        $content->input = $this->input;
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "imo";
         

        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
    
 
    // All unknown pages will hit here
    public function registration($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Registration';
        $content = new View('/registration.html');
        $content->input = $this->input;
        
        $errors = array();
        
        // Submission 
        if(isset($this->input['submit'])) :
            /// Load Captcha 
            //require_once __DIR__ . '/../system/vendors/autoload.php'; 
            
          
        
            // Strip tags
            foreach($this->input as $k=>$v) :
               if(is_string($v)): $this->input[$k] = strip_tags($v); endif;
            endforeach;
            
              // Validation 
                // Firstname
                if(empty($this->input['firstname'])) {
                       $errors['firstname'] = 'Please, enter your first name';
                }
                
                // Lastname
                if(empty($this->input['lastname'])) {
                       $errors['lastname'] = 'Please, enter your last name';
                }
                 
                // Gender
                if(empty($this->input['gender'])) {
                       $errors['gender'] = 'Please, your gender';
                }
                
                // Address
                if(empty($this->input['address'])) {
                       $errors['address'] = 'Please, enter an address';
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
                
                // Arrival Date
                //$this->input['arrival_date'] =  CONF_YEAR . '-' . $this->input['arrival_month']. '-' . $this->input['arrival_day'];
                $this->input['arrival_date'] = $this->input['arrival_day'];
                
                
                // Departure Date
                //$this->input['departure_date'] =  CONF_YEAR . '-' . $this->input['departure_month']. '-' . $this->input['departure_day'];
                $this->input['departure_date'] = $this->input['departure_day'];
                
                // Tshirt 
                if(!empty($this->input['tshirt-q']) && empty($this->input['tshirt'])) {
                       $errors['tshirt'] = 'Please, select a t-shirt size or answer "No" to the question about the IMC' . CONF_YEAR . ' official t-shirt.';
                }
                
                // reg_type
                if(empty($this->input['reg_type'])) {
                       $errors['reg_type'] = 'Please, choose a registration type';
                }
               
                // Payment method 
                if(empty($this->input['payment_meth'])) {
                       $errors['payment_meth'] = 'Please,  select your payment method';
                }
                
                // Legal Approval
                if(empty($this->input['ok'])) {
                       $errors['ok'] = 'Please, acknowledge the service agreement and disclaimer';
                }
              
              
              
              
                // Captcha
                /*
                $recaptcha = new \ReCaptcha\ReCaptcha(reCaptcha_Secret_Key);
                $resp = $recaptcha->verify($this->input['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
                
                if (!$resp->isSuccess()) {
                    // Invalid CAPTCHA 
                    $errors['captcha'] = _("Please, confirm you are not a robot.");
                }
                */
                
                /*
                // Meteoroids shuttle
                if(empty($this->input['meteoroid_shuttle'])) {
                       $errors['meteoroid_shuttle'] = 'Please, answer the question about the Meteoroids shuttle.';
                }
                */
                
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
               
                // Test errors
                if(empty($errors)) :
                
                    unset($this->input['g-recaptcha-response']);
                    
                    $ACC    = unserialize(ACCOMODATIONS);
                    $content->input['bill']    = "<table class='table table-striped'><tbody>";
                
                    // Arrival Time
                    $this->input['arrival_time'] = $this->input['arrival_time_h'].':'.$this->input['arrival_time_m'].':00';

                    // Departure Time
                    $this->input['departure_time'] = $this->input['departure_time_h'].':'.$this->input['departure_time_m'].':00';
                 
                    // Get amount due based on choice and date (see config.php for early date)
                    $this->input['amount_due'] = $this->input[$this->input['reg_type'].'_price'];
                    $content->input['bill']  .= "<tr><td>". $ACC[$this->input['reg_type']]['details'] ."</td><td class='text-right'>".number_format($this->input[$this->input['reg_type'].'_price'], 2, '.', '') ."&euro;</td></tr>";
                    
                    // Add Tshirt price if any
                    if(!empty($this->input['tshirt_q']) && $this->input['tshirt_q']=='yes'): 
                        $this->input['amount_due']+= TSHIRT_PRICE;
                        $content->input['bill']  .= "<tr><td>T-shirt</td><td class='text-right'>".number_format(TSHIRT_PRICE, 2, '.', '')."&euro;</td></tr>";
                    endif;
                    
                    // Add Proceedings price if any
                    if($this->input['proceedings']=='print'):
                        $this->input['amount_due']+= PROCEEDINGS_PRICE;
                        $this->input['print']+= PROCEEDINGS_PRICE; // PROCEEDINGS PRINT PRICE
                        $content->input['bill']  .= "<tr><td>Print Proceedings</td><td class='text-right'>".number_format(PROCEEDINGS_PRICE, 2, '.', '')."&euro;</td></tr>";
                    endif;
                                         
                    // Add fees for paypal
                    $this->input['amount_due_paypal'] = $this::get_paypal_price($this->input['amount_due']);  
                    $content->input['amount_due_paypal'] = $this->input['amount_due_paypal'];
                    
                    // PAYPAL
                    if($this->input['payment_meth']=='1'):
                         $pay_pal_fees = $this->input['amount_due_paypal'] - number_format( $this->input['amount_due'], 2, '.', '');
                         $content->input['bill']  .= "<tr><td>Paypal Fees</td><td class='text-right'>".number_format($pay_pal_fees, 2, '.', '')."&euro;</td></tr>";
                         $content->input['bill']  .= "</tbody><tfoot><tr><td><strong>TOTAL</strong></td><td class='text-right'><strong>". number_format($this->input['amount_due_paypal'], 2, '.', '')."&euro;</strong></td></tr></tfoot>";      
                         unset($pay_pal_fees);                  
                     else:
                         $content->input['bill']  .= "</tbody><tfoot><tr><td><strong>TOTAL</strong></td><td class='text-right'><strong>".number_format($this->input['amount_due'], 2, '.', '')."&euro;</strong></td></tr></tfoot>";
                    endif;
                   
                    $content->input['bill']  .= '</table>';
                    
                    // Get IP
                    $this->input['ip'] =  $_SERVER['REMOTE_ADDR'];
                    
                    // Generate Edit Link
                    // Create a code so the user can see his registration details
                    $this->input['edit_link'] = md5(rand(99999,999999));
                    while(Participants_Model::count_edit_link(array('edit_link' => $edit_link)) != 0) {
                       $this->input['edit_link'] = md5(rand(99999,999999));
                    } 
 
                    // Add participants (and get user_id)
                    $this->input['user_id'] = Participants_model::add_participants($this->input);

                    // Write log
                    $file = LOG_DIR.'/participants.txt';
                    $text = date("Y-m-d H:i:s") . " >> NEW PARTICIPANT ADDED  (ID: ". $this->input['user_id'] ." - ". $this->input['firstname'] ." ". $this->input['lastname'] ." - IP: " . $this->input['ip'].")\n";
                    file_put_contents($file, $text, FILE_APPEND | LOCK_EX);
                  
                     
                    if(!empty($this->input['user_id'])) {
                        
                        // Add participants Travel info
                        $t_id = Travels_model::add_travelInfo($this->input);
                        
                        // Add participants Details
                        $d_id = Details_model::add_details($this->input);
                        
                      
                        // Add participants Registration
                        $r_id = Registrations_model::add_registration($this->input);
                         
                        // Write log
                        $file = LOG_DIR.'/participants.txt';
                        $text = date("Y-m-d H:i:s") . ">> NEW PARTICIPANT DETAILS (ID: ". $this->input['user_id'] ." - ". $this->input['firstname'] ." ". $this->input['lastname'] .")\n>>     TRAVEL ID#".$t_id ."\n>>     DETAILS ID#".$d_id ."\n>>     REG ID#".$r_id ."\n";
                        file_put_contents($file, $text, FILE_APPEND | LOCK_EX);
                        unset($t_id, $d_id, $r_id);
                                               
                        // Add papers if any 
                        if(!empty($this->input['talk'])): 
                             foreach($this->input['talk'] as $key=>$val):
                                Talks_model::add_talk(array(
                                    'year'      => $this->input['year'],
                                    'title'     => $val,
                                    'authors'   => $this->input['talk_authors'][$key],
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
                         
                        // Send email to admins
                        $message  =  '<html><body>';
                        $message .=  '<h3>A new IMC Participant has just registered  for IMC ' . CONF_YEAR . '</h3>';
                        $message .=  Participants_helper::get_email_table($this->input);
                        $message .=  '<p>Source ' . BASE_URL . '</p>';
                        $message .=  '</body></html>';
                        
                         
                        $subject  = "IMC ". CONF_YEAR ." - Registration " . date("F d, Y");
                           
                        $res = Mail::send(
                            array('from'=>IMC_EMAIL,
                                  'from_name'=>'IMC ' . CONF_YEAR . ' Org. Committee',
                                  'to'=> IMC_EMAIL,
                                  'to_name'=>'IMC ' . CONF_YEAR . ' Org. Committee',
                                  'subject'=>$subject,
                                  'message'=>$message
                            )
                        );
                        
                        
                        /****************************
                         * SPECTROSCOPIC Workshops
                        ****************************/
                        if(!empty($this->input['workshop2'])):
                            $subject  = "IMC ". CONF_YEAR ." - Spectroscopic Workshop Registration " . date("F d, Y");
                            $message  = "<html><body>";
                            $message .= "<p>Dear Regina, Juraj and others,</p>";
                            $message .= "<p>A new participant just registered to the <strong>IMC2018 Spectropscopic Workshop</strong>:</p>";
                            $message .= "<strong>".$this->input['title'] . ' ' . $this->input['firstname']. ' ' . $this->input['lastname'] . "</strong><br>";
                            $message .= $this->input['email'];
                            $message .= "<p>You can contact Marc if you need further details.</p>";
                            $message .= "<p>Thank you and see you in Slovakia!</p>";
                            $message .= "Vincent - vperlerin@gmail.com";
                            
                            Mail::send(
                                array('from'=>IMC_EMAIL,
                                      'from_name'=>'IMC ' . CONF_YEAR . ' Org. Committee',
                                      'to'=> 'Regina.Rudawska@esa.int',
                                      'to_name'=>'IMC ' . CONF_YEAR . ' Org. Committee',
                                      'subject'=>$subject,
                                      'message'=>$message
                            ));
                            
                            
                            Mail::send(
                                array('from'=>IMC_EMAIL,
                                      'from_name'=>'IMC ' . CONF_YEAR . ' Org. Committee',
                                      'to'=> 'imc2018@imo.net',
                                      'to_name'=>'IMC ' . CONF_YEAR . ' Org. Committee',
                                      'subject'=>$subject,
                                      'message'=>$message
                            ));
                        endif;
                
 
 
                        /****************************
                         * VISUAL Workshops
                        ****************************/
                        if(!empty($this->input['workshop1'])):
                            $subject  = "IMC ". CONF_YEAR ." - Spectroscopic Workshop Registration " . date("F d, Y");
                            $message  = "<html><body>";
                            $message .= "<p>Dear Cis and others,</p>";
                            $message .= "<p>A new participant just registered to the <strong>IMC2018 Visual Workshop</strong>:</p>";
                            $message .= "<strong>".$this->input['title'] . ' ' . $this->input['firstname']. ' ' . $this->input['lastname'] . "</strong><br>";
                            $message .= $this->input['email'];
                            $message .= "<p>You can contact Marc if you need further details.</p>";
                            $message .= "<p>Thank you and see you in Slovakia!</p>";
                            $message .= "Vincent - vperlerin@gmail.com";
                            
                            Mail::send(
                                array('from'=>IMC_EMAIL,
                                      'from_name'=>'IMC ' . CONF_YEAR . ' Org. Committee',
                                      'to'=> 'imc2018@imo.net',
                                      'to_name'=>'IMC ' . CONF_YEAR . ' Org. Committee',
                                      'subject'=>$subject,
                                      'message'=>$message
                            ));
                        endif;                        
                        
 
                        /****************************
                         * Send email to participant
                        ****************************/
                        $message  =  "<html><body>";
                        $message  .= '<p>Dear ' . $this->input['title'] . ' ' . $this->input['firstname']. ' ' . $this->input['lastname'] . ',</p>';
                        $message  .= '<p><strong>Congratulations, you registered for the  ' . $this->input['year'] . ' International Meteor Conference!</strong></p>';
                        
                        $message .=  '<p>Your registration is nearly complete; all you need to do now is send the required payment of</p>';
                        $message .= '<ul>';
                        $message .=  '<li><strong>' . number_format( $this->input['amount_due'], 2, '.', '')  . ' EUR</strong> (usually free from EU and EEA countries; costs are always at participants\' expense) or</li>';
                        $message .=  '<li><strong>' . number_format( $this->input['amount_due_paypal'], 2, '.', '') . " EUR</strong> (by credit card / PayPal).</li>";
                        $message .= '</ul>';
                        $message .=  '<p>The necessary instructions for making your payment can be found at <a href="'.PUBLIC_URL.'/payment">'.PUBLIC_URL.'/payment</a></p>';    
                        $message .=  '<p>The registration fee should be sent to the IMO Treasurer <strong>IMMEDIATELY</strong>. ';
                        $message .=  'Delaying payment will result in the <strong>cancellation of your registration</strong>.</p>';
                        $message .=  '<p>The details you provided during the registration are appended below. ';
                        $message .=  'You can update some of your registration details - including updating talks and posters or adding new talks and posters - by clicking the link below<br/> ';
                        
                        $message .=    PUBLIC_URL.'/my_registration/'.$this->input['edit_link']; 
                        
                        
                        // Workshops
                        if(!empty($this->input['workshop1']) || !empty($this->input['workshop2'])):
                            $message .= '<p>You choose to participate to the following August 29 Workshop:</p>';
                            $message .= '<ul>';
                            if(!empty($this->input['workshop1'])):
                                $message .= '<li>Visual Workshop</li>';
                            endif;
                            if(!empty($this->input['workshop2'])):
                                $message .= '<li>Spectroscropic Workshop</li>';
                            endif;
                            $message .= '</ul>';
                            
                            $message .= '<p>REMINDER: Participation in the August 29th Workshops is free.<br><strong>The extra night of August 29-30 and the extra meals (lunch and dinner on August 29 and lunch on August 30) should be arranged with the hotel by the participants themselves, just like any other additional nights or meals you may wish.</strong></p><p>If you mention “IMC2018” while booking, a double room for one night costs 50€ plus 0.33€ tax (including breakfast), and a meal costs 8.50€. These prices are not guaranteed if you use third parties such as booking.com to book the hotel. If you would like to share a doubled room during the workshop, please contact the LOC (imc2018@imo.net) before booking the room. The LOC can then assist you with finding a roommate with whom you can share the room cost.</p>';
                        
                        endif;
                        
                        
                        
                        $message .=  '<p>We look forward to meeting you at the conference!</p>';
                        $message .=  Participants_helper::get_email_table($this->input);
                        $message .=  "</body></html>";
                        
                        $from     = MAIN_CONTACT;
                        $subject  = "IMC ". CONF_YEAR ." - Registration " . date("F d, Y");
                        
                        $res = Mail::send(
                            array('from'=>IMC_EMAIL,
                                  'from_name'=>MAIN_CONTACT,
                                  'to'=> $this->input['email'],
                                  'to_name'=>$this->input['firstname'] . ' ' . $this->input['lastname'],
                                  'subject'=>$subject,
                                  'message'=>$message
                            )
                        );
                  
                        
                        $content->input['success'] = '<p><small>Thank you for your registration. You will receive an email with specific instructions for the payment in a few minutes. If you don\'t receive this email within the next 24 hours, please <a href="mailto:vperlerin@gmail.com">contact us</a> ASAP.</small></p>';                 
                
                        
                        unset($ACC, $this->input);
                    } else {
                           $errors[] = 'An error occured during the process. Please, contact <a href="mailto:vperlerin@gmail.com">Vincent Perlerin</a>.';
                           $content->input['errors'] = $errors;
                    }
                
                else :
                    $content->input['errors'] = $errors;    
                endif;
        
                
        endif;
        // -> Submission
        
        // Get sessions
        $content->input['sessions'] = unserialize(SESSIONS); // see config.php
        
        // Get countries 
        $content->input['countries'] = Countries_model::get_countries();

        // Get payment methods
        $content->input['payment_methods'] = unserialize(PAYMENT_METHODS); // see config.php
         
        // Get food requirements
        $content->input['food_requirements']  = unserialize(FOOD_REQUIREMENTS); // see config.php
        
        // Get proceedings
        $content->input['proceeding']  = unserialize(PROCEEDINGS); // see config.php
        
        // Get delivery paper dates
        $content->input['delivery_dates']  = unserialize(DELIVERY_DATES); // see config.php
        
        // Get accomodation
        $content->input['accomodations'] = unserialize(ACCOMODATIONS); // see config.php
          
        $this->template->header = new View('/shared/header.html');
        $this->template->header->active = "reg";
        
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
    }
}
