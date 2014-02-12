<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */

define ("ERROR_INVALID_CREDENTIALS",    1);
define ("ERROR_MAX_ATTEMPTS",           2);
define ("ERROR_AUTH_GROUP_INVALID",     3);

class UserIdentity extends CUserIdentity
{
    public function authenticate()
    {
        $this->errorCode=self::ERROR_NONE;

        $authenticated = false;
        $username = $this->username;
        $password = $this->password;
        
        # Check if user exists or is locked out
        $user = new UserObj($username);
        if($user->loaded and isset($user->active,$user->attempts) and ($user->active==0 or $user->attempts>5))
        {
            $this->errorCode=ERROR_MAX_ATTEMPTS;
            return !$this->errorCode;
        }
        
        # The new Authentication System
        $adauth = new ADAuth("adcontroller");
        
        # Authenticate!
        if($adauth->authenticate($username, $password)){
            
            # !Important! User groups and their permission levels
            # You can add your own AD Groups if you manage admin accounts through them
            $valid_groups = array(
                "ASSETT-Programming"=>10,
                "ASSETT-Admins"=>10,
                "ASSETT-TTAs"=>3,
                "ASSETT-Core"=>3,
                "ASSETT-Staff"=>3,
                "ASSETT-ATCs"=>3,
                "ASSETT-Design"=>3,
                "ASSETT-Old"=>1,
            );
            
            # Empty for now
            $info = $adauth->lookup_user();
                
            # Iterate through groups and assign user to appropriate groups
            foreach($valid_groups as $group=>$permlevel) {
                if($adauth->is_member($group)) {
                    // Update only if membership changed or new user
                    if($user->loaded === FALSE) {
                        $user->permission = $permlevel;
                    }
                    break;
                }
            }
            
            if(is_null($user->permission) and !$user->loaded) {
                $user->permission = 1;
            }
            
            $user->email = $info[0]["mail"][0];
            $user->name = $info[0]["displayname"][0];
            
            if($user->permission==0) {
                $this->errorCode = ERROR_AUTH_GROUP_INVALID;
            }
                
            if(!$this->errorCode) {
                /**
                 * Load the system and check if an admin account exists (do not need to load each table object)
                 * No users = no admin account, make the first person to login the user account if in DEBUG mode
                 * This would be a security issue if someone leaves a production site in DEBUG mode and there's a fresh install
                 * which is very unlikely.
                 */
                $system = new System(false);
                if($system->count_users() == 0 and YII_DEBUG) {
                    $user->permission = 10;
                }
                # Default the watchlist to their username
                if(!$user->loaded) {
                    $user->watchlist = $user->username;
                }
                # Continue to create the user
                $user->last_login = date("Y-m-d H:i:s");
                $user->attempts = 0;
                $user->save();
                
                # After saving, reload the user to pull any information that automatically added to the database
                $user->load();
            }
            
            # Switch to the directory and lookup user's CU affiliation (student/staff/faculty)
            $adauth->change_controller("directory");
            $info = $adauth->lookup_user();
            $user->roles = $this->parse_roles($info[0]["edupersonaffiliation"]);
            
            # Save and reload
            $user->save();
            $user->load();

        } else {
            if($user->loaded)
            {
                $user->attempts++;
                $user->save();
            }
            $this->errorCode=ERROR_INVALID_CREDENTIALS;
        }
        
        return !$this->errorCode;
    }

    /*
     * Takes "edupersonaffiliation" field values from the Directory.
     * Parses whether the person is student/staff/faculty.
     * A person may be none or many.
     * 
     */
    private function parse_roles($roles) {
        if(!is_array($roles) or empty($roles)) {
            return "";
        }
        $return = array();
        foreach($roles as $role) {
            $role = trim(strtolower($role));
            switch($role) {
                case "student": 
                    if(!in_array("student",$return)) $return[] = "student"; 
                break;
                case "employee":
                case "staff":
                case "officer/professional":
                    if(!in_array("staff",$return)) $return[] = "staff";
                break;
                case "faculty":
                    if(!in_array("faculty",$return)) $return[] = "faculty";
                break;
            }
        }
        
        return implode(",",$return);
    }
}