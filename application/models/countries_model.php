<?php

class Countries_Model {

	/**
    * Get full list of countries 
    * (for drop downs)
    */
    public static function get_countries() {
		$db = new DB;
 		
		$sql = "SELECT iso2, short_name, country_id
				FROM country_codes
		";
	
        return $db->select($sql);
      
    }
    
    /**
    * Get country short name
    */
    public static function get_country_name($binds) {
        $db = new DB;
 		
		$sql = "SELECT short_name 
				FROM country_codes
                WHERE country_id = :country_id
		";
	
        $res = $db->select($sql,$binds);
        return $res[0]['short_name'];
    }

		
}

?>