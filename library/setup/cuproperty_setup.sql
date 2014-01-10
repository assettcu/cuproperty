/*
 * Author: Ryan Carney-Mogan
 * MySQL - 5.5.10-log : Database - cuproperty
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`cuproperty` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `cuproperty`;

/*Table structure for table `emails` */

DROP TABLE IF EXISTS `emails`;

CREATE TABLE `emails` (
  `emailid` int(255) NOT NULL AUTO_INCREMENT,
  `emailfrom` varchar(50) NOT NULL,
  `propertyid` int(255) NOT NULL,
  `date_sent` datetime NOT NULL,
  PRIMARY KEY (`emailid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `images` */

DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
  `imageid` int(255) NOT NULL AUTO_INCREMENT,
  `propertyid` int(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `sorder` int(100) NOT NULL DEFAULT '0',
  `who_uploaded` varchar(25) NOT NULL,
  `date_uploaded` datetime NOT NULL,
  PRIMARY KEY (`imageid`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=latin1;

/*Table structure for table `property` */

DROP TABLE IF EXISTS `property`;

CREATE TABLE `property` (
  `propertyid` int(255) NOT NULL AUTO_INCREMENT,
  `department` varchar(255) NOT NULL,
  `contactname` varchar(60) NOT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `contactphone` varchar(25) DEFAULT NULL,
  `status` enum('posted','removed') NOT NULL DEFAULT 'posted',
  `description` text,
  `postedby` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`propertyid`)
) ENGINE=InnoDB AUTO_INCREMENT=1265 DEFAULT CHARSET=latin1;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `permission` int(10) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `attempts` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `preferences` text,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
