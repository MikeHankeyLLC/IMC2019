<?php

class Payment_Confirm_Model {
    
     /*
     * Add new confirm model
     */
    public function add_paymentEmail($binds) {
          $db = new DB;  
          $date = date("Y-m-d H:i:s");
          $sql = "INSERT INTO imc_payment_confirm
                    SET user_id     = :user_id,
                        email_date  = '".$date."',
                        amount    = :amount
           ";
   
           
          $db->query($sql, $binds);
          return array('d_id'=>$db->insert_id, 'email_date'=>$date, 'amount'=>$binds['amount']) ;
    }   
 
 
  /*
  * Get confirm email from a given user
  */
  public function get_paymentEmail($binds) {
        $db = new DB;  
        $sql = "SELECT * 
                FROM imc_payment_confirm
                WHERE user_id = :user_id";
        return $db->select($sql, $binds);     
  }
  
    
}