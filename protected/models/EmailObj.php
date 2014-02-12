<?php
/**
 * Email Object
 * 
 * The purpose of this class is mainly log emails in our email database. If clients use our system to email each other
 * we want to make sure there are no communication errors. If one party said they had not received an email we can
 * check the system to verify. We do not store any of the email's contents, just the people conversating and which property
 * it's concerning.
 * 
 * The system will also log croned emails. The system checks user watchlists for any property matches and will email the
 * users if there are matches. The system will record these emails as well, including the matched keywords.
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
 *      emailto             (varchar 50)        Who the email is going to (Not Null)
 * 		propertyid			(int 255)			Identifying number of the property (Not Null)
 *      matchedkeywords     (text)              Cron job will create emails based on matched keywords
 * 		date_sent			(datetime)			Date and Time of email sent (Not Null)
 * 
 */
 
class EmailObj extends FactoryObj
{
    public $error           = "";
    public $matchedkeywords = "";
    
    /**
     * Constructor sets up the class with an associated email row if @param $emailid is not null.
     *
     * @param   (string)    $emailid  (Optional) The ID of the email log.
     */
    public function __construct($emailid=null) 
    {
        parent::__construct("emailid", "emails", $emailid);
    }
    
    /**
     * Get Schema
     * 
     * This returns the schema this class should have in the database.
     * This might differ from get_current_schema() which gets what the database current has.
     * MD5 hashing the schema is used to compare the database and the object schema.
     * 
     * @return  (array)
     */
    public function get_schema() {
        # Schema version 1b2f12ec31b0a17f1bcbc4eae89a639e
        return array(
            array(
                "Field"     => "emailid",
                "Type"      => "int(255)",
                "Null"      => "NO",
                "Key"       => "PRI",
                "Default"   => NULL,
                "Extra"     => "auto_increment",
            ),
            array(
                "Field"     => "emailfrom",
                "Type"      => "varchar(50)",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "emailto",
                "Type"      => "varchar(50)",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "propertyid",
                "Type"      => "int(255)",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "matchedkeywords",
                "Type"      => "text",
                "Null"      => "YES",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "date_sent",
                "Type"      => "datetime",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
        );
    }

    /**
     * Update Schema
     * 
     * The only reason to modify this function is if a column has updated its name or
     * a column has been removed.
     * 
     * @return  (boolean)
     */
    public function upgrade()
    {
        return parent::upgrade();
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
        try {
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
                $this->emailfrom = $user->username;
                $this->email_to = $email_to;
                $this->propertyid = $property->propertyid;
                $this->date_sent = date("Y-m-d H:i:s");
                $this->save();
            } else {
            	throw new Exception("Could not send mail. Error: ".$mail->error);
            }
        }
        catch(Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
        
		return true;
    }

    /**
     * Send Matched Keyword Email
     * 
     * Cron will send an email if a new post has any matches to any active user's watchlist.
     * 
     * @param   (string)    $email_to           Which to send to
     * @param   (object)    $property           Property Object which has all the property information
     * @param   (string)    $matchedkeywords    The watchlist words that matched the listing
     */
    public function send_matched_keywords($email_to,$property,$matchedkeywords)
    {
        try {
            # Append the header to email body
            ob_start();
?>This email is generated because a <a href="<?php echo Yii::app()->baseUrl.Yii::app()->createUrl('property'); ?>?id=<?php echo $property->propertyid; ?>">new CU Property posting</a> 
matched one or more of your keywords: <div style="margin:10px 4px;"><i><?php echo implode(", ",$matchedkeywords); ?></i></div>
Below is a description of the property with the highlighted matching keywords:
<hr/>
<br/>

<?php
            echo $property->department." | ".$property->contactname."<br/>";
            echo "<div style='font-style:italics;font-size:10px;color:000;'>Posted: ".$property->date_added."</div><br/>";
            $desc = $property->description;
            foreach($matchedkeywords as $keyword) {
                $desc = str_ireplace($keyword,"<strong>".$keyword."</strong>",$desc);
            }
            echo "<div style='color:#777;'><i>".$desc."</i></div>";
            $body = ob_get_contents();
            ob_end_clean();
            
            $email_text = $body . $email_text;
            $email_subject    = "CU Property watchlist matched a new posting!";
            
            $email_from = array(
                "CU Property" => "cuproperty@assett.colorado.edu"
            );
            
            $mail = new Mail;
            if($mail->send_mail($email_from,$email_to,$email_subject,$email_text)) {
                $values = array_values($email_from);
                $this->emailfrom = array_pop($values);
                
                $values = array_values($email_to);
                $this->emailto = array_pop($values);
                
                $this->matchedkeywords = $matchedkeywords;
                $this->propertyid = $property->propertyid;
                $this->date_sent = date("Y-m-d H:i:s");
                $this->save();
            } else {
                throw new Exception("Could not send mail. Error: ".$mail->error);
            }
        }
        catch(Exception $e) {
            $this->error = $e->getMessage();  
            return false; 
        }
        
        return true;
    }

}