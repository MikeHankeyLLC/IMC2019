<?php

class Registrations_Model {
 
       
    /*
     * Add new registration (not paid yet)
     */
    public static function add_registration($binds) {
          $db = new DB;  
          $sql = "INSERT INTO imc_registration
                    SET year                = :year,
                        amount_due          = :amount_due,
                        amount_due_paypal   = :amount_due_paypal,
                        payment_meth        = :payment_meth,
                        paid                = -1,
                        user_id             = :user_id, 
                        reg_date            = '".date('Y-m-d')."'
           ";
          //  payment_meth    = :payment_meth,
          $db->query($sql, $binds);
         return  $db->insert_id;
    }
    
    /*
    * Update Registration
    */
   public static function update_registration($binds) {
        $db = new DB;
        $sql = "UPDATE imc_registration
                SET year                    = :year,
                    amount                  = :amount,
                    amount_due              = :amount_due,
                    amount_due_paypal       = :amount_due_paypal,
                    payment_meth            = :payment_meth,
                    paid                    = :paid,
                    user_id                 = :user_id,
                    pay_date                = :pay_date, 
                    roomnumber              = :roomnumber,
                    admin_comment           = :admin_comment        
                WHERE  user_id = :user_id";
        $res = $db->query($sql, $binds);
        return $res; 
   }    
   
   /*
    * Confirmation Payment sent
   */
    public static function payment_confirmation_email_sent($binds) {
        $db = new DB;
        $sql = "UPDATE imc_registration
                SET  email_sent = 1
                WHERE  user_id = :user_id";
        $res = $db->query($sql, $binds);
        return $res; 
    
    }
}