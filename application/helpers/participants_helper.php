<?php

class Participants_Helper {

 	/**
	* Remove old wrong data from ams_report
	*/
    public static function get_email_table($input) {
        
          $payment_methods =  unserialize(PAYMENT_METHODS);
          $food_req        =  unserialize(FOOD_REQUIREMENTS);        
          $proceed         =  unserialize(PROCEEDINGS);
          $acc             =  unserialize(ACCOMODATIONS);
          $country         = Countries_model::get_country_name(array('country_id'=>$input['country']));
          
          $message  = '<hr/>';
          $message .= '<b>Participant ID</b>: '.$input['user_id'].'<br/>';
          $message .= '<hr/>';
          
          $gender = ($input['gender']=='M')?'Male':(($input['gender']=='F')?'Female':'Other');
          
          $message .= "<table border='0' cellpadding='5' cellspacing='0' width='100%' id='emailBody'> <tr> <td align='left' valign='top'><b>Name</b></td> <td align='left' valign='top'>".$input['title']." ".$input['firstname']." " . $input['lastname'] . "</td> </tr> <tr> <td align='left' valign='top'><b>Email</b></td> <td align='left' valign='top'>".$input['email']."</td> </tr> <tr> <td align='left' valign='top'><b>Gender</b></td> <td align='left' valign='top'>". $gender."</td> </tr> <tr> <td align='left' valign='top'><b>Organization</b></td> <td align='left' valign='top'>".(!empty($input['org'])?$input['org']:'-')."</td> </tr> <tr> <td align='left' valign='top'><b>Phone</b></td> <td align='left' valign='top'>".$input['phone']."</td> </tr> <tr> <td align='left' valign='top'><b>Address</b></td> <td align='left' valign='top'>".$input['address']."</td> </tr> <tr> <td align='left' valign='top'><b>City</b></td> <td align='left' valign='top'>".$input['city']."</td> </tr> <tr> <td align='left' valign='top'><b>Postal Code</b></td> <td align='left' valign='top'>".$input['post_code']."</td> </tr> <tr> <td align='left' valign='top'><b>Country</b></td> <td align='left' valign='top'>".$country."</td> </tr> <tr> <td align='left' valign='top'><b>Date of birth</b></td> <td align='left' valign='top'>".$input['dob']."</td> </tr> <tr> <td align='left' valign='top' colspan='2'><br><b>TRAVEL DETAILS</b><hr/></td> </tr> <tr> <td align='left' valign='top'><b>Will travel by</b></td> <td align='left' valign='top'>".$input['travel_type']."</td> </tr> <tr> <td align='left' valign='top'><b>Arrival</b></td> <td align='left' valign='top'>".$input['arrival_date'].' at ' . $input['arrival_time']."</td> </tr> <tr> <td align='left' valign='top'><b>Departure</b></td> <td align='left' valign='top'>".$input['departure_date'].' at ' . $input['departure_time']."</td> </tr> <tr> <td align='left' valign='top'><b>Travel Details</b></td> <td align='left' valign='top'>".(!empty($input['details'])?$input['details']:'-')."</td> </tr>";
                                    
          
          if(!empty($input['talk']) || !empty($input['poster'])):  
            $message .= "<tr><td align='left' valign='top' colspan='2'><br><b>CONTRIBUTIONS</b><hr/></td></tr>";
          endif;
          
          if(!empty($input['talk'])): 
            $message .= "<tr><td align='left' valign='top' colspan='2'><br><b>Talk(s)</b><hr/></td></tr>";
            foreach($input['talk'] as $key=>$val):
                 $num = $key+1;
                 
                 $message .= "<tr> <td align='left' valign='top'><b>Talk #".$num."</b></td> <td align='left' valign='top'>".$val."</td> </tr> <tr> <td align='left' valign='top'><b>Authors</b></td> <td align='left' valign='top'>".$input['talk_authors'][$key]."</td> </tr> <tr> <td align='left' valign='top'><b>Abstract</b></td> <td align='left' valign='top'>".$input['talk_abstract'][$key]."</td> </tr> <tr> <td align='left' valign='top'><b>Duration</b></td> <td align='left' valign='top'>".$input['talk_duration'][$key]."</td> </tr> <tr> <td align='left' valign='top'><b>Delivery Date</b></td> <td align='left' valign='top'>".$input['talk_delivery_date'][$key]."</td> </tr>";
             endforeach;
          endif;   
          
          if(!empty($input['poster'])): 
            $message .= "<tr><td align='left' valign='top' colspan='2'><br><b>Poster(s)</b><hr/></td></tr>";
            foreach($input['poster'] as $key=>$val):
                 $num = $key+1;
                 $message .= "<tr>  <td align='left' valign='top'><b>Poster #".$num."</b></td>  <td align='left' valign='top'>".$val."</td>  </tr>  <tr>  <td align='left' valign='top'><b>Authors</b></td>  <td align='left' valign='top'>".$input['poster_authors'][$key]."</td>  </tr>  <tr>  <td align='left' valign='top'><b>Abstract</b></td>  <td align='left' valign='top'>".$input['poster_abstract'][$key]."</td>  </tr>  <tr>  <td align='left' valign='top'><b>Delivery Date</b></td>  <td align='left' valign='top'>".$input['poster_delivery_date'][$key]."</td>  </tr>";
             endforeach;
          endif;   
          
          unset($num);
   
          $message .= "<tr><td align='left' valign='top' colspan='2'><br><b>Proceedings</b><hr/></td></tr>";
          $proc = !empty($input['proceedings'])?$proceed[$input['proceedings']]: 'no proceedings';
          $message .= "<tr><td align='left' valign='top'><b>Proceedings format</b></td><td align='left' valign='top'>".$proc."</td></tr>";  
   
          $message .= "<tr><td align='left' valign='top' colspan='2'><br><b>Accomodation</b><hr/></td></tr>";
          $accomodation =  $acc[$input['reg_type']];
          $message .= "<tr><td align='left' valign='top'><b>Accomodation type</b></td><td align='left' valign='top'>".$accomodation['abbr']."</td></tr>";     
          // Roomate
          if($input['reg_type']=='4_bed_room' || $input['reg_type']=='2_bed_room' || $input['reg_type']=='3_bed_room'): 
              $message .= "<tr><td align='left' valign='top'><b>Roommate(s)</b></td><td align='left' valign='top'>".$input['roomate']."</td></tr>";     
          endif;     
          
          
          $message .= "<tr><td align='left' valign='top' colspan='2'><br><b>Workshops</b><hr/></td></tr>";
          $message .= "<tr><td align='left' valign='top'><b>Visual Wokshop</b></td><td align='left' valign='top'>".($input['workshop1']?'Yes':'No')."</td></tr>";     
          $message .= "<tr><td align='left' valign='top'><b>Spectroscropic Workshop</b></td><td align='left' valign='top'>".($input['workshop2']?'Yes':'No')."</td></tr>";     
                     
                            
          $message .= "<tr><td align='left' valign='top' colspan='2'><br><b>Other details</b><hr/></td></tr>";
          
          /*
          $message .= "<tr><td align='left' valign='top'><b>Meteoroid Shuttle</b></td><td align='left' valign='top'>".(($input['meteoroid_shuttle']!=-1)?'Yes':'No')."</td></tr>";                                                        
          */
          
          $message .= "<tr><td align='left' valign='top'><b>Tshirt size</b></td><td align='left' valign='top'>".(!empty($input['tshirt'])?$input['tshirt']:'No T-shirt')."</td></tr>";    
          
          $message .= "<tr><td align='left' valign='top'><b>Food Requirement</b></td><td align='left' valign='top'>".$food_req[$input['food']]."</td></tr>";    
          
          if(!empty($input['food_other'])): 
             $message .= "<tr><td align='left' valign='top'>&nbsp;</td><td align='left' valign='top'>".$input['food_other']."</td></tr>"; 
          endif;
          
          
          $message .= "<tr><td align='left' valign='top' colspan='2'><br><b>Payment</b><hr/></td></tr>";
          $message .= "<tr><td align='left' valign='top'><b>Method</b></td><td align='left' valign='top'>".$payment_methods[$input['payment_meth']]."</td></tr>"; 
          
          
          $message .= "<tr><td align='left' valign='top' colspan='2'><br><b>Comments</b><hr/></td></tr>";
          $message .= "<tr><td align='left' valign='top'><b>&nbsp;</b></td><td align='left' valign='top'>".$input['comments']."</td></tr>"; 
                                          
          $message .="</table>";
 
   
	        
          return $message;  			
    }
	
	 
}

?>
