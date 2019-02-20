<?php

class csv_controller extends Template_Controller {
 
    public $default_admin_page = '/admin/participants/';
 
    function __construct($cont, $func) {
        parent::__construct($cont, $func);
    }
 
    public function logged_in_required() {
        if(!empty($_SESSION[Cookie::get('sc_381')])) {
            return true;
        }
        redirect('/admin/login');
    }
 
     /*
	 * CSV File (reserved to admin)
	 */
     public function index() {
        $this::logged_in_required(); 
         
        $content = new View('/csv/body.csv');
        $this->template->header = new View('/csv/header.csv');
        
        
        // Participants Excel 
        if(!empty($this->input['csv_type']) && $this->input['csv_type']=='Participants') :
           // Get list of users
           $this->input['participants'] = Participants_Model::get_participants($this->input);
           
           // Get payment methods
           $payment_methods = 	 unserialize(PAYMENT_METHODS); // see config.php
         
           // Get food requirements
           $food_req  = unserialize(FOOD_REQUIREMENTS); // see config.php
           
           
            // COLUMN TITLES
            $content->input = "Id;Date of Registration;Last Name;First Name;Address;City;Postal Code;Country;Email;Date of Birth;Organization;Has Paid;Amount;Currency;Payment Date;Payment Method;Tshirt;Food Requirement;Food Comment";
            $content->input .= "\r\n";
            foreach($this->input['participants'] as $part) :
              
                // From imc_participants
                $country  = Countries_model::get_country_name(array('country_id'=>$part['country']));
                $has_paid = $part['paid']==1?'YES':'NO';
                $pay_date = (empty($part['pay_date']) || $part['pay_date']=='0000-00-00')?'n/a':$part['pay_date'];
                
                $payment_method  =   !empty($part['payment_meth'])?$payment_methods[$part['payment_meth']]:'';
                
                // From imc_details
                $details = Details_model::get_details(array('user_id'=> $part['id']));
                $details = $details[0];
                
                $food_req = !empty($part['payment_meth'])?$food_req[$details['food']]:'';
                $currency = empty($part['currency'])?'n/a':$part['currency'];
                
                // ROW
                $content->input .= $part['id'].';'.$part['reg_date'].';'.$part['lastname'].';'.$part['firstname'].';'.$part['address'].';'.$part['city'].';'.$part['post_code'].';'.$country.';'.$part['email'].';'.$part['dob'].';'.$part['org'].';'.$has_paid.';'.$part['amount'].';'.$currency.';'.$pay_date.';'.$payment_method.';'.$details['tshirt'].';'.$food_req.';'.$details['food_other'];
                $content->input .= "\r\n";
                
                $this->template->header->title = "Participants";
                
            endforeach;
            /*."\r\n"."Vincent;Perlerin";*/
        endif; 
         
       
        $this->template->content = $content;
        //$this->template->footer = new View('/csv/footer.html');   
     }
    
}
 