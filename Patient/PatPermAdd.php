<?php ob_start(); ?>
<?php  $pt = "Add Patient"; ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/Len/Jiloa/Master/Header.php'); ?> 
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>
<?php $_SESSION['today'] = date("Y-m-d");  // H:i:s ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_last = "-1";
if ((isset($_POST['lastname'])) && (isset($_POST['firstname']))) {
  $colname_last = (get_magic_quotes_gpc()) ? $_POST['lastname'] : addslashes($_POST['lastname']);
  $colname_first = (get_magic_quotes_gpc()) ? $_POST['firstname'] : addslashes($_POST['firstname']);

mysql_select_db($database_swmisconn, $swmisconn);
$query_dupchk = sprintf("SELECT medrecnum, lastname, firstname, othername, gender, ethnicgroup, dob, est FROM patperm WHERE lastname = '%s' and firstname = '%s'", $colname_last, $colname_first);
$dupchk = mysql_query($query_dupchk, $swmisconn) or die(mysql_error());
$row_dupchk = mysql_fetch_assoc($dupchk);
$totalRows_dupchk = mysql_num_rows($dupchk);
	
	if ($totalRows_dupchk == 0) {  // if name does not already exist, continue

if ((isset($_POST['dob']) && $_POST['dob'] > '1914-01-01')  || (isset($_POST['age']) && $_POST['age'] > 0)) {   //  DOB or AGE CHECK

  if (isset($_POST['dob'])  AND strlen($_POST['dob'])>1) {
		$calcdob = $_POST['dob'];
		$est = "N";
	}
	else {
		if (isset($_POST['age']) && $_POST['age'] > 0) {
//			$calcdob = "2013-12-11";
			$calcdob = Date('Y-m-d', strtotime("- ".$_POST['age']." years"));
			$est = "Y";
			}
		else {
	//		if (isset($_POST['age'])) {
				$calcdob = "0000-00-00";
	//			$calcdob = Date('Y-m-d', strtotime("- ".$_POST['age']." years"));
			$est = "Y";
			}
		}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formppa")) {
  $insertSQL = sprintf("INSERT INTO patperm (hospital, active, entrydt, entryby, lastname, firstname, othername, gender, ethnicgroup, employeegroup, dob, est) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['hospital'], "text"),
                       GetSQLValueString($_POST['active'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['lastname'], "text"),
                       GetSQLValueString($_POST['firstname'], "text"),
                       GetSQLValueString($_POST['othername'], "text"),
                       GetSQLValueString($_POST['gender'], "text"),
                       GetSQLValueString($_POST['ethnicgroup'], "text"),
                       GetSQLValueString($_POST['employeegroup'], "text"),
                       GetSQLValueString($calcdob, "date"),
                       GetSQLValueString($est, "text"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());


$colname_NewMRN = "-1";
if ((isset($_POST['lastname'])) && (isset($_POST['firstname']))) {
  $colname_NewMRN = (get_magic_quotes_gpc()) ? $_POST['lastname'] : addslashes($_POST['lastname']);
  $colname_NewMRN1 = (get_magic_quotes_gpc()) ? $_POST['firstname'] : addslashes($_POST['firstname']);
}
mysql_select_db($database_swmisconn, $swmisconn);
$query_NewMRN = sprintf("SELECT MAX(medrecnum) medrecnum FROM patperm WHERE lastname = '%s' and firstname = '%s'", $colname_NewMRN, $colname_NewMRN1);
$NewMRN = mysql_query($query_NewMRN, $swmisconn) or die(mysql_error());
$row_NewMRN = mysql_fetch_assoc($NewMRN);
$totalRows_NewMRN = mysql_num_rows($NewMRN);

mysql_select_db($database_swmisconn, $swmisconn);
$query_Fee = sprintf("SELECT fee from fee where name = 'Initial Registration'");
$Fee = mysql_query($query_Fee, $swmisconn) or die(mysql_error());
$row_Fee = mysql_fetch_assoc($Fee);
$totalRows_Fee = mysql_num_rows($Fee);

$amtdue = $row_Fee['fee']*($_POST['rate']/100); 	

// Add billing
  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, comments, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       $row_NewMRN['medrecnum'],
                       0,
                       19,
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "int"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

		  mysql_select_db($database_swmisconn, $swmisconn);
		  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

	echo "Rate ".$_POST['rate']. "     ". "Reason ".$_POST['ratereason'];
  $insertGoTo = "PatShow1.php?mrn=".$row_NewMRN['medrecnum'];
	mysql_free_result($NewMRN);

         //mysql_free_result($reason);

  header(sprintf("Location: %s", $insertGoTo));
      } // if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formppa"))
    }  // DOB or Age check
  }  //if ($totalRows_dupchk == 0
  Else { // if name does already exists
  
   		$insertGoTo = "PatPermMatch.php?lastname=".$_POST['lastname']."&firstname=".$_POST['firstname']."&othername=".$_POST['othername']."&gender=".$_POST['gender']."&ethnicgroup=".$_POST['ethnicgroup']."&dob=".$_POST['dob']."&age=".$_POST['age']."&rate=".$_POST['rate']."&ratereason=".$_POST['ratereason'];
  		header(sprintf("Location: %s", $insertGoTo));
   }
} // if (isset($_POST['lastname'])) && (isset($_POST['firstname']))
?>
<?php 
		mysql_select_db($database_swmisconn, $swmisconn);
		$query_reason = "Select id, list, name, seq from dropdownlist where list = 'Rate Reason' Order By seq";
		$reason = mysql_query($query_reason, $swmisconn) or die(mysql_error());
		$row_reason = mysql_fetch_assoc($reason);
		$totalRows_reason = mysql_num_rows($reason);
?>
<?php 	mysql_select_db($database_swmisconn, $swmisconn);
		$query_ethnicddl = "Select list, name, seq from dropdownlist where list = 'Ethnic Group' Order By seq";
		$ethnicddl = mysql_query($query_ethnicddl, $swmisconn) or die(mysql_error());
		$row_ethnicddl = mysql_fetch_assoc($ethnicddl);
		$totalRows_ethnicddl = mysql_num_rows($ethnicddl);
?>

<?php 	mysql_select_db($database_swmisconn, $swmisconn);
		$query_employeeddl = "Select list, name, seq from dropdownlist where list = 'Employee Group' Order By seq";
		$employeeddl = mysql_query($query_employeeddl, $swmisconn) or die(mysql_error());
		$row_employeeddl = mysql_fetch_assoc($employeeddl);
		$totalRows_employeeddl = mysql_num_rows($employeeddl);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../javascript_form/gen_validatorv4.js" type="text/javascript" xml:space="preserve"></script>
</head>

<body onLoad="document.forms.formppa.lastname.focus()">
<table width="40%" border="0" align="center">
  <caption align="top" class="subtitlebl">
    ADD PATIENT PERMANENT DATA
  </caption>
  <tr>
    <td>
	<form name="formppa" method="POST" action="<?php echo $editFormAction; ?>">
	 <table width="100%" border="1" bgcolor="#BCFACC">
  <tr>
    <td class="sidebar">MRN, entry date, enteredby, <br />
      hospital, and Active are<br />
      added automatically </td>
    <td><p>Enter 1st character of name in upper case<br /> 
      and remainder in lowercase...no spaces. </p>
      <p>&nbsp;
<?php if(isset($_POST['dob'])) { echo "dob:".$_POST['dob'];  }
	  if(isset($_POST['age'])) { echo "age:".$_POST['age'];  } ?></p></td>
    </tr>
  <tr>
    <td class="subtitlebl" div align="right">Last Name:</td>
    <td title="Required: alphabetic or numeric characters only - no spaces"><input name="lastname" type="text" id="lastname"  autocomplete="off"/>    </td>
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
    <td title="Required!"><select name="ethnicgroup" id="ethnicgroup" type="text" >
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
    </select>    </td>
    </tr>

  <tr>
    <td class="subtitlebl" align="right">Employee Group:</td>
    <td title="e.g PH = Peace House, CA = CalvaryArrows"><select name="employeegroup" id="employeegroup" type="text" >
	<option value="0">None</option>
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
    If this is a dependent, save as None and go to edit this patient to link this patient to the employee.</td>
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
    <td class="subtitlebl" align="right">Registration Fee:</td>
    <td nowrap="nowrap">Rate:
      <select name="rate" id="rate">
      <option value="200">200%</option>
      <option value="150">150%</option>
      <option value="125">125%</option>
      <option value="100" selected="selected">Standard</option>
      <option value="75">75%</option>
      <option value="50">50%</option>
      <option value="25">25%</option>
      <option value="0">None</option>
            </select>
      Rate Reason:      
      <select name="ratereason" id="ratereason">
         <option value="103">None</option>
        <?php
do {  
?>
        <option value="<?php echo $row_reason['id']?>"><?php echo $row_reason['name']?></option>
        <?php
} while ($row_reason = mysql_fetch_assoc($reason));
  $rows = mysql_num_rows($reason);
  if($rows > 0) {
      mysql_data_seek($reason, 0);
	  $row_reason = mysql_fetch_assoc($reason);
  }
?>
      </select></td>
    </tr>
<?php if(allow(18,3) == 1) {?>   <!--needs permmission to add patient-->   
  <tr>
    <td><input name="active" type="hidden" id="active" value="Y" />
      <input name="hospital" type="hidden" id="hospital" value="Bethany" />
      <input name="billstatus" type="hidden" id="billstatus" value="Due" />
      <input name="status" type="hidden" id="status" value="Registered" />
      <input name="urgency" type="hidden" id="urgency" value="Routine" />
      <input name="comments" type="hidden" id="comments" value="none" />
      <input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
      <input name="entrydt" type="hidden" id="entrydt" value="<?php echo date("Y-m-d H:i:s"); ?>" /></td>
    <td><input type="submit" name="Submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="SAVE PATIENT" /></td>
    </tr>
<?php } ?>
</table>
    <input type="hidden" name="MM_insert" value="formppa">
	</form></td>
  </tr>
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
<?php ob_end_flush();?>
