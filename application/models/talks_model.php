<?php

class Talks_Model {
    
    
	// Build talks binds
	static private function talks_binds($binds) {
        $where  = array();
        $return = array();
        
         
        if(!empty($binds['user_id'])) {
            $where[] = "part.id = '". $binds['user_id'] ."'"; 
        } 
         
        if(!empty($binds['lastname'])) {
            $where[] = "imc_talks.authors LIKE '%" . $binds['lastname'] ."%'"; 
        }
        
        if(!empty($binds['session'])) {
            $where[] = "imc_talks.session = " . $binds['session']; 
        }
        
        if(!empty($binds['id'])) {
            $where[] = "imc_talks.id = '" . $binds['id']."'"; 
        }
        
        if(!empty($binds['first_editing'])) {
            $where[] = "imc_talks.first_editing = " . $binds['first_editing']; 
        }
        
        if(!empty($binds['final_editing'])) {
            $where[] = "imc_talks.final_editing = " . $binds['final_editing']; 
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
     * Add new talk
     */
    public function add_talk($binds) {
          $db = new DB;  
          $sql = "INSERT INTO imc_talks
                    SET year          = :year,
                        title         = :title,
                        abstract      = :abstract,
                        authors       = :authors,
                        session       = :session,
                        duration      = :duration,
                        delivery_date = :delivery_date,
                        user_id       = :user_id
           ";
           $db->query($sql, $binds);
          return  $db->insert_id;
    }
    
    
    /**
    * Get talks  
    */
    public function get_talks($binds) {
        $db = new DB;
 		$bb = self::talks_binds($binds);
		$where = "INNER JOIN imc_participants AS part ON part.id = imc_talks.user_id " . $bb['where'];
		$limit = $bb['limit'];
        
		$sql = " SELECT imc_talks.* , part.lastname, part.firstname
                 FROM imc_talks
                 $where
				 $limit";
  		$res = $db->select($sql, $binds);
        return $res;   
    }
    
    
    /**
    * Get talks from user
    */
    public function get_talks_from_users($binds) {
        $db = new DB;
 		$bb = self::talks_binds($binds);
		$limit = $bb['limit'];
        
		$sql = " SELECT imc_talks.* , part.lastname, part.firstname
                 FROM imc_talks
                 INNER JOIN imc_participants AS part ON part.id = imc_talks.user_id
                 WHERE imc_talks.user_id = :user_id
				 $limit";
  		$res = $db->select($sql, $binds);
        return $res;   
    }
    
    /*
    * Count Talks
    */
    public function count_talks($binds) {
        $db = new DB;
 		$bb = self::talks_binds($binds);
		$where = "INNER JOIN imc_participants AS part ON part.id = imc_talks.user_id " . $bb['where'];
		$limit = $bb['limit'];

        $sql = " SELECT count(*) as res
                 FROM imc_talks
                 $where
				 $limit";
        $res = $db->select($sql, $binds);
        
        if(!empty($res)) {
			return $res[0]['res'];
		} else {
			return 0;	
		}  
    }
    
    
    /**
    * Get talks with authors
    */
    public function get_all_talks_with_authors() {
         $db = new DB;
         $sql = "SELECT imc_talks.* , part.lastname, part.firstname, part.email
                   FROM imc_talks
                INNER JOIN imc_participants AS part ON part.id = imc_talks.user_id";
        return $db->select($sql);   
    }
    

    /**
    * Update talk
    */
    public function update_talk($binds) {
        $db = new DB;
        
        $sql = "UPDATE imc_talks
                SET  
                  title         = :title,
                  abstract      = :abstract,
                  authors       = :authors,
                  duration      = :duration,
                  session       = :session,
                  delivery_date = :delivery_date,
                  first_editing = :first_editing,
                  final_editing = :final_editing
                WHERE  id = :talk_id";
        
        $res = $db->query($sql, $binds);
        return $res;
    }

 
      
    /**
    * Get talks
    */
    public function get_all_talks() {
        $db = new DB;
 		 
         $sql = "SELECT *
                 FROM imc_talks
                 ORDER BY user_id
        ";
        
        $res = $db->select($sql);
        return $res;   
    }
    
    
    /**
    * Get All confirmed talks
    */
    public function get_all_confirmed_talks() {
         $db = new DB;
         $sql = "SELECT imc_talks . * , part.lastname, part.firstname
            FROM imc_talks
            INNER JOIN imc_participants AS part ON part.id = imc_talks.user_id
            WHERE part.confirmed =1
            GROUP BY imc_talks.session
            ORDER BY imc_talks.id";
         $res = $db->select($sql);
        return $res;  
    }
    
    /**
    * Remove all talks from a user
    */
    public function remove_talks($binds) {
        $db = new DB;
 		 
         $sql = "DELETE FROM 
                 imc_talks
                 WHERE id = :talk_id
        ";
        
         $res = $db->query($sql, $binds);
        return $res;   
    }
    
    
    public function remove_talks_from_user($binds) {
        $db = new DB;
 		 
         $sql = "DELETE FROM 
                 imc_talks
                 WHERE user_id = :user_id
        ";
        
         $res = $db->query($sql, $binds);
        return $res;   
    }
    
    
}