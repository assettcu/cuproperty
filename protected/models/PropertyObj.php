<?php
/**
 * Property Object
 * 
 * Factory Object extension for property objects.
 * 
 * 
 * @author      Ryan Carney-Mogan
 * @category    Core_Classes
 * @version     1.0.1
 * @copyright   Copyright (c) 2013 University of Colorado Boulder (http://colorado.edu)
 * 
 * @database    cuproperty
 * @table       property
 * @schema      
 *      propertyid      (int 255)                   Value of property ID (PK, Not Null, Auto-increments)
 *      department      (varchar 255)               Department name (Not Null)
 *      contactname     (varchar 60)                Name of the point of contact (Not Null)
 *      contactemail    (varchar 255)               Email of the point of contact
 *      contactphone    (varchar 25)                Phone of the point of contact
 *      status          (enum 'posted','removed')   Status of the post of the property (Not Null)
 *      description     (text)                      Description of the property
 *      postedby        (varchar 25)                Username of property creator (Not Null)
 *      date_added      (datetime)                  Date property was added (Not Null)
 *      date_updated    (datetime)                  Date property was updated
 * 
 */
 
class PropertyObj extends FactoryObj
{
    
    public function __construct($propertyid=null)
    {
        parent::__construct("propertyid","property",$propertyid);
    }
    
    
	/**
	 * Pre-Save
	 * 
	 * Overloaded function to call before saving. This should automatically add the user
	 * adding this property as well as the date it was added.
	 */
    public function pre_save()
    {
        if(!$this->loaded and !$this->is_valid_id()) {
            $this->postedby     = Yii::app()->user->name;
            $this->date_added   = date("Y-m-d H:i:s");
        }
        
        $this->date_updated = date("Y-m-d H:i:s");
    }
    
    /**
	 * Post Load
	 * 
	 * Overloaded function to call after loading property. Load all images associated with this
	 * property (if loaded).
	 */
    public function post_load()
    {
        if($this->loaded) {
            $this->load_images();
        }
    }
    
	/**
	 * Run Check
	 *
	 * Overloaded function. It will check to make sure all property values are proper before saving.
	 * This function is a check only, it shouldn't modify values.
	 *
	 * @return	(boolean)
	 */
    public function run_check()
    {
        if($this->department == "") {
            return !$this->set_error("Department/Program name cannot be empty");
        }
        if($this->contactname == "") {
            return !$this->set_error("Contact Name cannot be empty.");
        }
        if($this->contactemail == "") {
            return !$this->set_error("Contact Email cannot be empty.");
        }
        if(!preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i",$this->contactemail)) {
            return !$this->set_error("Contact Email is malformed or incorrect. Check to make sure it is valid.");
        }
        if($this->description == "") {
            return !$this->set_error("Description of CU Property cannot be empty.");
        }
        return true;
    }
    
	/**
	 * Has Images
	 *
	 * Returns whether this property has images or not. Must be loaded previously.
	 *
	 * @return	(boolean)
	 */
    public function has_images()
    {
        if($this->loaded and isset($this->images)) {
            return (count($this->images)>0);
        }
        return false;
    }
    
	/**
	 * Load Images
	 *
	 * Loads images from the database based on this property's ID.
	 *
	 * @return	(object[])
	 */
    public function load_images()
    {
        $this->images = array();
        $conn = Yii::app()->db;
        $query = "
            SELECT      imageid
            FROM        {{images}}
            WHERE       propertyid = :propertyid
            ORDER BY    sorder ASC, date_uploaded DESC;
        ";
        $command = $conn->createCommand($query);
        $command->bindParam(":propertyid",$this->propertyid);
        $result = $command->queryAll();
        
        if(!$result or empty($result)) {
            return array();
        }
        
        foreach($result as $row) {
            $image = new ImageObj($row["imageid"]);
            if(!is_file(getcwd()."/".$image->location)) {
                $image->delete();
            } else {
                $this->images[] = $image;
            }
        }
        
        return $this->images;
    }
}
