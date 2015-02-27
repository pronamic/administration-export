<?php

include 'config.php';
include 'functions.php';

// Database
$dsn = sprintf( 
	'mysql:dbname=%s;host=%s',
	$db_name,
	$db_host
);

$pdo = new PDO( $dsn, $db_user, $db_password );
