<?php error_reporting(E_ALL ^ E_DEPRECATED);?>
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
<?php require_once('../../Connections/bethanyconn.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "employeesadd")) {
  $insertSQL = sprintf("INSERT INTO employee (id, type, employeegroup, lastname, firstname, othername, ethnicity, gender, dob, mrn, active, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['type'], "text"),
                       GetSQLValueString($_POST['employeegroup'], "text"),
                       GetSQLValueString($_POST['lastname'], "text"),
                       GetSQLValueString($_POST['firstname'], "text"),
                       GetSQLValueString($_POST['othername'], "text"),
                       GetSQLValueString($_POST['ethnicity'], "text"),
                       GetSQLValueString($_POST['gender'], "text"),
                       GetSQLValueString($calcdob, "date"),
                       GetSQLValueString($_POST['mrn'], "int"),
                      GetSQLValueString($_POST['active'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

  $insertGoTo = "EmployeesView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
 }
  header(sprintf("Location: %s", $insertGoTo));
  
}

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
<title>Employees Add</title>
</head>

<body>

<table align='center' width="20%" border="1" cellspacing="1" cellpadding="1">
<form action="<?php echo $editFormAction; ?>" method="POST" name="employeeadd">
  <tr>
    <td colspan="2" align='center'>ADD EMPLOYEE</td>
    </tr>
  <tr>
    <td align="right">Type:</td>
      <td><select name="type" id="type">
        <option value="employee">Employee</option>
        <option value="dependent">Dependent</option>
      </select>
  </tr>
  <tr>
      <td align="right">Employeegroup:</td>
    <td title="Required!"><select name="employeegroup" id="employeegroup" type="text" >
	<option value="">Select</option>
      <?php
do {  
?>
      <option value="<?php echo $row_employeeddl['name']?>"><?php echo $row_employeeddl['name']?></option>
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
    <td title="Required: alphabetic or numeric characters only - no spaces"><input name="lastname" type="text" id="lastname"  autocomplete="off"/></td>
    </tr>
  <tr>
    <td class="subtitlebl" div align="right">First Name:</td>
    <td title="Required: alphabetic or numeric characters only - no spaces"><input name="firstname" type="text" id="firstname"  autocomplete="off"/></td>
    </tr>
  <tr>
    <td class="subtitlebl" align="right">Other Name:</td>
    <td title="alphabetic or numeric characters only - no spaces"><input name="othername" type="text" id="othername"  autocomplete="off" data-validation="length" data-validation-length="3-30"  data-validation-optional="true" data-validation-error-msg="3 to 30 Char"/></td>
    </tr>
  <tr>
    <td class="subtitlebl" align="right">Gender (Sex):      </td>
    <td title="Required!"><select name="gender" id="gender" data-validation="required" data-validation-error-msg="Selection Required!">
      <option value="0">Select</option>
      <option value="F">Female</option>
      <option value="M">Male</option>
        </select></td>
    </tr>
  <tr>
    <td class="subtitlebl" align="right">Ethnic Group:</td>
    <td title="Required!"><select name="ethnicity" id="ethnicity" type="text" >
	<option value="0">Select</option>
      <?php
do {  
?>
      <option value="<?php echo $row_ethnicddl['name']?>"><?php echo $row_ethnicddl['name']?></option>
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
    <td nowrap="nowrap"  title="If DOB and AGE are blank, patient record will not be added&#10;DOB must have Year dash Month dash Day Format&#10;Age  must be a number between 1 and 100 &#10; Newborns or children under 1 year must have DOB"><input name="dob" type="text" id="dob" data-validation="date" data-validation-optional="true" data-validation-error-msg="Invalid date" />
      <span class="subtitlebl">      yyyy-mm-dd <br />
      </span>
      <Span>(enter age > 0 to calculate a dob)<br />
        <span class="subtitlebl">OR Age</span></span> 
      <input name="age" id="age" type="text" size="3" maxlength="3" autocomplete="off" data-validation="number" data-validation-allowing="range[1;100]" data-validation-optional="true" data-validation-error-msg="Year Number required"/> 
      <span class="RedBold_14">Not both! </span></td></tr>
  <tr>
    <td align="right">MRN:</td>
    <td align="center"> <input name="mrn" type="text" size="15" maxlength="30" autocomplete="off"/></td>
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
      <input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
    <input name="entrydt" type="hidden" id="entrydt" value="<?php echo date('Y-m-d H:i:s')?>" />
      <input type="submit" name="Submit" id="Submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="Add Employee" />
      </td>
  </tr>
  <input type="hidden" name="MM_insert" value="employeesadd" />
</form>
</table>


<script  type="text/javascript">
 var frmvalidator = new Validator("formppa");
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

 frmvalidator.addValidation("gender","dontselect=0");
 frmvalidator.addValidation("ethnicgroup","dontselect=0");

</script>
<script src="../../jquery-1.11.1.js"></script>
<script src="../../jQuery-Form-Validator-master/form-validator/jquery.form-validator.min.js"></script>
<script>

/* important to locate this script AFTER the closing form element, so form object is loaded in DOM before setup is called */
   $.validate({
		//modules : 'date, security'
    });</script>

</body>
</html>
