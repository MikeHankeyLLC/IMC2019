<?php

class JSON_Response
{
    private $status;
    private $input;
    private $result;
    private $errors;

    public function __construct()
    {
        $this->status = false;
        $this->input  = array();
        $this->result = array();
        $this->errors = array();
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value = null)
    {
        $this->$name = $value;
    }

    public function get_response()
    {
        $this->status = ($this->status == true) ? 1 : 0;
        return json_encode(get_object_vars($this));
    }

    public function print_response($exit = true)
    {
        header('Content-Type: application/json');
        print $this->get_response();
        if ( $exit ) exit;
    }
}
