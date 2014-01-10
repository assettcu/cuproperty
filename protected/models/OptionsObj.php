<?php
/**
 * Options Object
 * 
 * Pulls an option from the database for the application. An example option would be:
 * 		Option Name:	siteurl
 * 		Option Value:	http://cuproperty.colorado.edu
 * 
 * @author      Ryan Carney-Mogan
 * @category    Core_Classes
 * @version     1.0.1
 * @copyright   Copyright (c) 2013 University of Colorado Boulder (http://colorado.edu)
 * 
 * @database    cuproperty
 * @table       options
 * @schema      
 *      option_name      	(varchar 255)		Option name (PK, Not Null)
 *      option_value	    (text)              Option value (Not Null)
 * 
 * 
 * 
 * ** DEPRECATED TABLE AND CLASS **
 */
class OptionsObj extends FactoryObj
{
	
	public function __construct($option_name=null) {
		parent::__construct("option_value", "options", $option_name);
	}
	
}
