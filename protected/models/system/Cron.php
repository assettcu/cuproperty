<?php

class Cron
{
    public $error_msg = "";
       
    public static function run_cron()
    {
        pclose(popen("start php ".LOCAL_LIBRARY_PATH."/cron/cron.php","r"));
    }
    
    public function parse_watchlist() {
        try {
            $conn = Yii::app()->db;
            $query = "
                SELECT      propertyid
                FROM        {{property}}
                WHERE       croned = 0
                AND         status = 'posted'
                ORDER BY    date_updated ASC;
            ";
            $properties = $conn->createCommand($query)->queryAll();
            
            # No properties? No need to cron.
            if(!$properties or empty($properties)) {
                return true;
            }
            
            $query = "
                SELECT      username
                FROM        {{users}}
                WHERE       active = 1
                AND         watchlist IS NOT NULL
                AND         watchlist != ''
                ORDER BY    username ASC;
            ";
            
            $usernames = $conn->createCommand($query)->queryAll();
            
            # No active users? No need to cron.
            if(!$usernames or empty($usernames)) {
                return true;
            }
            
            # Loop through each un-croned property and search for keyword matches to user watchlists
            foreach($properties as $row2) {
                $property = new PropertyObj($row2["propertyid"]);
                
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
                $property->croned = 1;
                $property->save();
            }
        }
        catch(Exception $e) {
            $this->error_msg = $e->getMessage();
            return false;
        }
        
        return true;
    }
}
