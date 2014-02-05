<?php
/**
 * Image Object
 * 
 * This class pulls rows from the Image table. It correlates images with their properties. It also logs who
 * uploaded the images as well as when they were uploaded and their order.
 * 
 * @author      Ryan Carney-Mogan
 * @category    Core_Classes
 * @version     1.0.1
 * @copyright   Copyright (c) 2013 University of Colorado Boulder (http://colorado.edu)
 * 
 * @database    cuproperty
 * @table       images
 * @schema      
 * 		imageid				(int 255)			Image Identifying number (PK, Not Null, Auto-increments)
 *      propertyid			(int 255)			Property ID associated with image (Not Null)
 * 		location			(varchar 255)		Location of the file (Not Null)
 * 		sorder				(int 100)			Order of the image according to other images of the same property (Not Null)
 *      who_uploaded        (varchar 25)        Username of uploader
 * 		date_uploaded		(datetime)			Date and Time of image upload (Not Null)
 * 
 */
 
class ImageObj extends FactoryObj
{
    
    public function __construct($imageid=null)
    {
        parent::__construct("imageid","images",$imageid);
    }
    
	/*
	 * Pre-Delete
	 * 
	 * Overloaded function to call before deletion. Should delete the image file itself before
	 * removing the row from the table in the database.
	 */
    public function pre_delete()
    {
        if($this->loaded and is_file(getcwd()."/".$this->location)) {
            # unlink(getcwd()."/".$this->location);
        }
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
        # Schema version ffe585d6c74d89830b7d7e763cd5a813
        return array(
            array(
                "Field"     => "imageid",
                "Type"      => "int(255)",
                "Null"      => "NO",
                "Key"       => "PRI",
                "Default"   => NULL,
                "Extra"     => "auto_increment",
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
                "Field"     => "location",
                "Type"      => "varchar(255)",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "sorder",
                "Type"      => "int(100)",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => "0",
                "Extra"     => "",
            ),
            array(
                "Field"     => "who_uploaded",
                "Type"      => "varchar(25)",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "date_uploaded",
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
     * The only reason to modify this function is if a column has changed its name or
     * a column has been removed. Otherwise the Factory Class will automatically update
     * the table schema with the new column descriptions.
     * 
     * @return  (boolean)
     */
    public function upgrade()
    {
        return parent::upgrade();
    }
}
