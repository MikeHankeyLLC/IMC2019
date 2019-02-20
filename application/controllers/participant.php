<?php

class participant_controller extends Template_Controller {
   
    function __construct($cont, $func) {
        parent::__construct($cont, $func);
    }
      
    public function index($vars = array()) {
        $this->page_title = 'IMC ' . CONF_YEAR .' - Edit Your Registration';
        $content = new View('/participant/index.html');
		
		
		
		if(empty($this->input['edit_link']) || strtotime(DEADLINE)-strtotime("now")>0):
			$this->input['deadly_error'] = _('It is NOT possible to update your registration anymore. Please <a href="/contact">contact</a> the local committee for more information.');  
		elseif(!empty($this->input['edit_link'])):

			// Retrieve Participant Info based on edit_link
						
			
		
		else:
			$this->input['deadly_error'] = _('It is NOT possible to update your registration anymore. Please <a href="/contact">contact</a> the local committee for more information.');  
		endif;
		
		
		$content->input = $this->input;
		
		$this->template->header = new View('/shared/header.html');
        $this->template->header->no_row = false;
        $this->template->header->slider = false;
        $this->template->header->active = "index";
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html');
        $this->template->footer->no_row = true;
    }
     
}
