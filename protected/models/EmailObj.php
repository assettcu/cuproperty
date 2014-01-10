<?php
/**
 * Email Object
 * 
 * The purpose of this class is mainly log emails in our email database. If clients use our system to email each other
 * we want to make sure there are no communication errors. If one party said they had not received an email we can
 * check the system to verify. We do not store any of the email's contents, just from who it was sent and which property
 * it was concerning.
 * 
 * 
 * @author      Ryan Carney-Mogan
 * @category    Core_Classes
 * @version     1.0.1
 * @copyright   Copyright (c) 2013 University of Colorado Boulder (http://colorado.edu)
 * 
 * @database    cuproperty
 * @table       emails
 * @schema      
 * 		emailid				(int 255)			Email Identifying number (PK, Not Null, Auto-increments)
 *      emailfrom			(varchar 50)		What email is the originator (Not Null)
 * 		propertyid			(int 255)			Identifying number of the property (Not Null)
 * 		date_sent			(datetime)			Date and Time of email sent (Not Null)
 * 
 */
 
class EmailObj extends FactoryObj
{
    public $error = "";
    
    /**
     * Constructor sets up the class with an associated email row if @param $emailid is not null.
     *
     * @param   (string)    $emailid  (Optional) The ID of the email log.
     */
    public function __construct($emailid=null) 
    {
        parent::__construct("emailid","emails",$emailid);
    }
    
    /**
     * Send Response to Post
     * 
     * Sends a customized email to the recepient.
     * 
     * @param   (string)    $email_to     	Which to send to
	 * @param	(string)	$email_text		The email text to attach (in addition to pre-formatted text)
	 * @param	(object)	$property		Property Object which has all the property information
     */
    public function send_response_to_post($email_to,$email_text,$property) 
    {
        # Append the header to email body
        ob_start();
?>This email is in response to post #<?php echo $property->propertyid;?>. The description of the property is as follows:

<?php echo $property->description; ?>


As of <?php echo StdLib::format_date($property->date_updated,"normal");?>

-------------------------------------------------------------------------------------------------
<?php
        $body = ob_get_contents();
        ob_end_clean();
        
        $email_text = $body . $email_text;
        $email_subject    = "CU Property Response Email: Post #".$property->propertyid;
        
        # Load user to get their email address
        $user = new UserObj(Yii::app()->user->name);
        $email_from = array(
            $user->name => $user->email
        );
        
        $mail = new Mail;
        if($mail->send_mail($email_from,$email_to,$email_subject,$email_text)) {
        	$values = array_values($email_from);
            $this->emailfrom = array_pop($values);
            $this->propertyid = $property->propertyid;
            $this->date_sent = date("Y-m-d H:i:s");
            $this->save();
        } else {
        	$this->error = "Could not send mail. Error: ".$mail->error;
			return false;
        }
		
		return true;
    }
}