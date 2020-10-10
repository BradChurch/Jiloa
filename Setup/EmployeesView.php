<?php require_once('../../Connections/swmisconn.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_swmisconn, $swmisconn);
$query_employeeview = "SELECT id, type, employeegroup, lastname, firstname, othername, ethnicity, gender, DATE_FORMAT(dob,'%d-%b-%Y') dob, mrn, active, entrydt, entryby FROM employee";
$employeeview = mysql_query($query_employeeview, $swmisconn) or die(mysql_error());
$row_employeeview = mysql_fetch_assoc($employeeview);
$totalRows_employeeview = mysql_num_rows($employeeview);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<table>
  <tr>
    <td><a href="SetUpMenu.php" title='SetUpMenu.php'>Menu</a></td>
    <td align="right"><a href="employeesAdd.php" title="employeesAdd.php">Add</a></td>
  </tr>
</table>
<p>&nbsp;</p>
<table border="1">
  <tr>
    <td>Edit</td>
    <td>id</td>
    <td>type</td>
    <td>employeegroup</td>
    <td>lastname</td>
    <td>firstname</td>
    <td>othername</td>
    <td>ethnicity</td>
    <td>gender</td>
    <td>dob</td>
    <td>mrn</td>
    <td>active</td>
    <td>entrydt</td>
    <td>entryby</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><a href="EmployeesEdit.php?id=<?php echo $row_employeeview['id']; ?>">E</a></td>
      <td><?php echo $row_employeeview['id']; ?></td>
      <td><?php echo $row_employeeview['type']; ?></td>
      <td><?php echo $row_employeeview['employeegroup']; ?></td>
      <td><?php echo $row_employeeview['lastname']; ?></td>
      <td><?php echo $row_employeeview['firstname']; ?></td>
      <td><?php echo $row_employeeview['othername']; ?></td>
      <td><?php echo $row_employeeview['ethnicity']; ?></td>
      <td><?php echo $row_employeeview['gender']; ?></td>
      <td><?php echo $row_employeeview['dob']; ?></td>
      <td><?php echo $row_employeeview['mrn']; ?></td>
      <td><?php echo $row_employeeview['active']; ?></td>
			<?php $date=date_create($row_employeeview['entrydt']); ?>
      <td><?php echo  date_format($date,"Y/m/d") ?></td>
      <td><?php echo $row_employeeview['entryby']; ?></td>
    </tr>
    <?php } while ($row_employeeview = mysql_fetch_assoc($employeeview)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($employeeview);
?>
<?php

?>