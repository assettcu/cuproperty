<?php
/**
 * Cron Class, runs operations in the background.
 * 
 * This can run any process that needs to run in the background. Needs work with creating timers and possibly
 * database integration so multiple instances do not run at the same time.
 * 
 * @author      Ryan Carney-Mogan
 * @category    Core_Classes
 * @version     1.0.1
 * @copyright   Copyright (c) 2013 University of Colorado Boulder (http://colorado.edu)
 * 
 */
class Cron
{
    public $error_msg = "";     # Local error message
       
    /**
     * Run Cron (STATIC)
     * 
     * Starts the cron independent pipe.
     */
    public static function run_cron()
    {
        # This opens a new pipe to start the cron job.
        # cron.php uses CURL to load the Yii framework and run the cron job.
        pclose(popen("start php ".LOCAL_LIBRARY_PATH."/cron/cron.php","r"));
    }
    
    /**
     * Parse Watchlist
     * 
     * This function will go through each property and user to see if any new post
     * matches any keywords in a user's watchlist. If it does then it will send an
     * email to the user letting them know a new posting matches their watchlist.
     * 
     * This is to help departments with more "needs" than "haves" without actually
     * having to post a "need" as a post.
     */
    public function parse_watchlist() {
        
        try {
            # Query for property that has not been croned and has not bene removed
            $conn = Yii::app()->db;
            $query = "
                SELECT      propertyid
                FROM        {{property}}
                WHERE       croned = 0
                AND         status = 'posted'
                ORDER BY    date_updated ASC;
            ";
            $properties = $conn->createCommand($query)->queryAll();
            
            # No properties found? No need to finish this process
            if(!$properties or empty($properties)) {
                return true;
            }
            
            # Query all users that are active and have a watchlist
            $query = "
                SELECT      username
                FROM        {{users}}
                WHERE       active = 1
                AND         watchlist IS NOT NULL
                AND         watchlist != ''
                ORDER BY    username ASC;
            ";
            $usernames = $conn->createCommand($query)->queryAll();
            
            # No active users? No need to finish this process
            if(!$usernames or empty($usernames)) {
                return true;
            }
            
            # Loop through each un-croned property and search for keyword matches to user watchlists
            foreach($properties as $row2) {
                $property = new PropertyObj($row2["propertyid"]);
                
                # Loop through each user's watchlist
                foreach($usernames as $row) {
                        
                    # Initialize variables for searching
                    $user = new UserObj($row["username"]);
                    $found = false;
                    $matchedkeywords = array();
                    
                    # Loop through each keyword in the user's watchlist
                    foreach(explode(",",$user->watchlist) as $keyword) {
                        if(stripos($property->description,$keyword)>0) {
                            # Match found! Continue to match keywords but mark as found!
                            $found = true;
                            $matchedkeywords[] = $keyword;
                        }
                    }
                    
                    # We found a matching keyword (or more than one)
                    if($found === TRUE) {
                        $email = new EmailObj();
                        $email->send_matched_keywords($user->email, $property, $matchedkeywords);
                    }
                }
                # Update property as being croned
                $property->croned = 1;
                $property->save();
            }
        }
        # If there are any errors, set the local error message and return false
        catch(Exception $e) {
            $this->error_msg = $e->getMessage();
            return false;
        }
        
        # If we made it to here, success!
        return true;
    }
}
