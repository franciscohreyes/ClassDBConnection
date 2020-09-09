<?php
/**
 * Created at: 09/09/2020
 * [Connection to database using PHP and Mysqli]
 */

/**
 * We install a global object to all PHP files that include it
 */
$bd = new BD();

class BD {
  var $host     = "localhost";
  var $user     = "DB_USER";
  var $password = "DB_PASSWORD";
  var $database = "DB_DATABASE";
  var $conn;

  const OPEN  = 1;
  const CLOSE = 0;

  var $status = self::CLOSE;

  /**
   * [Open connection]
   * @function
   */
  public function open(){
    $this->conn = mysqli_connect($this->host, $this->user, $this->password) or die(mysqli_error());
    $this->conn->select_db($this->database) or die(mysqli_error());
  }

  /**
   * [Close connection]
   * @function
   */
  public function close(){
    mysql_close($this->conn);
  }

  /**
   * [Execute query not return results]
   * @function
   * @param string $sql
   */
  public function ExecuteNonQuery($sql){
    if($sql){
      if($this->status == self::CLOSE) $this->open();

      $result = mysqli_query($this->conn, $sql);
      settype($result, "null");
    }
  }

  /**
   * [Execute a query]
   * @function
   * @param string $query
   * @param array records
   */
  public function Execute($query){
    if($query != ""){
      if($this->status == self::CLOSE) $this->open();

      $result  = mysqli_query($this->conn, $query);
      $records = array();

      while($reg = mysqli_fetch_array($result)){
        $records[] = $reg;
      }

      return $records;
    } else {
      echo "param is required";
    }
  }

  /**
   * [Execute a query by returning a row with all its fields]
   * @function
   * @param string $tableName
   * @param string $filter
   * @return array
   */
  public function ExecuteRecord($tableName, $filter){
    $all = $this->Execute("SELECT * FROM $tableName WHERE $filter");

    if(count($all) > 0){
      return $all[0];
    }

    return false;
  }

  /**
   * [Execute a query by returning a column with all your records]
   * @param string $tableName
   * @param string $field
   * @param string $filter
   * @return array
   */
  public function ExecuteField($tableName, $field, $filter){
    $all = $this->Execute("SELECT $field FROM $tableName WHERE $filter");

    $aux = array();

    foreach ($all as $row) {
      $aux[] = $row[0];
    }

    return $aux;
  }

  /**
   * [Bring all the records of a table]
   * @function
   * @param string $tableName
   * @param string $order
   * @return array
   */
  public function ExecuteTable($tableName, $order = "", $limit = 10){
    if($order != ""){
      return $this->Execute("SELECT * FROM ". $tableName . " ORDER BY ". $order);
    } else {
      return $this->Execute("SELECT * FROM ". $tableName);
    }
  }

  /**
   * [Bring a single value from the database]
   * @param string $query
   * @return single record
   */
  public function ExecuteScalar($query){
    if($query != ""){
      if($this->status == self::CLOSE) $this->open();

      $result = mysqli_query($this->conn, $query) or die (mysqli_error());

      $record = mysqli_fetch_array($result);
      return $record[0];
    } else {
      echo "param is required.";
    }
  }

  /**
   * [Returns the number of records in a table]
   * @function
   * @param string $tableName
   * @return N records
   */
  public function RecordCount($tableName){
    if($tableName != ""){
      return $this->ExecuteScalar("SELECT COUNT(*) FROM ". $tableName);
    } else {
      echo "param is required.";
    }
  }
}
?>