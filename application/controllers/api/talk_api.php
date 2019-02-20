<?php

class talk_api_controller extends Template_Controller {

    function __construct($cont, $func) {
        parent::__construct($cont, $func);
    }

    /*
    * Delete Talk
    */
    public function delete_talk() {
       if(!empty($this->input['talk_id'])) {
            Talks_model::remove_talks(array('talk_id'=>$this->input['talk_id']));
       } else {
            $errors['talk_id'] = "Invalid talk_id ";   
       }
       
       $json = new JSON_Response(); 
       $json->input      = $this->input;
       $json->errors     = !empty($errors)?$errors:'';
       $json->print_response();
    }   
    

    /*
     * Edit Talk
     */
    public function edit_talk() {
        $errors = array();
        
        if(!empty($this->input['talk_id'])) {
             
             if(!empty($this->input['talk'])): 
                if(empty($this->input['title'])):
                       $errors['title'] = "Talks require a title.";
                endif;
                if(empty($this->input['abstract'])):
                       $errors['abstract'] = "Talks require an abstract.";
                endif;
                if(empty($this->input['authors'])):
                       $errors['authors'] = "Talks require at least one author.";
                endif;
                if(empty($this->input['session'])):
                        $errors['session'] = "Talks require a session";
                endif;
                if(empty($this->input['talk_delivery_date'])): 
                     $errors['delivery_date'] = "Talks require a paper delivery date";
                endif; 
             endif; 
             
             if(empty($errors)): 
                 // Update talk
                 Talks_model::update_talk($this->input);
             endif; 
        } else {
            $errors['talk_id'] = "Invalid Talk id";
        }
		$json = new JSON_Response(); 
        $json->input      = $this->input;
        $json->errors     = !empty($errors)?$errors:'';
	    $json->print_response();
                    
    }
}
