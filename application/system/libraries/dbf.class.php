<?php

class DBF {
    
    static public $db;
    static public $error;
    static public $insert_id;
 
    final static private function connect_db($which_db) {
        self::$db = new Database($which_db);
    }
    
    final static function set($sql, $binds = array(), $which_db=null) {
       
        // Default DB: both
        if(!empty(self::$db) && (self::$db->dbname=='ams_org' || self::$db->dbname=='db338761497') && empty($which_db)):
            $which_db = 'both';
        endif;
        
        // Connect to the db if its not already
        if (empty(self::$db) || !empty($which_db)) {
            self::connect_db($which_db);
        }
        
        // Replace the binds
        $sql    = self::binder($sql, $binds);
        $result = self::$db->query($sql); 
 	 
        // Return the last insert ID 
        return self::$db->insert_id;
    }

    final static public function query($sql, $binds = array(), $return_type = 'none', $which_db=null) {
        
         // Default DB: both
        if(!empty(self::$db) && (self::$db->dbname=='ams_org'  || self::$db->dbname=='db338761497') && empty($which_db)):
            $which_db = 'both';
        endif;
           
        // Connect to the db if its not already
        if (empty(self::$db) || !empty($which_db)) {
            self::connect_db($which_db);
        }
   
        // Replace the binds
        $sql    = self::binder($sql, $binds);
        $result = self::$db->query($sql);

        if (!empty(self::$db->error) && defined('MYSQL_DEBUG')) {
            pp('<h1>' . self::$db->error . '<h1>');
            pp($sql);
            exit;
        }

        $return = $result;
       
        
        switch ($return_type) {
            case 'assoc':
                $return = mysqli_fetch_assoc($result);
                break;

            case 'num': 
                $data = array();
                if (is_object($result)) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $data[] = $row;
                    }
                }
                $return = $data;
                break;

            case 'none':
            default:
            break;
        } 

        self::$insert_id = self::$db->insert_id;
        self::$error     = self::$db->error;

        return $return;
    }

    final static public function close() {
        if (self::$db) {
            $thread_id = self::$db->thread_id;

            self::$db->kill($thread_id);
        }
    }

    final static public function debug($sql, $binds = array(),$which_db=null) {
        // Connect to the db if its not already
        if (empty(self::$db)) {
            self::connect_db($which_db);
        }

        // Replace the binds
        $sql = self::binder($sql, $binds);

        pp($sql);
        //exit;
    }

    final static private function binder($sql, $binds) {
        if (!empty($binds)) {
            krsort($binds);
             
            foreach ($binds as $key => $val) {
				
				
                // In case we have arrays inside arrays
                if(!is_array($val)): 
					 
   					$val = mysqli_real_escape_string(self::$db, $val);
 					
                     if (is_numeric($val) && substr(strval("$val"), 0, 1) !== '0') {
                        $sql = str_replace(":$key", "$val", $sql);
                    } else {
                        $sql = str_replace(":$key", "'$val'", $sql);
                    }
                endif;
                 
            }
        }
		 
 
        return $sql;
    }
}
