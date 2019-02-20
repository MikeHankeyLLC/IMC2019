<?php

class Details_Model {
    
    // Build details binds
	static private function details_binds($binds) {
        $where  = array();
        $return = array();
        
        if(!empty($binds['tshirt'])) {
            $where[] = "tshirt = '". $binds['tshirt'] ."'"; 
        }  
   
        if(!empty($binds['food'])) {
            $where[] = "food = '". $binds['food'] ."'"; 
        } 
        
        if(!empty($binds['proceedings'])) {
            $where[] = "proceedings = '". $binds['proceedings'] ."'"; 
        }  
        
         if(!empty($binds['user_id'])) {
            $where[] = "user_id = '". $binds['user_id'] ."'"; 
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
    * Get details for given users
    */
    public static function get_details($binds) {
 		$db = new DB;
 		$bb = self::details_binds($binds);
		$where = $bb['where'];
		$limit = $bb['limit'];

        $sql = "SELECT *
                FROM imc_details
                $where
				$limit
        ";
        
      
        $res = $db->select($sql, $binds);
        return $res;   
    }
 
       
    /*
     * Add new details
     */
    public static function add_details($binds) {
          $db = new DB;  
          $sql = "INSERT INTO imc_details
                    SET tshirt              = :tshirt,
                        food                = :food,
                        food_other          = :food_other,
                        roomate             = :roomate,
                        reg_type            = :reg_type,
                        comments            = :comments,
                        proceedings         = :proceedings,
                        user_id             = :user_id
           ";
           
          if(!empty($binds['workshop1'])):
            $sql .= ", workshop1=1";
          endif;
            
          if(!empty($binds['workshop2'])):
            $sql .= ", workshop2=1";
          endif;
          
          $db->query($sql, $binds);
          return  $db->insert_id;
    }
    
    
    
    /*
    * Update details
    */
    public static function update_details($binds) {
        $db = new DB;
        // meteoroid_shuttle    = :meteoroid_shuttle,
        $sql = "UPDATE imc_details
                SET tshirt              = :tshirt,
                   food                 = :food,
                   food_other           = :food_other,
                   roomate              = :roomate,
                   reg_type             = :reg_type,
                   comments             = :comments,
                   proceedings          = :proceedings,
                   user_id              = :user_id
      ";
      
      if(!empty($binds['workshop1'])):
            $sql .= ", workshop1=1";
      else: 
            $sql .= ", workshop1=0";   
      endif;
            
      if(!empty($binds['workshop2'])):
            $sql .= ", workshop2=1";
      else: 
            $sql .= ", workshop2=0";   
      endif;            
                   
      $sql .= " WHERE  user_id = :user_id";
        
      $res = $db->query($sql, $binds);
     
      return $res;   
        
    }
   
    
}