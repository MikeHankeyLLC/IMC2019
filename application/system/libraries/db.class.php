<?php

class DB {
    public $db;
    public $affected_rows;

    public function __construct() {
        if (!Registry::get('both')) {
            $db = new Database('both');
            Registry::set('both', $db);
            unset($db);
        }

        $this->db = Registry::get('both');
    }
   
    public function query($sql, $binds = array()) {
        $sql = self::binder($sql, $binds);

        $result = $this->db->query($sql);
        $this->affected_rows = $this->db->affected_rows;
        $this->insert_id     = $this->db->insert_id;
        $this->error         = $this->db->error;

        return $result;
    }
 
    public function select($sql, $binds = array()) {
        $sql = self::binder($sql, $binds);

        $result = $this->db->query($sql);

        $data = array();
        if (is_object($result)) {
            while($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }

        return $data;
    }

     public function fetch($sql, $binds = array()) {
        $sql = self::binder($sql, $binds);

        $result = $this->db->query($sql);

		return  mysqli_fetch_assoc($result);
    }

    public static function close() {
        if ($db = Registry::get('both')) {
            $thread_id = $db->thread_id;
            
            $db->kill($thread_id);
        }
    }

    public function binder($sql, $binds) {
        //run the sql insertion scanner
        self::scan($sql, $binds);

        if (!empty($binds)) {
            krsort($binds);
            foreach ($binds as $key => $val) {
                $val = self::esc($val);
                if (is_numeric($val)) {
                    $sql = str_replace(":$key", "$val", $sql);
                } else {
                    $val = strip_tags($val);
                    $sql = str_replace(":$key", "'$val'", $sql);
                }
            }
        }

        return $sql;
    }

    public function scan($sql, $binds) {
        /*
        include_once(ROOT_DIR . "/application/helpers/sqlInsertionScanner.class.php");
        $s = new sqlInsertionScanner();
        if($s->scanQS()) {
            $log_text .= "From database class\n";
            $log_text .= "**QUERY**\n{$sql}\n**BINDS**\n";
            foreach($binds as $key => $value) {
                $log_text .= "{$key} => {$value}\n";
            }
            $log_text .= $_SERVER['REMOTE_ADDR'] . "\n" . $s->explainQS();

            $subject = "AMS " . DEV_OR_PROD . " - SQL Insertion Alert";

            Mail::send(DEVELOPER_EMAIL_ADDRESSES, "AMS Developer Alert <developer-alert@amsmeteors.org>",
                       $subject, $log_text);

        }
        */
    }

    public function esc($string) {
        $clean = mysqli_real_escape_string($this->db, $string);
        return $clean;
    }
} // End mysqli wrapper
