<?php

class Posters_Model {
     
	// Build posters binds
	static private function posters_binds($binds) {
        $where  = array();
        $return = array();
          
           
        if(!empty($binds['user_id'])) {
            $where[] = "imc_participants.id = '". $binds['user_id'] ."'"; 
        } 
        
        if(!empty($binds['id'])) {
            $where[] = "imc_posters.id = '". $binds['id'] ."'"; 
        } 
         
        if(!empty($binds['lastname'])) {
            $where[] = "imc_posters.authors LIKE '%" . $binds['lastname'] ."%'"; 
        }
        
        if(!empty($binds['first_editing'])) {
            $where[] = "imc_posters.first_editing = " . $binds['first_editing']; 
        }
        
        if(!empty($binds['final_editing'])) {
            $where[] = "imc_posters.final_editing = " . $binds['final_editing']; 
        }     
        
        if(!empty($binds['session'])) {
            $where[] = "imc_posters.session = " . $binds['session']; 
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
    
    
    
    
    /**
    * Get posters from user
    */
    public function get_posters_from_users($binds) {
        $db = new DB;
 		$bb = self::posters_binds($binds);
		$limit = $bb['limit'];
        
		$sql = " SELECT imc_posters.* , part.lastname, part.firstname
                 FROM imc_posters
                 INNER JOIN imc_participants AS part ON part.id = imc_posters.user_id
                 WHERE imc_posters.user_id = :user_id
				 $limit";
  		$res = $db->select($sql, $binds);
        return $res;   
    }
    
    
    /*
    * Count Posters
    */
    public function count_posters($binds) {
        $db = new DB;
 		$bb = self::posters_binds($binds);
		$where = "INNER JOIN imc_participants AS part ON part.id = imc_posters.user_id " . $bb['where'];
		$limit = $bb['limit'];

        $sql = " SELECT count(*) as res
                 FROM imc_posters
                 $where
				 $limit";
        $res = $db->select($sql, $binds);
        
        if(!empty($res)) {
			return $res[0]['res'];
		} else {
			return 0;	
		}  
    }
       
    /*
     * Add new paper
     */
    public function add_poster($binds) {
          $db = new DB;  
          $sql = "INSERT INTO imc_posters
                    SET year         = :year,
                        title        = :title, 
                        abstract     = :abstract,
                        authors      = :authors,
                        session      = :session,
                        delivery_date = :delivery_date,
                        user_id      = :user_id
           ";
          $db->query($sql, $binds);
          //echo $db->binder($sql, $binds);
          return  $db->insert_id;
    }
    
    
    /**
    * Get posters with authors
    */
    public function get_all_posters_with_authors() {
         $db = new DB;
         $sql = "SELECT imc_posters.* , part.lastname, part.firstname, part.email
                    FROM imc_posters
                INNER JOIN imc_participants AS part ON part.id = imc_posters.user_id";
        return $db->select($sql);   
    }


    /**
    * Update poster
    */
    public function update_poster($binds) {
        $db = new DB;
        
        $sql = "UPDATE imc_posters
                SET  
                  title         = :title,
                  abstract      = :abstract,
                  authors       = :authors,
                  delivery_date = :delivery_date,
                  session       = :session,
                  first_editing = :first_editing,
                  final_editing = :final_editing
                WHERE  id       = :poster_id";
 
        $res = $db->query($sql, $binds);
        return $res;
    }
    
    
    /**
    * Get posters
    */
    public function get_posters($binds) {
		$db = new DB;
 		$bb = self::posters_binds($binds);
		$where = "INNER JOIN imc_participants AS part ON part.id = imc_posters.user_id \n" . $bb['where'];
		$limit = $bb['limit'];
 		 
         $sql = "SELECT imc_posters.*, part.lastname, part.firstname 
                 FROM imc_posters 
                 $where
				 $limit";
                 
        //echo $db->binder($sql, $binds);
  		$res = $db->select($sql, $binds);
  		return $res;   
    }
 
    
    
    /**
    * Remove all talks from a user
    */
    public function remove_posters($binds) {
        $db = new DB;
 		 
         $sql = "DELETE FROM 
                imc_posters
                 WHERE id = :poster_id
        ";
        
         $res = $db->query($sql, $binds);
        return $res;   
    }
    
    public function remove_posters_from_user($binds) {
        $db = new DB;
 		 
         $sql = "DELETE FROM 
                 imc_posters
                 WHERE user_id = :user_id
        ";
          $res = $db->query($sql, $binds);
        return $res;   
    }
 
    
    
    /**
    * Get All confirmed posters
    */
    public function get_all_confirmed_posters() {
         $db = new DB;
         $sql = "SELECT imc_posters . * , part.lastname, part.firstname
            FROM imc_posters
            INNER JOIN imc_participants AS part ON part.id = imc_posters.user_id
            WHERE part.confirmed =1
            GROUP BY imc_posters.session
            ORDER BY imc_posters.id";
         $res = $db->select($sql);
        return $res;  
    }
    
}