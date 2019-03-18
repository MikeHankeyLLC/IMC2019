<?php

class Admins_Model {
    
    /*
     * Test if is admin
     */
     public static function is_admin_user($binds) {
         
        $sql = "SELECT  *
                FROM    admin_users  
                WHERE   email  = :email
                AND     active = 1
         ";
         
        
        $res = DBF::query($sql, $binds,'num');
 
        
        // Test the password
        if(!empty($res)): 

            if(strcmp( Crypt::decrypt($res[0]['_pwd']), $binds['pwd'])  == 0):
              return true;
            else:       
              return false;
            endif;
 
        else:

            return false;
        endif;

     }
    
}
 