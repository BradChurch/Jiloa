<?php  $pt = "POC Result Entry Add"; ?>
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>

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
} ?>
<?php $saved = ""; ?>

<?php if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {?>

<?php  $updateSQL = sprintf("UPDATE results SET result=%s WHERE id=%s",
                       GetSQLValueString($_POST['result'], "text"),
                       GetSQLValueString($_POST['rid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

  $saved = 'true'; // triggers <body onload="out()"  below to close the edit window and refresh the calling page with the new data

?>


<?php  }?>

<!--end of action *****************************************************************
***********************************************************************************
begin display ******************************************************************-->

<?php
mysql_select_db($database_swmisconn, $swmisconn);
$query_result = "SELECT r.result, r.normflag, r.entrydt, t.formtype, t.ddl FROM results r join tests t on t.id = r.testid WHERE r.id = '".$_GET['rid']."'";
$result = mysql_query($query_result, $swmisconn) or die(mysql_error());
$row_result = mysql_fetch_assoc($result);
$totalRows_result = mysql_num_rows($result);


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Delete POCT</title>

<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
		function openBrWindow(theURL,winName,features) { //v2.0
			window.open(theURL,winName,features);
		}
		function out(){
			opener.location.reload(1); //This updates the data on the calling page
				self.close();
		}
		function MM_closeBrWindow() { // this works too
			window.close(); 
		}
</script>


</head>

<?php if($saved == "true") {?>
<body onload="out()">
<?php }?>
<body>

<!--Edit POCT rid <?php //echo $_GET['rid'] ?><br />&test=<?php //echo $_GET['test'];?>-->
<p></p>
<p></p>
<table width="200" border="1" align="center" bgcolor="#FFFDDA">
<form name="form1" method="post" action="<?php echo $editFormAction; ?>">  <tr>
    <td colspan="2" align="center" class="BlueBold_16">EDIT RESULTS</td>
  </tr>
  <tr>
    <td align="center">Test: </td>
    <td>Result:</td>
  </tr>
  <tr>
    <td><?php echo $_GET['test'];?></td>
<?php if($row_result['formtype'] == 'TextField'){ ?>
    <td><input name="result" type="text" value="<?php echo $row_result['result'] ?>"></td>

<?php } else if ($row_result['formtype'] == 'DropDown') { 
		mysql_select_db($database_swmisconn, $swmisconn);
		$query_ddl = "Select list, name, seq from dropdownlist where list = '".$row_result['ddl']."' Order By seq";
		$ddl = mysql_query($query_ddl, $swmisconn) or die(mysql_error());
		$row_ddl = mysql_fetch_assoc($ddl);
		$totalRows_ddl = mysql_num_rows($ddl);
?>

   <td><select name="result">
  <?php do { ?>
	   <option value="<?php echo $row_ddl['name']?>"<?php if (!(strcmp($row_ddl['name'], $row_result['result']))) {echo "selected=\"selected\"";} ?>><?php echo $row_ddl['name']?></option>
  <?php
} while ($row_ddl = mysql_fetch_assoc($ddl));
  $rows = mysql_num_rows($ddl);
  if($rows > 0) {
      mysql_data_seek($ddl, 0);
	  $row_ddl = mysql_fetch_assoc($ddl);
	}
?>
</select></td>

<?php } ?>
  </tr>
  <tr>
    <td><input name="button" style="background-color:#f81829" type="button" onclick="out()" value="Close" /></div></td>
    <td align="right"><input type="submit" name="submit" id="submit" value="SAVE"></td>
  </tr>
    <input type="hidden" name="rid" value="<?php echo $_GET['rid'] ?>" />
    <input type="hidden" name="MM_update" value="form1" />

 </form>
 </table>

</body>
</html>