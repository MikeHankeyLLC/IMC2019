<?php

class Emails_Model {
    /*
    * Get emails for given user
    */
    public function get_emails($binds) {
 		$db = new DB;
 		
        $sql = "SELECT imc_emails_sent.*, imc_participants.email
                FROM imc_emails_sent
                INNER JOIN imc_participants ON imc_participants.id = imc_emails_sent.user_id
                WHERE imc_emails_sent.user_id = :user_id
        ";
        
        $res = $db->select($sql, $binds);
        return $res;   
    }
    
    /*
    * Add email for given user 
    */
    public function add_email($binds) {
         $db = new DB;
         $sql = "UPDATE imc_emails_sent
                   SET  last_email_date  = now(),
                        num_of_emails    = num_of_emails + 1
                 WHERE    user_id = :user_id;       
         ";
         $db->query($sql, $binds);
    }
}