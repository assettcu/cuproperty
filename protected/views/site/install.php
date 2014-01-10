<h1>Installing the CU Property Application</h1>
<?php
# Let's destroy any sessions currently, just in case
@Yii::app()->session->destroy();

# Does the application need installing? Check if database exists, and can connect.
try {
	if(is_null(Yii::app()->db)) {
		echo "Database is not configured properly. Please check the configuration files.";
	}
} catch(Exception $e) {
	echo "Database is not configured properly. Please check the configuration files.<br/>";
	echo "Error: ".$e->getMessage();
}

# Check to see if each of the tables are installed
$tables = array(
	"emails",
	"images",
	"property",
	"users"
);
echo "<span class='gray italic bold'>Looking for tables...</span><br/>";
echo "<div class='indent'>";
foreach($tables as $table) {
	
	$q = "SELECT 1 FROM {{".$table."}}";
	$conn = Yii::app()->db;
	
	# Try connecting to each table
	try {
		$conn->createCommand($q)->queryScalar();
		echo "Table found: <span class='orange'>".$table."</span><br/>";
		
	# If table can't be found then add it to the database
	} catch(Exception $e) {
		
		echo "<span class='red'>Could not find table: <span class='orange'>".$table."</span></span><br/>";
		echo "<span class='blue'>Adding table <span class='orange'>".$table."</span>... ";
		
		# Add table to the database using Yii transactions
		$transaction = $conn->beginTransaction();
		try {
			# Custom function to get table specific querys
			$q = get_table_sql($table);
			$command = $conn->createCommand($q);
			$command->execute();
			$transaction->commit();
			
		# If there was an error adding the table to the database exit gracefully
		} catch(Exception $f) {
			$transaction->rollback();
			echo "<span class='red'>Could not add table: ".$f."</span><br/>";
			echo "<hr /><span class='red'>INSTALL WAS NOT COMPLETED</span>";
			return;
		}
		echo "added!</span><br/>";
	}
}
echo "</div>";
echo "<span class='blue'>All tables added/found!</span><br/>";

echo "<hr/>";
echo "<div class='font-bigger bold'>Installation complete. You will be redirected in <span id='time'>5</span> seconds.</div>";

# Function to serve up table specific SQL queries
function get_table_sql($table) {
	switch($table) {
		case "emails":
			return "
				CREATE TABLE `emails` (
				  `emailid` int(255) NOT NULL AUTO_INCREMENT,
				  `emailfrom` varchar(50) NOT NULL,
				  `propertyid` int(255) NOT NULL,
				  `date_sent` datetime NOT NULL,
				  PRIMARY KEY (`emailid`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
			";
		break;
		case "images":
			return "
				CREATE TABLE `images` (
				  `imageid` int(255) NOT NULL AUTO_INCREMENT,
				  `propertyid` int(255) NOT NULL,
				  `location` varchar(255) NOT NULL,
				  `sorder` int(100) NOT NULL DEFAULT '0',
				  `who_uploaded` varchar(25) NOT NULL,
				  `date_uploaded` datetime NOT NULL,
				  PRIMARY KEY (`imageid`)
				) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=latin1;
			";
		break;
		case "property":
			return "
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
			";
		return;
		case "users":
			return "
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
			";
		break;
		default: return "";
	}
}

?>