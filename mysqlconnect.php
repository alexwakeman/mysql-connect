<?php

define('DB_ADDR', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'databaseXYZ');


/*
	Useage: 
	
	// 
	$update = $conn->update('table_name', array('column_1', 'column_2'), array($val1, $val2), array('where_column'), array($where_value));
	$myCollection = $conn->getRows('table_name', array('where_column1', 'where_column2'), array($id, $where_clause_value), 'orderby_column_name', 'DESC', (int)$limit_min, $offset);
*/

/**
 * Convenience methods for building MySQL queries using arrays as parameters
 *
 * @author wakemana
 */
class Mysqlconnect {

	public function __construct() {
		;
	}

	public function update($tableName, $cols, $vals, $whereCols, $whereVals) {
		$link = new mysqli(DB_ADDR, DB_USER, DB_PASS, DB_NAME);
		
		if (!$tableName || !is_string($tableName)) {
			return false;
		}

		$sql = 'UPDATE ' . $tableName . ' ';
		$sql .= ' SET ';
		for ($i = 0; $i < count($cols); $i++) {
			$vals[$i] = mysql_real_escape_string(filter_var($vals[$i], FILTER_SANITIZE_STRING));
			if ($i < count($cols) - 1) {
				$sql .= $cols[$i] . ' = "' . $vals[$i] . '", ';
			} else {
				$sql .= $cols[$i] . ' = "' . $vals[$i] . '" ';
			}
		}
		$sql .= ' WHERE ';

		for ($i = 0; $i < count($whereCols); $i++) {
			$whereVals[$i] = mysql_real_escape_string(filter_var($whereVals[$i], FILTER_SANITIZE_STRING));
			$sql .= $whereCols[$i] . ' = ' . $whereVals[$i] . ' ';
			if ($i < count($whereCols) - 1) {
				$sql .= ' AND ';
			}
		}
		$result = $link->query($sql);
		return $result;
	}

	public function getRow($tableName, $cols, $vals) {
		$link = new mysqli(DB_ADDR, DB_USER, DB_PASS, DB_NAME);

		if (!$tableName || !is_string($tableName)) {
			return false;
		}

		$sql = 'SELECT * FROM ' . $tableName . ' ';

		if ($cols && $vals) {
			for ($i = 0; $i < count($cols); $i++) {

				if ($i == 0) {
					$sql .= ' WHERE ';
				}
				$sql .= $cols[$i] . ' =  \'' . mysql_real_escape_string(filter_var($vals[$i], FILTER_SANITIZE_STRING)) . '\'';
				if ($i < count($cols) - 1) {
					$sql .= ' AND ';
				}
			}
		} else {
			return false;
		}

		$result = $link->query($sql);
		$row = $result->fetch_assoc();
		if (count($row) > 0)
			return $row;
		else
			return false;
	}

	public function getRows($tableName, $cols = false, $vals = false, $orderBy = false, $sortOrder = 'DESC', $limitMin = null, $offset = null) {
		$link = new mysqli(DB_ADDR, DB_USER, DB_PASS, DB_NAME);

		$sql = 'SELECT * FROM ' . $tableName . ' ';

		if ($cols && $vals) {
			for ($i = 0; $i < count($cols); $i++) {

				if ($i == 0) {
					$sql .= ' WHERE ';
				}
				$sql .= $cols[$i] . ' =  "' . mysql_real_escape_string(filter_var($vals[$i], FILTER_SANITIZE_STRING)) . '"';
				if ($i < count($cols) - 1) {
					$sql .= ' AND ';
				}
			}
		}

		if ($orderBy) {
			$sql .= ' ORDER BY ' . $orderBy . " $sortOrder " ;
		}
		
		if ($limitMin !== null && $offset !== null) $sql .= ' LIMIT ' . $limitMin . ', ' . $offset . ' ';
		$result = $link->query($sql);
		
		if ($result->num_rows > 0) {
			$rows = array();
			for ($i = 0; $i < $result->num_rows; $i++) {
				$result->data_seek($i);
				$row = $result->fetch_assoc();
				array_push($rows, $row);
			}
			return $rows;
		} else {
			return false;
		}
	}

	public function saveEntry($tableName, $cols, $vals) {
		$link = new mysqli(DB_ADDR, DB_USER, DB_PASS, DB_NAME);

		$sql = 'INSERT INTO ' . $tableName . ' ';
		$sql .= '(';
		for ($i = 0; $i < count($cols); $i++) {

			$cols[$i] = mysql_real_escape_string($cols[$i]);
			if ($i < count($cols) - 1) {
				$sql .= $cols[$i] . ', ';
			} else {
				$sql .= $cols[$i];
			}
		}
		$sql .= ')';
		$sql .= ' VALUES ';
		$sql .= '(';
		for ($i = 0; $i < count($vals); $i++) {
			$vals[$i] = mysql_real_escape_string($vals[$i]);
			if ($i < count($vals) - 1) {
				$sql .= '"' . $vals[$i] . '",';
			} else {
				$sql .= '"' . $vals[$i] . '"';
			}
		}
		$sql .= ') ';

		$result = $link->query($sql);
		$lastInsert = mysqli_insert_id($link);
		return $lastInsert;
	}
}

?>
