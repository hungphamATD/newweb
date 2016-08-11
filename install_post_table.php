<?php
require_once("./../db.php");

$dbobj=new db_gafam;
$servername = "localhost";
$username = "root";
$password = "";
$dbname="gafam";
//connect to database, if failed connection then make new database
if(!$dbobj->connectDB($servername, $username, $password, $dbname)){
	$dbobj->createDB($dbname);
}

//create user table

$table_name="user_tb";
$table_content="id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				type VARCHAR(50) NOT NULL,
				username VARCHAR(50) NOT NULL,
				email VARCHAR(50) NOT NULL,
				password VARCHAR(50) NOT NULL,
				salt VARCHAR(8) NOT NULL,
				first_name VARCHAR(50) NOT NULL,
				last_name VARCHAR(50) NOT NULL,
				last_login INT(11) UNSIGNED,
				last_activity INT(11) UNSIGNED,
				activation VARCHAR(50) NOT NULL,
				time_created INT(11) UNSIGNED
				";
//

if($dbobj->createTable($table_name,$table_content)){
	echo "create table successful";
}
else{
	die("created user table failed");
}


?>