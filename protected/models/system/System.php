<?php

class System
{
    public  $objects = array();
    
    private $error_flag     = FALSE;
    private $error_msg      = "";
    
    public function __construct($init=TRUE) {
        if($init===TRUE) {
            $this->init();
        }
    }
    
    public function init() {
        try {
            Yii::app()->db->setActive(true);
            # !IMPORTANT! These objects must be instantiated in order for the system
            # to recognize the connections to the tables.
            $this->objects = array(
                new EmailObj(),
                new ImageObj(),
                new IssueObj(),
                new PropertyObj(),
                new UserObj()
            );
        }
        catch(Exception $e) {
            $this->set_error("System is unhealthy: ".$e->getMessage());
            return false;   
        }
        
        return true;
    }
    
    public function install()
    {
        # Does the application need installing? Check if database exists, and can connect
        try {
            # Required fields
            $required = array(
                "db-host",
                "db-name",
                "db-username",
                "db-password",
                "table-prefix"
            );
            
            # Did all the required fields get passed in?
            if(count(array_intersect($required, array_keys($_REQUEST))) != count($required)) {
                throw new Exception("Not all required fields were submitted.");
            }
            
            # Verify the required unempty fields
            foreach($required as $field) {
                # Skip the fields that can be empty
                if($field == "table-prefix" or $field == "db-password") {
                    continue;
                }
                # Check if empty, throw error if they are.
                if(empty($_REQUEST[$field])) {
                    throw new Exception("Field <i>".lookupfieldname($field)."</i> cannot be empty.");
                }
            }

            # Try connecting to the database with the passed in credentials
            try {
                # Setup connection details
                $dsn = 'mysql:host='.$_REQUEST["db-host"].';dbname='.$_REQUEST["db-name"];
                $username = $_REQUEST["db-username"];
                $password = $_REQUEST["db-password"];
                $prefix = $_REQUEST["table-prefix"];
                
                # Make the connection
                $conn = new CDbConnection($dsn, $username, $password);
                $conn->active = true;
                $conn->setActive(true);
            }
            catch(Exception $e) {
                throw new Exception("Could not connect to database. Make sure you have created the database first. Details: ".$e->getMessage());
            }

            # Setup the database params for saving in the extended configuration
            $db_params = array(
                'components'=>array(
                    'db'=>array(
                        'connectionString'  => $dsn,
                        'emulatePrepare'    => true,
                        'username'          => $username,
                        'password'          => $password,
                        'charset'           => 'utf8',
                        'tablePrefix'       => $prefix,
                    ),
                ),
                'params'=>array(
                    'LOCALAPP_SERVER'           => $_SERVER["HTTP_HOST"],
                ),
            );
            
            # Make sure to only overwrite if explicitly asked to
            $config_ext = Yii::app()->basePath."\\config\\main-ext.php";
            if(is_file($config_ext)) {
                throw new Exception("Database configuration already exists. Delete this configuration in order to install this application.");
            }
            
            # Open up the file and write the new configuration.
            $handle = fopen($config_ext,"w");
            fwrite($handle,"<?php return ");
            fwrite($handle,var_export($db_params,true));
            fwrite($handle,"; ?>");
            fclose($handle);
            
            # Make read-only
            chmod($config_ext, 0060);
        } 
        # Catch all the errors and output them as Flashes
        catch(Exception $e) {
            $this->set_error($e->getMessage());
            return false;
        }
        
        # If we made it to here, installation is a success!
        return true;
    }
    
    # Define a couple of local functions first
    # Function to change field name
    private function lookupfieldname($field) {
        switch($field) {
            case "db-host": return "Database Host";
            case "db-name": return "Database Name";
            case "db-username": return "Database Username";
            case "db-password": return "Database Password";
            case "table-prefix": return "Table Prefix";
            default: return $field;
        }
    }
    
    # Function to serve up table specific SQL queries
    private function get_table_sql($table) {
        switch($table) {
            case "emails":
                $obj = new EmailObj();
                return $obj->create_table_schema();
            break;
            case "images":
                $obj = new ImageObj();
                return $obj->create_table_schema();
            break;
            case "property":
                $obj = new PropertyObj();
                return $obj->create_table_schema();
            return;
            case "users":
                $obj = new UserObj();
                return $obj->create_table_schema();
            break;
            case "issues":
                $obj = new IssueObj();
                return $obj->create_table_schema();
            break;
            default: return "";
        }
    }
    
    public function healthy() {
        try {
            $conn = Yii::app()->db;
            foreach($this->objects as $obj) {
                $q = "DESCRIBE {{".$obj->table."}}";
                $conn->createCommand($q)->queryAll();
                $obj->has_matching_schema();
            }
        }
        catch(Exception $e) {
            return false;
        }
        
        return true;
    }
    
    public function update()
    {
        try {
            # Loop through each of the objects
            foreach($this->objects as $obj) {
                # This will upgrade the mysql database tables
                if(!$obj->has_matching_schema()) {
                    if(!$obj->upgrade()) {
                        throw new Exception("Error updating table: ".$obj->get_error());
                    }
                    if(!$obj->has_matching_schema()) {
                        throw new Exception("Table schema mismatch.");
                    }
                }
            }
        }
        catch(Exception $e) {
            $this->set_error($e->getMessage());
            return false;
        }
        
        return true;
    }
    
    private function set_error($message) {
        $this->error_flag   = TRUE;
        $this->error_msg    = $message;
    }
    
    public function get_error() {
        return $this->error_msg;
    }
    
    public function has_error() {
        return ($this->error_flag === TRUE);
    }
    
}
