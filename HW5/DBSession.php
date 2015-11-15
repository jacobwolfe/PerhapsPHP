<?php
include 'HW5/Controllers/DatabaseConnection.php';
include 'HW5/Models/SessionVar.php';
Class DBSession {

public $sessionName = "";
public $sessionVars;

function __construct($sesName) {
  $this->sessionName = $sesName;
  $this->getSessionVar();
}

public function getSessionVar(){
  $sessionVars = [];

  $sql_select = "SELECT sessionName, varName, varValue, LastUpdate
  FROM sessionvar
  WHERE sessionName = :curSessionName";
  $stmt = $conn->prepare($sql_select);
  $stmt->bindParam(':curSessionName', $this->sessionName, PDO::PARAM_STR);
  $stmt->execute();
  $returnedList = $stmt->fetchAll();

  if(count($returnedList)>0){
    foreach($returnedList as $var) {
        $sessionvar = new SessionVar($var['sessionName']);
        $sessionvar->varName = $var['varName'];
        $sessionvar->varValue = $var['varValue'];
        $sessionvar->LastUpdate = $var['LastUpdate'];
        $sessionVars[] = $sessionvar;
    }
  }
  $this->$sessionVars = $sessionVars;
  return $sessionVars;
}

public function insertvar($varName, $varValue){
	date_default_timezone_set('American/Chicago');
	$sql_insert = "Insert into sessionvar Values(:sessionName, :varName, :varValue ,getdate())";
	try
	{
    $stmt = $conn->prepare($sql_insert);
    $stmt->bindParam(':curSessionName', $this->sessionName, PDO::PARAM_STR);
    $stmt->execute();
    $this->getSessionVar();
    echo "<p>Insert Success.</p>";
	}
	catch(Exeception $e) {
		echo "<p>Insert Failed.</p>";
	}
}

public function varValue($varName){
  $valueList = $this->sessionVars;
  foreach ($valueList as $sessionVar) {
    if($sessionVar->varName == $varName){
      return $sessionVar->varValue;
    }
  }
  return null;
}

public function updateVal($varName, $varVal) {
	date_default_timezone_set('American/Chicago');

  $sql_update = "UPDATE sessionvar
  SET varValue= :varVal, LastUpdate = getdate()
  WHERE sessionName= :curSessionName AND varName = :varName";

  try
	{
    $stmt = $conn->prepare($sql_update);
    $stmt->bindParam(':varVal', $varVal, PDO::PARAM_STR);
    $stmt->bindParam(':curSessionName', $this->sessionName, PDO::PARAM_STR);
    $stmt->bindParam(':varName', $varName, PDO::PARAM_STR);
    $stmt->execute();
    $this->getSessionVar();
    echo "<p>Update Success.</p>";
	}
	catch(Exeception $e) {
		echo "<p>Update Failed.</p>";
	}
}

public function deleteVar($varName){
	$sql_delete = "DELETE FROM sessionvar
  WHERE sessionName = :curSessionName AND varName = :varName";

  try
	{
    $stmt = $conn->prepare($sql_delete);
    $stmt->bindParam(':curSessionName', $this->sessionName, PDO::PARAM_STR);
    $stmt->bindParam(':varName', $varName, PDO::PARAM_STR);
    $stmt->execute();
    $this->getSessionVar();
    echo "<p>Delete Success.</p>";
	}
	catch(Exeception $e) {
		echo "<p>Delete Failed.</p>";
	}
}

  public printVars(){
    $varList = $this->getSessionVar();
    if(count($varList)>0){
      foreach($varList as $var) {
        echo '
        <p> '.$var->toString().'</p>';
      }
    }
    else {
      echo '
      <p>No session variables set</p>';
    }
  }
}
?>
