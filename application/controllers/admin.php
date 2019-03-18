<?php
ini_set('memory_limit', '-1');

class admin_controller extends Template_Controller {
	
	public $default_admin_page = '/admin/participants/';
	
 	function __construct($cont, $func) {
        parent::__construct($cont, $func);
        if($func!='login' && $func!='forgot' && $func!='update_pwd') {
			$this->logged_in_required();
		}
	}
 
  	public function logged_in_required() {
        if(!empty($_SESSION[Cookie::get('sc_381')])) {
            return true;
        }
        redirect('/admin/login');
    }
 	
	public function index() {
       if(Auth::is_user_logged_in()) {
            redirect($this->default_admin_page);
        } else {
            redirect('/admin/login');
        }
    }


    /*
    * Send email
    */
    public function send_email() {
        $content = new View('/admin/send_email.html'); 
       
        if(!empty($this->input['user_id'])) { 
        
            // Get participant info for amount_due and amount_due_paypal (required by marc)
            $this->input['user'] = Participants_model::get_participants($this->input); 
              
            $res = Emails_model::get_emails($this->input);
            $this->input['email'] = $res[0];
            
            
        } else {
            $this->input['errors'] = 'Unknow user';   
        }
                
        $content->input = $this->input;
               
        $this->template->header = new View('/admin/header_modal.html');
		$this->template->header->title = _('Send email'); 
        $this->template->header->active_menu = 'Email'; 
	    $this->template->content = $content; 
        $this->template->footer = new View('/admin/footer_modal.html');
        
        
    }

    /*
    * Edit Talk
    */
    public function edit_talk() {
        $content = new View('/admin/edit_talk.html');
        
        if(!empty($this->input['talk_id'])) { 
            $res = Talks_model::get_talks(array('id'=>$this->input['talk_id']));
            $this->input['talk'] = $res[0];
        } else {
            $this->input['errors'] = 'Unknow talk';   
        }
        
        // Get sessions
        $this->input['sessions'] = unserialize(SESSIONS); // see config.php
        
        // Get delivery paper dates
        $this->input['delivery_dates']  = unserialize(DELIVERY_DATES); // see config.php
        
        $content->input = $this->input;
        
        $this->template->header = new View('/admin/header_modal.html');
		$this->template->header->title = _('Edit Talk'); 
        $this->template->header->active_menu = 'Talks'; 
	    $this->template->content = $content; 
        $this->template->footer = new View('/admin/footer_modal.html');
    }


    /*
    * Edit Poster
    */
    public function edit_poster() {
        $content = new View('/admin/edit_poster.html');
        
        if(!empty($this->input['poster_id'])) { 
            $res = Posters_model::get_posters(array('id'=>$this->input['poster_id']));
            $this->input['poster'] = $res[0];
            
        } else {
            $this->input['errors'] = 'Unknow Poster';   
        }
        
        // Get sessions
        $this->input['sessions'] = unserialize(SESSIONS); // see config.php
        
        // Get delivery paper dates
        $this->input['delivery_dates']  = unserialize(DELIVERY_DATES); // see config.php
        
        $content->input = $this->input;
        
        $this->template->header = new View('/admin/header_modal.html');
		$this->template->header->title = _('Edit Poster'); 
        $this->template->header->active_menu = 'Posters'; 
	    $this->template->content = $content; 
	    $this->template->footer = new View('/admin/footer_modal.html');

    }
    
    /*
    * Edit Participant
    */
    public function edit_participant() {
        $content = new View('/admin/edit_participant.html');
         
        if(!empty($this->input['user_id'])) {
           
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
            
            // Get Payment Confirmation Email 
            $this->input['conf_emails'] = Payment_confirm_model::get_paymentEmail($this->input); 
            
            $content->input = $this->input;
            
           
            
        } else {
            $this->input['errors'] = 'Unknow user';   
        }
        
        // Get sessions
        $content->input['sessions'] = unserialize(SESSIONS); // see config.php
        
        // Get countries 
        $content->input['countries'] = Countries_model::get_countries();
        
        // Get payment methods
        $content->input['payment_methods']	 = 	 unserialize(PAYMENT_METHODS); // see config.php
        
        // Get currencies 
        $content->input['currencies'] =  unserialize(CURRENCIES); // see config.php
        
        // Get food requirements
        $content->input['food_requirements']  =  unserialize(FOOD_REQUIREMENTS); // see config.php 
        
        // Get proceedings
        $content->input['proceeding']	 = 	 unserialize(PROCEEDINGS); // see config.php
         
         // Get delivery paper dates
        $content->input['delivery_dates']  = unserialize(DELIVERY_DATES); // see config.php
        
        $this->template->header = new View('/admin/header_modal.html');
		$this->template->header->title = _('Edit Participant'); 
        $this->template->header->active_menu = 'Participants'; 
	    $this->template->content = $content;
	    $this->template->footer = new View('/admin/footer_modal.html');
    }
    

     /*
	 * Posters (admin)
	 */
     public function posters() {
		$content = new View('/admin/posters.html');
        
        // Count total talks
        $content->input['total_results']   = Posters_model::count_posters($this->input);
        
        // SET LIMIT to # of users to retrieve
        if(!empty($this->input['page'])) {
            $page = $this->input['page'];
            $this->input['start'] = ($this->input['page'] - 1) *MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
        } else {
           $this->input['start'] = 0;
        }
		$this->input['end']         =  MAX_RECORD_TO_DISPLAY; 
		$this->input['max_record']  = MAX_RECORD_TO_DISPLAY; 
				
 		// Get list of users
		$this->input['posters'] = Posters_model::get_posters($this->input);
      
		$content->input = $this->input;
        
        // Get delivery paper dates
        $content->input['delivery_dates']  = unserialize(DELIVERY_DATES); // see config.php
        
        // Get sessions
        $content->input['sessions'] = unserialize(SESSIONS); // see config.php
        
		$this->template->header = new View('/admin/header.html');
		$this->template->header->title = _('Posters'); 
        $this->template->header->active_menu = 'Proceedings'; 
        $this->template->header->active_submenu = 'Posters'; 
	    $this->template->content = $content;
	    $this->template->footer = new View('/admin/footer.html');
     }

    
     /*
	 * Talks (admin)
	 */
     public function talks() {
		$content = new View('/admin/talks.html');
        
        // Count total talks
        $content->input['total_results']   = Talks_model::count_talks($this->input);
        
        // SET LIMIT to # of users to retrieve
        if(!empty($this->input['page'])) {
            $page = $this->input['page'];
            $this->input['start'] = ($this->input['page'] - 1) *MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
        } else {
           $this->input['start'] = 0;
        }
		$this->input['end'] =  MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
		$this->input['max_record'] = MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
				
 		// Get list of users
		$this->input['talks'] = Talks_model::get_talks($this->input);
        
		$content->input = $this->input;
        
        // Get delivery paper dates
        $content->input['delivery_dates']  = unserialize(DELIVERY_DATES); // see config.php
        
        // Get sessions
        $content->input['sessions'] = unserialize(SESSIONS); // see config.php
        
		$this->template->header = new View('/admin/header.html');
		$this->template->header->title = _('Talks'); 
        $this->template->header->active_menu = 'Proceedings'; 
        $this->template->header->active_submenu = 'Talks'; 
	    $this->template->content = $content;
	    $this->template->footer = new View('/admin/footer.html');
     }
     
     /*
	 * Accomodation (admin)
	 */
     public function accomodation() {
		$content = new View('/admin/accomodation.html');
        
       
        // Count total users 
		$this->input['total_results'] = Participants_Model::count_participants($this->input);
        
        // SET LIMIT to # of users to retrieve
        if(!empty($this->input['page'])) {
            $page = $this->input['page'];
            $this->input['start'] = ($this->input['page'] - 1) *MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
        } else {
           $this->input['start'] = 0;
        }
		$this->input['end']         = MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
		$this->input['max_record']  = MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
				
 		// Get list of users
		$this->input['participants'] = Participants_Model::get_participants($this->input);
        
        //pp($this->input['participants']);
        
        // Count number of confirmed participants
        //pp($this->input['participants']);
        $v =  array_count_values(array_map(function($foo){ if($foo['confirmed']==1||$foo['confirmed']==2) return 1;}, $this->input['participants']));
        $this->input['confirmed_participants'] = (!empty($v[1]))?$v[1]:0;   
          
		// Get pagination
		$this->input['page'] 		  = !empty($this->input['page'])?$this->input['page']:1;
		$this->input['pagination']    = Pagination::generate($this->input['page'],$this->input['total_results'],"IMO",MAX_RECORD_TO_DISPLAY);
        
        // GET ACC Abbreviation
        $this->input['accomodations'] = unserialize(ACCOMODATIONS);
        foreach($this->input['participants'] as $k=>$v):
            $this->input['participants'][$k]['accomodation'] = $this->input['accomodations'][$this->input['participants'][$k]['reg_type']]['abbr'];
        endforeach;
        
        $content->input = $this->input;
        
        // Get countries 
        $content->input['countries'] = Countries_model::get_countries();
        
        // Get payment methods
        $content->input['payment_methods']	 = 	 unserialize(PAYMENT_METHODS); // see config.php
         
        // Get food requirements
        $content->input['food_requirements']  =  unserialize(FOOD_REQUIREMENTS); // see config.php 
        
        // Get payment methods
        $content->input['payment_methods']	 = 	 unserialize(PAYMENT_METHODS); // see config.php
     
        
		$this->template->header = new View('/admin/header.html');
		$this->template->header->title = _('IMC Accomodation'); 
        $this->template->header->active_menu = 'Accomodation'; 
	    $this->template->content = $content;
	    $this->template->footer = new View('/admin/footer.html');
     }      
    
     /*
	 * Participants (admin)
	 */
     public function participants() {
		$content = new View('/admin/participants.html');
       
        // Count total users 
		$this->input['total_results'] = Participants_Model::count_participants($this->input);
        
        // SET LIMIT to # of users to retrieve
        if(!empty($this->input['page'])) {
            $page = $this->input['page'];
            $this->input['start'] = ($this->input['page'] - 1) *MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
        } else {
           $this->input['start'] = 0;
        }
		$this->input['end']         = MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
		$this->input['max_record']  = MAX_RECORD_TO_DISPLAY;// MAX_RECORD_TO_DISPLAY;
				
 		// Get list of users
		$this->input['participants'] = Participants_Model::get_participants($this->input);
         
        $v =  array_count_values(array_map(function($foo){ if($foo['confirmed']==1||$foo['confirmed']==2) return 1;}, $this->input['participants']));
        $this->input['confirmed_participants'] = (!empty($v[1]))?$v[1]:0;   
        unset($v);
        
        $v =  array_count_values(array_map(function($foo){ if($foo['confirmed']==-2) return 1;}, $this->input['participants']));
        $this->input['cancelled_participants'] = (!empty($v[1]))?$v[1]:0;   
        unset($v);
        
        // WORKSHOPS
        $v =  array_count_values(array_map(function($foo){ if($foo['workshop1']==1) return 1;}, $this->input['participants']));
        $this->input['workshop_1_total'] = (!empty($v[1]))?$v[1]:0;   
        unset($v);   
        
        $v =  array_count_values(array_map(function($foo){ if($foo['workshop2']==1) return 1;}, $this->input['participants']));
        $this->input['workshop_2_total'] = (!empty($v[1]))?$v[1]:0;   
        unset($v);       
        
        
        
         
		// Get pagination
		$this->input['page'] 		  = !empty($this->input['page'])?$this->input['page']:1;
		$this->input['pagination']    = Pagination::generate($this->input['page'],$this->input['total_results'],"IMO",MAX_RECORD_TO_DISPLAY);
        
        // GET ACC Abbreviation
        $this->input['accomodations'] = unserialize(ACCOMODATIONS);
        foreach($this->input['participants'] as $k=>$v):
            $this->input['participants'][$k]['accomodation'] = $this->input['accomodations'][$this->input['participants'][$k]['reg_type']]['abbr'];
        endforeach;
        
        $content->input = $this->input;
        
        // Get countries 
        $content->input['countries'] = Countries_model::get_countries();
        
        // Get payment methods
        $content->input['payment_methods']	 = 	 unserialize(PAYMENT_METHODS); // see config.php
         
        // Get food requirements
        $content->input['food_requirements']  =  unserialize(FOOD_REQUIREMENTS); // see config.php 
        
        // Get payment methods
        $content->input['payment_methods']	 = 	 unserialize(PAYMENT_METHODS); // see config.php
     
        
		$this->template->header = new View('/admin/header.html');
		$this->template->header->title = _('IMC Participants'); 
        $this->template->header->active_menu = 'Participants'; 
	    $this->template->content = $content;
	    $this->template->footer = new View('/admin/footer.html');
     }  
    
    
    /*
    * Logs 
    */
     public function logs_view($vars = array()) {
        $content = new View('/admin/logs_view.html');
        
        $file = fopen(LOG_DIR.'/admin_login.txt', "r") or fopen(LOG_DIR.'/admin_login.txt', "r+");
        $content->input =  fread($file,filesize(LOG_DIR.'/admin_login.txt'));
        fclose($file);
          
        $this->template->header = new View('/admin/header.html');
		$this->template->header->title = _('Admin Log'); 
        $this->template->header->active_menu = 'Logs'; 
	    $this->template->content = $content;
	    $this->template->footer = new View('/admin/footer.html');
    }
    
        /*
    * Logs 
    */
     public function logs_view_participant($vars = array()) {
        $content = new View('/admin/logs_view_participant.html');
        
        $file = fopen(LOG_DIR.'/participants.txt', "r")  or fopen(LOG_DIR.'/participants.txt', "r+");
        $content->input =  fread($file,filesize(LOG_DIR.'/participants.txt'));
        fclose($file);
        
         
        $this->template->header = new View('/admin/header.html');
		$this->template->header->title = _('Participants Log'); 
        $this->template->header->active_menu = 'Logs'; 
	    $this->template->content = $content;
	    $this->template->footer = new View('/admin/footer.html');
    }
    
    

    /*
	 * Forgot password (admin)
	 */
	public function forgot() {
		
		$content = new View('/admin/forgot.html');
		
		if( !empty($this->input['submit']) && !empty($this->input['email']) && filter_var($this->input['email'], FILTER_VALIDATE_EMAIL) ):
			 
            // Test if the email is in the admin table
            $sql = "SELECT * from admin_users where email= :email";
            $user = DBF::query($sql,$this->input,'num');

            if(empty($user)):
                $this->input['error'] = _('Unknown email address, please try again or contact Vincent.'); 
            else:
 
                $user_id = $user[0]['id'];

                // Generate a pwd_link
                $unique = false;
                while(!$unique) {
                    $pwd_link = md5(rand(99999,999999));
                    $sql = "SELECT user_id from admin_users where pwd_link = :pwd_link";
                    $res = DBF::query($sql,$this->input,'num');
                    $unique = (empty($res));
                    unset($res);
                }  
                unset($unique);


                // Add the pwd_link to the user records
                $sql = "UPDATE  admin_users 
                        set     pwd_link = :pwd_link
                        WHERE   id = :id";
                DBF::set($sql,array('pwd_link'=>$pwd_link, 'id'=> $user_id));
                
                $user = $user[0];

                /****************************
                * Send email to user
                ****************************/
                $message  =  "<html><body>";
                $message  .= '<p>Click the link below to update your IMC'.CONF_YEAR.' admin password.</p>';
                $message  .= '<p><strong>IMPORTANT</strong> This link is for single use only.</p>';
                $message  .= '<a href="'.PUBLIC_URL.'/admin/update_pwd/?chp='.$pwd_link.'">'.PUBLIC_URL.'/admin/update_pwd/?chp='.$pwd_link.'</a>';
                $message .=  "</body></html>";
                        
                $from     = MAIN_CONTACT; 
                   
                
                Mail::send(
                     array(  'from'      => MAIN_CONTACT,
                             'from_name' => 'IMC ' . CONF_YEAR . ' Admins',
                             'to'        => $user['email'],
                             'to_name'   => $user['firstname'] . ' ' . $user['lastname'],
                             'subject'   => 'Reset your password',
                             'message'   => $message,
                             'cc_name'   => 'IMC ' . CONF_YEAR . ' ',
                             'cc_email'  => 'vperlerin@gmail.com',
                      )
                );
  
                $this->input['success'] = 'We just sent an email containing a link for resetting your password.';
                 

            endif;
           
            

        elseif(!empty($this->input['submit'])):
            $this->input['error'] = _('Unknown email address, please try again or contact Vincent.'); 
        endif;
		
		$this->template->header = new View('/admin/header.html');
		$this->template->header->title = _('Forgot your password'); 
        $this->template->header->no_menu = true;
        $this->template->content = $content;
        $content->input = $this->input; 
	    $this->template->footer = new View('/admin/footer.html');
    }    


         /**
         * Update Password from Email (see forgot password)
         */
        public function update_pwd() {
 
            if(empty($this->input['chp'])):
                redirect('/admin/login');
            endif;
             
            if(!empty($this->input['chp']) && empty($this->input['submit'])):
                
                // We test the chp
                $sql = "SELECT * from admin_users where pwd_link = :chp";
                $user = DBF::query($sql,$this->input,'num');
               
                if(!empty($user)):
                    $user = $user[0];
 
                    // Replace the pwd_link so it expires
                    // Generate a new pwd_link
                    $unique = false;
                    while(!$unique) {
                        $pwd_link = md5(rand(99999,999999));
                        $sql = "SELECT user_id from admin_users where pwd_link = :pwd_link";
                        $res = DBF::query($sql,$this->input,'num');
                        $unique = (empty($res));
                        unset($res);
                    }  
                    unset($unique);
  
                    // Add the new pwd_link to the user records
                    $sql = "UPDATE  admin_users 
                            set     pwd_link = :pwd_link
                            WHERE   id = :id";
                    DBF::set($sql,array('pwd_link'=>$pwd_link, 'id'=> $user['id']));
                    
                    // Add the User_id to the session
                    $_SESSION['id'] = $user['id'];

                else:
                    $this->input['error'] =  'This link is not valid anymore. Please, <a href="/user/forget_password">try again</a>.';
                    $this->input['hide_form'] = true;
                endif;
            elseif(empty($this->input['submit'])):
                    $this->input['error'] =  'This link is not valid.';
                    $this->input['hide_form'] = true;
            
            endif;
 
            
            if(!empty($this->input['submit']) && !empty($this->input['pwd'])):
 

                // Update the password 
                $sql = "UPDATE admin_users
                        SET _pwd = :pwd
                        WHERE id = :user_id";
                DBF::set($sql,array('pwd'=>Crypt::encrypt($this->input['pwd']),'user_id'=>$_SESSION['id']));
                
 
                $this->input['success']  =  'Your password has been updated. Please, <a href="/admin/login">log back in</a>.';
                $this->input['hide_form'] = true;
            endif;

            $this->template->header = new View('/shared/header.html');
            $content = new View('/admin/update_pwd.html');
            $content->input = $this->input; 
            $this->template->content = $content;
            $this->template->footer = new View('/shared/footer.html'); 
            
        }

    /*
	 * Login (admin)
	 */
	public function login() {
		
        $content = new View('/admin/login.html');
        
 
		if(!empty($this->input['email']) &&  !empty($this->input['pwd']) ) {
         
			// Test if he's admin
            $res = Admins_model::is_admin_user($this->input); 
         
            
			// Set cookie session
			if ($res) {
			    $session = md5(date("YmdHis")   . rand(99999,999999));
			    Cookie::set('sc_381', $session, false);
            }

            $_SESSION[$session] = $res;
         	
			if($res) {
                // Write log 
                $file = LOG_DIR.'/admin_login.txt';
                $text = date("Y-m-d H:i:s") . " >> ". $this->input['email'] ."\n";
                file_put_contents($file, $text, FILE_APPEND | LOCK_EX);
             
                redirect($this->default_admin_page);
			} else {
				$input['error'] = _('Unknown user/email combination, please try again'); 
				$content->input = $input;
			}
			
			
		}  
		
		$this->template->header = new View('/admin/header.html');
		$this->template->header->title = _('Login'); 
        $this->template->header->no_menu = true;
	    $this->template->content = $content;
	    $this->template->footer = new View('/admin/footer.html');
    }
    
    
     
    /*
    * Open Session
    */
     public function open_session($vars = array()) {
        $content = new View('/admin/open_session.html');
        
        // Get All questions 
        $this->input['questions'] = Opensession_Model::get_questions(array());
        
        if(!empty($this->input['question_id'])):
            // Delete the question
            Opensession_Model::delete_question($this->input);
            redirect('/admin/open_session');
            die;
        endif;
         
         
        $content->input = $this->input; 
        $this->template->header = new View('/admin/header.html');
		$this->template->header->title = _('Admin Log'); 
        $this->template->header->active_menu = 'OpenSession'; 
	    $this->template->content = $content;
	    $this->template->footer = new View('/admin/footer.html');
    }
    
	
	/**
	 * Logout (admin)
	*/
	public function logout() {
        unset($_SESSION[Cookie::get('sc_381')]);
        Cookie::delete('sc_381');
        redirect('/');
    }
	
	
}
