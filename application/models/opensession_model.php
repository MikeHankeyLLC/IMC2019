<?php

class Opensession_Model {
    
           
    /*
     * Add a question
     */
    public function add_question($binds) {
          $sql = "INSERT INTO imc_opensession
                    SET name            = :name,
                        email           = :email,
                        question        = :question 
           ";
            
           DBF::set($sql,$binds);
   }


  /*
  * Delete a question
  */
  public function delete_question($binds) {
          $sql = "DELETE FROM imc_opensession
                    WHERE question_id = :question_id;
           ";
           
           DBF::query($sql,$binds);
   }     
   
   
  /*
  * Get All questions
  */
  public function get_questions($binds) {
          $sql = "SELECT * FROM imc_opensession;";
           
           //DBF::debug($sql,$binds,'num');
           return DBF::query($sql,$binds,'num');
   }   
}