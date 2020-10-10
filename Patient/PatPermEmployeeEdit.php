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
$saved = "";

// check for form submitted
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2") and isset($_POST['employee'])) {
// the ddl select 'employee' will select only the medrecnum (even though the other values are displayed)
// to retrieve the group value to save in the employee record here, a query to find employeegroup value again is needed
		mysql_select_db($database_swmisconn, $swmisconn);
		$query_employeeinfo = "SELECT employeegroup FROM patperm WHERE medrecnum = '".$_POST['employee']."'" ;
		$employeeinfo = mysql_query($query_employeeinfo, $swmisconn) or die(mysql_error());
		$row_employeeinfo = mysql_fetch_assoc($employeeinfo);
		$totalRows_employeeinfo = mysql_num_rows($employeeinfo);
//echo $_POST['employee'];		
//echo $row_employeeinfo['employeemrn'];
//echo $row_employeeinfo['employeegroup'];
//exit;
//update employee infor in the dependent patient record
  $insertSQL = sprintf("UPDATE patperm SET employeemrn=%s, employeegroup=%s  WHERE medrecnum=%s",
                       GetSQLValueString($_POST['employee'], "int"),
                       GetSQLValueString($row_employeeinfo['employeegroup'], "text"),
					             GetSQLValueString($_POST['dependentmrn'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
// set value to true - used in triggering body onload="out()" which will close the pop-up page and refresh the parent page.
$saved = "true";
}

// store dependentmrn MRN for later use (set hidden value - used to save info in dependent record above
$colname_dependentmrn = "-1";
if (isset($_GET['dependentmrn'])) {
  $colname_dependentmrn = $_GET['dependentmrn'];
}
// Select list of patients with same last name as the dependent's last name for sellection in DDL
mysql_select_db($database_swmisconn, $swmisconn);
$query_employeemrn = ("SELECT medrecnum, active, lastname, firstname, othername, gender, employeegroup, employeemrn FROM patperm WHERE lastname = '".$_GET['lastname']."' and employeegroup IS NOT NULL and employeemrn IS NULL and medrecnum != '".$_GET['dependentmrn']."' ORDER BY employeegroup DESC, lastname, firstname ASC");
$employeemrn = mysql_query($query_employeemrn, $swmisconn) or die(mysql_error());
$row_employeemrn = mysql_fetch_assoc($employeemrn);
$totalRows_employeemrn = mysql_num_rows($employeemrn);
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Employee</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
function out(){
	opener.location.reload(1); //This updates the data on the calling page
	  self.close();  // used for "Close' link and <body onload="out()">
}
</script>
</head>

<?php if($saved == "true") {?>
<body onload="out()"> <!-- close the pop-up page and refresh the parent page-->
<?php }?>

<body>
<h2>For Patient: <?php echo $_GET['lastname'] ?>, <?php echo $_GET['firstname'] ?></h2>      <table bgcolor="#F8FDCE">
			<form name="form2" id="form2" method="POST" action="">
        <tr>
          <td colspan ="5">SELECT Employee:  Parent, Spouse, or Agent</td>
          <td></td>
        </tr>
        <tr>
        	<td align="center">Grp-MRN-Last, First (middle) Name</td>
        </tr>
        <tr>
          <td><select id="employee" name="employee" size="5">
						<option value=''>Clear</option>
<?php do {?>
						<option value="<?php echo $row_employeemrn['medrecnum']?>"> <?php echo $row_employeemrn['employeegroup']?> - <?php echo $row_employeemrn['medrecnum']?>: <?php echo $row_employeemrn['lastname']?>,<?php echo $row_employeemrn['firstname']?>(<?php echo $row_employeemrn['othername']?>)"
        			 </option>
            <?php
} while ($row_employeemrn = mysql_fetch_assoc($employeemrn));
  $rows = mysql_num_rows($employeemrn);
  if($rows > 0) {
      mysql_data_seek($employeemrn, 0);
	  $row_employeemrn = mysql_fetch_assoc($employeemrn);
  }
?>
          </select></td>  
          <td nowrap></td>  
        </tr>
        <tr>
          <td><input name="button" style="background-color:#f81829" type="button" onclick="out()" value="Close" /></td>
          <td align="right" nowrap><input type="submit" name="button2" id="button" value="UPDATE">
          
          <input type="hidden" name="dependentmrn" id="dependentmrn" value="<?php echo $_GET['dependentmrn']; ?>">
          <input type="hidden" name="MM_update" value="form2">
          </td>
        </tr>

			</form>
			</table>
      <p>user notes;</p>  
      <p>Only patients who have the last name of<strong> <?php echo $_GET['lastname'] ?></strong></p>
      <p>AND have been assigned a group id in the patient record</p>
      <p>will appear on this employee selection list.</p>
      <p>List is sorted by group decending, then lastname, firstname ascending.</p>
</body>
</html>
<?php
mysql_free_result($employeemrn);
?>
