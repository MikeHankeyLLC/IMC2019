<?php

class poster_api_controller extends Template_Controller {

    function __construct($cont, $func) {
        parent::__construct($cont, $func);
    }

    /*
    * Delete Poster
    */
    public function delete_poster() {
       if(!empty($this->input['poster_id'])) {
            Posters_model::remove_posters(array('poster_id'=>$this->input['poster_id']));
       } else {
            $errors['poster_id'] = "Invalid poster_id ";   
       }
       
       $json = new JSON_Response(); 
       $json->input      = $this->input;
       $json->errors     = !empty($errors)?$errors:'';
       $json->print_response();
    }   
    

    /*
     * Edit Poster
     */
    public function edit_poster() {
        $errors = array();
        
        if(!empty($this->input['poster_id'])) {
             
             if(!empty($this->input['poster'])): 
                if(empty($this->input['title'])):
                       $errors['poster'] = "Posters require a title.";
                endif;
                if(empty($this->input['abstract'])):
                       $errors['abstract'] = "Posters require an abstract.";
                endif;
                if(empty($this->input['authors'])):
                       $errors['authors'] = "Posters require at least one author.";
                endif;
                if(empty($this->input['session'])):
                       $errors['session'] = "Posters require a session";
                endif;
                if(empty($this->input['poster_delivery_date'])): 
                       $errors['delivery_date'] = "Posters require a paper delivery date";
                endif; 
             endif; 
             
             if(empty($errors)): 
                 // Update poster
                 Posters_model::update_poster($this->input);
             endif; 
        } else {
            $errors['poster_id'] = "Invalid Poster id";
        }
		$json = new JSON_Response(); 
        $json->input      = $this->input;
        $json->errors     = !empty($errors)?$errors:'';
	    $json->print_response();
                    
    }
}
