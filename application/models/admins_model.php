<?php

class Admins_Model {
    
    /*
     * Test if is admin
     */
     public static function is_admin_user($binds) {
         
        $sql = "SELECT count(*)  as c
                FROM    admin_users  
                WHERE   email  = :email
                AND     pwd = :pwd   
                AND     active = 1
         ";
         
        
         $res = DBF::query($sql, $binds,'num');
           
         if(!empty($res) && !empty($res[0]['c'])):
            return true;
         else:
            return false;
         endif;
     }
    
}
 