<?php

class Travels_Model {
    
    // Build travels binds
	static private function travels_binds($binds) {
        $where  = array();
        $return = array();
       
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
    * Get travel details for given users
    */
    public static function get_travels($binds) {
 		$db = new DB;
 		$bb = self::travels_binds($binds);
		$where = $bb['where'];
		$limit = $bb['limit'];

        $sql = "SELECT *
                FROM imc_travels
                $where
				$limit
        ";
        
        $res = $db->select($sql, $binds);
        return $res;   
    }
    
       
       
    /*
     * Add new travel info 
     */
    public static function add_travelInfo($binds) {
          $db = new DB;  
          $sql = "INSERT INTO imc_travels
                    SET travel_type     = :travel_type,
                        arrival_date    = :arrival_date,
                        arrival_time    = :arrival_time,
                        departure_date  = :departure_date,
                        departure_time  = :departure_time,
                        details         = :details,
                        user_id         = :user_id
           ";
          $db->query($sql, $binds);
          return $db->insert_id;
    }
    
    
    /**
     * UDPATE travel info
     */
   public static function update_travelInfo($binds) {
      $db = new DB;
        $sql = "UPDATE imc_travels
                SET  travel_type  =  :travel_type,
                   arrival_date = :arrival_date,
                  arrival_time = :arrival_time,
                  departure_date = :departure_date,
                  departure_time = :departure_time,
                  details = :details
                WHERE  user_id = :user_id";
        $res = $db->query($sql, $binds);
        return $res;
   }
}