<?php require_once('../../Connections/swmisconn.php'); ?>
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
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
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if(isset($_POST['MM_update']) && $_POST['MM_update']== 'employee') {
// if there is a value entered in the dob input
if (isset($_POST['dob'])  AND strlen($_POST['dob'])>1) {
		$calcdob = $_POST['dob'];
		$est = "N";
	}
	else {
// if ther is a value in the age input
		if (isset($_POST['age'])) {
//			$calcdob = "2013-12-11";
			$calcdob = Date('Y-m-d', strtotime("- ".$_POST['age']." years"));
			$est = "Y";
		}
	}

  $updateSQL = sprintf("UPDATE employee SET type=%s, active=%s, entrydt=%s, entryby=%s, mrn=%s, lastname=%s, firstname=%s, othername=%s, gender=%s, ethnicity=%s, dob=%s, est=%s, employeegroup=%s  WHERE id=%s",
                       GetSQLValueString($_POST['type'], "text"),
                       GetSQLValueString($_POST['active'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['mrn'], "int"),
                       GetSQLValueString($_POST['lastname'], "text"),
                       GetSQLValueString($_POST['firstname'], "text"),
                       GetSQLValueString($_POST['othername'], "text"),
                       GetSQLValueString($_POST['gender'], "text"),
                       GetSQLValueString($_POST['ethnicity'], "text"),
                       GetSQLValueString($calcdob, "date"),
                       GetSQLValueString($est, "text"),
                       GetSQLValueString($_POST['employeegroup'], "text"),
					   GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

 $updateGoTo = "EmployeesView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
 }
  header(sprintf("Location: %s", $updateGoTo));
}
mysql_select_db($database_swmisconn, $swmisconn);
$query_employee = "SELECT id, type, employeegroup, lastname, firstname, othername, ethnicity, gender, dob, mrn, active, entrydt, entryby FROM employee WHERE id= '".$_GET['id']."'";
$employee = mysql_query($query_employee, $swmisconn) or die(mysql_error());
$row_employee = mysql_fetch_assoc($employee);
$totalRows_employee = mysql_num_rows($employee);
?>

<?php 	mysql_select_db($database_swmisconn, $swmisconn);
		$query_ethnicddl = "Select list, name, seq from dropdownlist where list = 'Ethnic Group' Order By seq";
		$ethnicddl = mysql_query($query_ethnicddl, $swmisconn) or die(mysql_error());
		$row_ethnicddl = mysql_fetch_assoc($ethnicddl);
		$totalRows_ethnicddl = mysql_num_rows($ethnicddl);
?>
<?php 	mysql_select_db($database_swmisconn, $swmisconn);
		$query_employeeddl = "SELECT list, name, seq FROM dropdownlist WHERE list = 'employee Group' ORDER BY seq ASC";
		$employeeddl = mysql_query($query_employeeddl, $swmisconn) or die(mysql_error());
		$row_employeeddl = mysql_fetch_assoc($employeeddl);
		$totalRows_employeeddl = mysql_num_rows($employeeddl);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<table align='center' width="20%" border="1" cellspacing="1" cellpadding="1">
  <form name="employee"  method="POST"action="<?php echo $editFormAction; ?>">
  <tr>
    <td colspan="2" align='center'>EDIT EMPLOYEE</td>
    </tr>
  <tr>
     <td align="right">Type:</td>
      <td><select name="type" id="type">
        <option value="" <?php if (!(strcmp("", $row_employee['type']))) {echo "selected=\"selected\"";} ?>>Select</option>
        <option value="employee" <?php if (!(strcmp("employee", $row_employee['type']))) {echo "selected=\"selected\"";} ?>> Employee</option>
        <option value="dependent" <?php if (!(strcmp("dependent", $row_employee['type']))) {echo "selected=\"selected\"";} ?>> Dependent</option>
      </select>
  </tr>
  <tr>
      <td align="right">Employeegroup:</td>
    <td title="Required!"><select name="employeegroup" id="employeegroup" type="text" >
	<option value="" <?php if (!(strcmp("", $row_employee['employeegroup']))) {echo "selected=\"selected\"";} ?>>Select</option>
      <?php
do {  
?>
      <option value="<?php echo $row_employeeddl['name']?>"  <?php if (!(strcmp($row_employeeddl['name'], $row_employee['employeegroup']))) {echo "selected=\"selected\"";} ?>  ><?php echo $row_employeeddl['name']?></option>
      <?php
} while ($row_employeeddl = mysql_fetch_assoc($employeeddl));
  $rows = mysql_num_rows($employeeddl);
  if($rows > 0) {
      mysql_data_seek($employeeddl, 0);
	  $row_employeeddl = mysql_fetch_assoc($employeeddl);
  }
?>
    </select>
    </td>
  </tr>
  <tr>
    <td class="subtitlebl" div align="right">Last Name:</td>
    <td><input name="lastname" type="text" id="lastname"  autocomplete="off" value="<?php echo $row_employee['lastname'] ?>"/>    </td>
    </tr>
  <tr>
    <td class="subtitlebl" div align="right">First Name:</td>
    <td><input name="firstname" type="text" id="firstname"  autocomplete="off" value="<?php echo $row_employee['firstname'] ?>"/></td>
    </tr>
  <tr>
    <td class="subtitlebl" align="right">Other Name:</td>
    <td><input name="othername" type="text" id="othername"  autocomplete="off" value="<?php echo $row_employee['othername'] ?>"/></td>
    </tr>
  <tr>
    <td class="subtitlebl" align="right">Gender (Sex): </td>
    <td>
    <select name="gender" id="gender">
        <option value="" <?php if (!(strcmp("", $row_employee['gender']))) {echo "selected=\"selected\"";} ?>>Select</option>
        <option value="F" <?php if (!(strcmp("F", $row_employee['gender']))) {echo "selected=\"selected\"";} ?>> F</option>
        <option value="M" <?php if (!(strcmp("M", $row_employee['gender']))) {echo "selected=\"selected\"";} ?>> M</option>
      </select>
      </td>
    </tr>
  <tr>
    <td class="subtitlebl" align="right">Ethnic Group:</td>
    <td title="Required!"><select name="ethnicity" id="ethnicity" type="text" >
    <option value="" <?php if (!(strcmp("", $row_employee['ethnicity']))) {echo "selected=\"selected\"";} ?>>Select</option>
      <?php
do {  
?>
      <option value="<?php echo $row_ethnicddl['name']?>"  <?php if (!(strcmp($row_ethnicddl['name'], $row_employee['ethnicity']))) {echo "selected=\"selected\"";} ?>  ><?php echo $row_ethnicddl['name']?></option>
      <?php
} while ($row_ethnicddl = mysql_fetch_assoc($ethnicddl));
  $rows = mysql_num_rows($ethnicddl);
  if($rows > 0) {
      mysql_data_seek($ethnicddl, 0);
	  $row_ethnicddl = mysql_fetch_assoc($ethnicddl);
  }
?>
    </select>
    </td>
  </tr>
  <tr>
                 <td class="subtitlebl" align="right" title="If DOB and AGE are blank, patient record will not be added&#10;DOB must have Year dash Month dash Day Format&#10;Age must be a number between 1 and 100 &#10; Newborns or children under 1 year must have DOB">Date of Birth:</td>
                <td nowrap="nowrap" title="If DOB and AGE are blank, patient record will not be added&#10;DOB must have Year dash Month dash Day Format&#10;Age must be a number between 1 and 100 &#10; Newborns or children under 1 year must have DOB">
                  <input name="dob" type="text" id="dob" autocomplete="off" data-validation="date" data-validation-optional="true" value="<?php echo $row_employee['dob']; ?>" />
                <span class="subtitlebl">      YYYY-MM-DD <br />
                </span>
                 <Span>(enter age to calculate a dob)<br />
                   or Age</span> 
                <input name="age" type="text" size="3" maxlength="3" autocomplete="off" data-validation-allowing="range[1;100]" data-validation-optional="true" />&nbsp;&nbsp;&nbsp; 
                (Delete date  if age is entered.)  </td>
  </tr>
  <tr>
    <td align="right">MRN:</td>
    <td align="center" title ="Employee ID: <?php echo $row_employee['id'] ?>">
    <input name="mrn" type="text" autocomplete="off" value= <?php echo $row_employee['mrn'] ?> />
    </td>
  </tr>
  <tr>
    <td align="right">Active:</td>
    <td>
      <select name="active" id="active">
        <option value="Y">YES</option>
        <option value="N">NO</option>
      
      </select>
    </td>
    
  </tr>
  <tr>
    <td><a href="employeesView.php" title="employeesView.php">Close</a></td>
    <td align="right">
    <input name="id" type="hidden" value="<?php echo $row_employee['id']; ?>" />
    <input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
    <input name="entrydt" type="hidden" value="<?php echo date('Y-m-d H:i:s');?>" />
      <input type="submit" name="Submit" id="Submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="Edit Employee" />
      </td>
  </tr>
  <input type="hidden" name="MM_update" value="employee" />
</form>
</table>

<script  type="text/javascript">
 var frmvalidator = new Validator("formppe");
 frmvalidator.EnableMsgsTogether();
 
 frmvalidator.addValidation("lastname","req","Please enter patient Last Name");
 frmvalidator.addValidation("lastname","maxlen=30", "Max length for LastName is 30");
 frmvalidator.addValidation("lastname","alnum", "alphabetic or numeric characters only - no spaces");

 frmvalidator.addValidation("firstname","req","Please enter patient First Name");
 frmvalidator.addValidation("firstname","maxlen=30", "Max length for FirstName is 30");
 frmvalidator.addValidation("firstname","alnum", "alphabetic or numeric characters only - no spaces");

 //frmvalidator.addValidation("othername","req","Please enter patient Other Name");
 frmvalidator.addValidation("othername","maxlen=30", "Max length for Other Name is 30");
 frmvalidator.addValidation("othername","alnum", "alphabetic or numeric characters only - no spaces");

 frmvalidator.addValidation("gender","dontselect=0", "Please Select Gender");
 frmvalidator.addValidation("ethnicgroup","dontselect=0", "Please Select Ethnic Group");

</script>
<script src="../../jquery-1.11.1.js"></script>
<script src="../../jQuery-Form-Validator-master/form-validator/jquery.form-validator.min.js"></script>
<script>

/* important to locate this script AFTER the closing form element, so form object is loaded in DOM before setup is called */
   $.validate({
		//modules : 'date, security'
    });</script>

<script src="../../jquery-1.11.1.js"></script>
<script src="../../jQuery-Form-Validator-master/form-validator/jquery.form-validator.min.js"></script>
<script>

/* important to locate this script AFTER the closing form element, so form object is loaded in DOM before setup is called */
    $.validate({
		//modules : 'date, security'
    });</script>

</body>
</html>
<?php
mysql_free_result($employee);
?>
