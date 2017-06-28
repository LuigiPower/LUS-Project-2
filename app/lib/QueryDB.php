<?php
/*
 * Class to connect and query DB
 */

class QueryDB {
	
	public function query($sql) {
		
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
		//print_r($mysqli);
		
		if ($mysqli->connect_errno) {
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}
		
		// query
		$result = $mysqli->query($sql);
		
		if (!$result) {
			echo "DB Error, could not query the database\n";
			echo 'MySQL Error: ' . mysql_error();
			exit;
		}


		$db_results = array();
		while ($row = $result->fetch_assoc()) {
			$db_results[] = $row;
			//echo $row[$class] . "\n";
			//echo "<br/>";
		}

		$result->free();
		$mysqli->close();
		
		return $db_results;
	}
	
}
