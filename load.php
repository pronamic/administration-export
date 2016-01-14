<?php

include 'config.php';
include 'functions.php';

// Database
$dsn = sprintf( 
	'mysql:dbname=%s;host=%s',
	$db_name,
	$db_host
);

$options = array(
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
); 

$pdo = new PDO( $dsn, $db_user, $db_password, $options );
