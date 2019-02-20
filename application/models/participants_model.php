<?php

class Participants_Model {
 	
    // Build members binds
	static private function members_binds($binds) {
        $where  = array();
        $return = array();
        
        
        if(!empty($binds['IP'])) {
            $where[] = "imc_participants.IP = '". $binds['IP'] ."'"; 
        } 
        
        if(!empty($binds['user_id'])) {
            $where[] = "imc_participants.id = '". $binds['user_id'] ."'"; 
        } 
         
        if(!empty($binds['title'])) {
            $where[] = "imc_participants.title = '". $binds['title'] ."'"; 
        }  
        
        if(!empty($binds['confirmed'])) {
            $where[] = "imc_participants.confirmed = '". $binds['confirmed'] ."'"; 
        }  
          
        if(!empty($binds['firstname'])) {
            $where[] = "imc_participants.firstname LIKE '%" . $binds['firstname'] ."%'"; 
        }
        
        if(!empty($binds['lastname'])) {
            $where[] = "imc_participants.lastname LIKE '%" . $binds['lastname'] ."%'"; 
        }
        
        if(!empty($binds['proceedings'])) {
            $where[] = "imc_participants.proceedings = 'print'"; 
        }
        
        if(!empty($binds['address'])) {
            $where[] = "imc_participants.address LIKE '%" . $binds['address'] ."%'"; 
        }
        
        if(!empty($binds['dob'])) {
            $where[] = "imc_participants.dob = '" . $binds['dob'] ."'"; 
        }
        
        if(!empty($binds['email'])) {
            $where[] = "imc_participants.email LIKE '%" . $binds['email'] ."%'"; 
        }
        
        if(!empty($binds['country'])) {
            $where[] = "imc_participants.country =". $binds['country']; 
        }
        
        if(!empty($binds['org'])) {
            $where[] = "imc_participants.org LIKE '%" . $binds['org'] ."%'"; 
        }
        
        if(!empty($binds['meteoroid_shuttle'])) {
            $where[] = "imc_participants.meteoroid_shuttle != -1";   
        }
        
        if(!empty($binds['paid'])) {
            $where[] = "reg.paid =" . $binds['paid']; 
        }
        
        if(!empty($binds['reg_type'])) {
            $where[] = "imc_details.reg_type = '" . $binds['reg_type'] ."'"; 
        }
		
		if(!empty($binds['edit_link'])) {
            $where[] = "imc_participants.edit_link = '" . $binds['edit_link'] ."'"; 
        }
         
        if (!empty($where)) {
            $return['where'] = 'WHERE ' . implode(' AND ', $where);
        } else {
            $return['where'] = '';
        }
		
		if(!empty($binds['start']) && !empty($binds['end'])) {
           $return['limit'] = "LIMIT :start,:end";
        } else if(!empty($binds['end'])) {
           $return['limit'] = "LIMIT :end";
        } else {
           $return['limit'] = "";	
        }
 		
        return $return;
	}
 
 
    /*
    * Get Participants
    */
    public static function count_participants($binds) {
        $db = new DB;
 		$bb = self::members_binds($binds);
		$where = $bb['where'];
		$limit = $bb['limit'];

        $sql = "SELECT count(*) as res
                FROM imc_participants
                INNER JOIN country_codes     AS c   ON imc_participants.country = c.country_id
                INNER JOIN imc_registration  AS reg ON imc_participants.id = reg.user_id
                INNER JOIN imc_details ON imc_participants.id = imc_details.user_id
                $where
				$limit
        ";
        
        $res = $db->select($sql, $binds);
        
        if(!empty($res)) {
			return $res[0]['res'];
		} else {
			return 0;	
		}  
    }
    
    /*
    * DELETE PARTICIPANTS
    */
    public static function delete_participants($binds) {
         $db = new DB;
         
         // Delete from imc_participants
         $sql = "DELETE FROM  imc_participants
                WHERE id = :user_id;
        ";
        
         $db->query($sql, $binds);
         
         // Delete from imc_registration
         $sql = "DELETE FROM imc_registration
                 WHERE user_id = :user_id;
        ";
        $db->query($sql, $binds);
        
        // Delete from imc_details
        $sql = "DELETE FROM imc_details
                 WHERE user_id = :user_id;
        ";
        $db->query($sql, $binds);
        
        // Delete from imc_details
        $sql = "DELETE FROM imc_details
                 WHERE user_id = :user_id;
        ";
        $db->query($sql, $binds);
        
        // Delete from imc_posters
        $sql = "DELETE FROM imc_posters
                 WHERE user_id = :user_id;
        ";
        $db->query($sql, $binds);
        
        // Delete from imc_talks
        $sql = "DELETE FROM imc_talks
                 WHERE user_id = :user_id;
        ";
        $db->query($sql, $binds);
        
        // Delete from imc_travels
        $sql = "DELETE FROM imc_travels
                 WHERE user_id = :user_id;
        ";
        $db->query($sql, $binds);
        
        
    }
    
    
    /**
    * Update Participant
    */
    public static function update_participants($binds) {
	 	
        
        $sql = "UPDATE  imc_participants
                SET  
                  title           = :title,
                  firstname       = :firstname,
                  lastname        = :lastname,
                  gender          = :gender,
                  address         = :address,
                  city            = :city,
                  country         = :country,
                  post_code       = :post_code,
                  email           = :email,
                  dob             = :dob,
                  phone           = :phone,
                  org             = :org";
        
        if(!empty($binds['confirmed'])):
            $sql .= ", confirmed       = :confirmed";
        endif;
                   
         $sql .= " WHERE  id = :user_id";
        
        $res = DBF::set($sql, $binds);
        
      
        return $res;
    }
 
 
    /*
    * Get Participants all info (for participant list)
    */
    public static function get_participants($binds) {
        $db = new DB;
 		$bb = self::members_binds($binds);
		$where = $bb['where'];
		$limit = $bb['limit'];

        $sql = "SELECT  imc_participants.*, 
                        c.short_name AS country_name, 
                        c.iso2 AS country_code,
                        reg.year, 
                        reg.payment_meth,
                        reg.paid,
                        reg.pay_date,
                        reg.amount,
                        reg.amount_due,
                        reg.amount_due_paypal,
                        reg.currency,
                        reg.roomnumber,
                        reg.reg_date,
                        reg.email_sent,
                        reg.admin_comment,
                        imc_details.*,
                        imc_emails_sent.*
                FROM imc_participants
                INNER JOIN country_codes     AS c   ON imc_participants.country = c.country_id
                INNER JOIN imc_registration  AS reg ON imc_participants.id = reg.user_id
                INNER JOIN imc_details ON imc_participants.id = imc_details.user_id
                INNER JOIN imc_emails_sent ON imc_emails_sent.user_id = reg.user_id
                $where
                ORDER BY imc_participants.id DESC
				$limit
                
        ";
		
        $res = $db->select($sql, $binds);
	 
        
        return $res;
    }
    
    
    /*
    * Get Participants all info from edit_link
    */
    public static function get_participant_from_edit_link($binds) {
        $db = new DB;
 	    $sql = "SELECT imc_participants.*, 
                        c.short_name AS country_name, 
                        reg.year, 
                        reg.payment_meth,
                        reg.paid,
                        reg.pay_date,
                        reg.amount,
                        reg.amount_due,
                        reg.currency,
                        reg.reg_date 
                FROM imc_participants
                INNER JOIN country_codes     AS c   ON imc_participants.country = c.country_id
                INNER JOIN imc_registration  AS reg ON imc_participants.id = reg.user_id
                WHERE imc_participants.edit_link = :edit_link
        ";
        $res = $db->select($sql, $binds);
        return $res;
    }
    
  
          
    /*
     * Add new participant
     * return the participant id
     */
    public static function add_participants($binds) {
 
           
          $sql = "INSERT INTO imc_participants
                    SET IP              = :ip,
                        title           = :title,
                        firstname       = :firstname,
                        lastname        = :lastname,
                        gender          = :gender,
                        address         = :address,
                        city            = :city,
                        country         = :country,
                        post_code       = :post_code,
                        email           = :email,
                        dob             = :dob,
                        confirmed       = -1,
                        phone           = :phone,
                        org             = :org,
                        edit_link       = :edit_link
           ";
       
          // Underage GPDR
          if(!empty($binds['firstname_underage']) && 
             !empty($binds['lastname_underage']) &&
             !empty($binds['email_underage'])  
          ):
              $sql .= ", grdp = '" . $binds['firstname_underage'] . ' ' . $binds['lastname_underage'] . ' ' . $binds['email_underage'] .  "'";
          endif;
 
          $res = DBF::set($sql, $binds);
            
          
          $db2 = new DB;
          $sql2 = "INSERT INTO imc_emails_sent
                    SET user_id          = $res,
                        last_email_date  = now(),
                        num_of_emails    = 0
          ";
          $db2->query($sql2,array());
          
          return $res;
    }

    
    
    /*
     *  Get confirmed participants
     */
    public static function get_confirmed_participants() {
        $db = new DB;  
        $sql= " SELECT imc_participants.title,
                       imc_participants.firstname,
                       imc_participants.lastname,
                       imc_participants.org,
                       country_codes.short_name AS country_name,
                       country_codes.iso2,
                       imc_details.meteoroid_shuttle
                FROM   imc_participants
                INNER JOIN country_codes ON imc_participants.country = country_codes.country_id
                INNER JOIN imc_details on imc_details.user_id = imc_participants.id
                WHERE  imc_participants.confirmed > 0
                ORDER BY country_codes.short_name, imc_participants.lastname
        ";
        return $db->select($sql);
    }
   
    /*
     * Test edit link code
    */
     public static function count_edit_link($binds) {
          $db = new DB;  
          $sql = "SELECT  count(edit_link) as count
                    FROM  imc_participants
                    WHERE edit_link = :personal_code";
          $res = $db->select($sql, $binds); 
          return $res[0]['count'];
     }
     
     /*
     * Test edit_link & email
     */
     public static function test_edit_link($binds) {
           $sql = "SELECT id
                   FROM    imc_participants
                   WHERE edit_link = :edit_link
                   AND   email     = :email";
           
           $res = DBF::query($sql,$binds,'num');
         
           if(!empty($res) && !empty($res[0])):
            return $res[0]['id'];
           else:
            return false;
           endif;
     }
     
         
    
    
    
}