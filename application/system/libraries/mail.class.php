<?php


/**
 * @author Vincent Perlerin
 */

class Mail {
    
	/**
	* Build Mailer
	*/
	private static function getMailer() {

 		$mail = new PHPMailer;
		$mail->isSMTP();										// Set mailer to use SMTP
		$mail->Host 			= SMTP_HOST;					// Specify main and backup SMTP servers
		$mail->SMTPAuth 		= true;							// Enable SMTP authentication
		$mail->Username 		= SMTP_USER;					// SMTP username
		$mail->Password 		= SMTP_PWD;             		// SMTP password
		$mail->SMTPSecure		= SMTPSecure;					// Enable TLS encryption, `ssl` also accepted
		$mail->Port 			= SMTP_TLS_PORT;				// TCP port to connect to	
		$mail->CharSet          = 'UTF-8';
        return $mail;
	}
    

    public static function send($data) {
 
        $mail = Mail::getMailer();
 			
        $mail->setFrom($data['from'],$data['from_name']);
        $mail->AddReplyTo($data['from'],$data['from_name']);
        $mail->addAddress($data['to'],$data['to_name']); // Add a recipient
             
        $mail->isHTML(true);  // HTML
        $mail->Subject  = $data['subject'];
        $mail->Body 	= nl2br($data['message']);
        $mail->AltBody 	= nl2br($data['message']);
        
        if(!empty($data['cc_email']) && !empty($data['cc_name'])):
            $mail->AddCC($data['cc_email'], $data['cc_name']);
        endif;    
         
        if(!empty($data['ccs'])):
            $mail->AddCC($data['ccs']);
        endif;  
        
        $mail->AddBCC('vperlerin@gmail.com','Vincent Perlerin');
 
        return $mail->send();
  
    }
    
    

}
?>
