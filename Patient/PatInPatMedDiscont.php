<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>

<?php
if(!function_exists("GetSQLValueString")) {
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
}
  $saved = "";

    if(isset($_POST['ordid']) and isset($_POST['SubmitAll']) AND $_POST['SubmitAll']  = 'discontinue') {
	
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_medsched = "SELECT id, orderid, status from ipmeds where orderid = ".$_POST['ordid']." and status = 'ordered' ORDER BY schedt ASC";
	$medsched = mysql_query($query_medsched, $swmisconn) or die(mysql_error());
	$row_medsched = mysql_fetch_assoc($medsched);
	$totalRows_medsched = mysql_num_rows($medsched);

  do { 
	  $UpdateSQL = sprintf("Update ipmeds set status = %s, givenby = %s, entryby = %s, entrydt = %s, comments = %s where id = %s",
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['givenby'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($row_medsched['id'], "int"));

   mysql_select_db($database_swmisconn, $swmisconn);
   $Result1 = mysql_query($UpdateSQL, $swmisconn) or die(mysql_error());
  
  } while ($row_medsched = mysql_fetch_assoc($medsched));
  $saved = "true";
} //if form
 ?>

<?php
if (isset($_GET['ordid'])) {
  $colname_ordid = (get_magic_quotes_gpc()) ? $_GET['ordid'] : addslashes($_GET['ordid']);
mysql_select_db($database_swmisconn, $swmisconn);

$query_ordered = "SELECT o.id, o.id ordid, o.medrecnum, o.visitid, o.item, o.nunits, o.unit, o.every, o.evperiod, o.fornum, o.forperiod, o.quant, DATE_FORMAT(o.entrydt,'%d%b%y %H:%i') entrydt, o.entryby, o.amtpaid, o.comments FROM orders o WHERE o.id = ".$colname_ordid."";
$ordered = mysql_query($query_ordered, $swmisconn) or die(mysql_error());
$row_ordered = mysql_fetch_assoc($ordered);
$totalRows_ordered = mysql_num_rows($ordered);
}
?>

<?php
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_users = "Select userid from users where active = 'Y'  order by userid";
	$users = mysql_query($query_users, $swmisconn) or die(mysql_error());
	$row_users = mysql_fetch_assoc($users);
	$totalRows_users = mysql_num_rows($users);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../javascript_form/gen_validatorv4.js" type="text/javascript" xml:space="preserve"></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function out(){
	opener.location.reload(1); //This updates the data on the calling page
	  self.close();
}
//-->
</script>
<style type="text/css">
 input.center{
 text-align:center;
 }
</style>

</head>

<?php if($saved == "true") {?>
	<body onload="out()">
<?php }?>

<body>
<table align="center">
  <tr>
    <td><form id="formrxd" name="formrxd" method="post" action="PatInPatMedDiscont.php">
      <table border="1" align="center" bgcolor="#BCFACC">
        <tr>
          <td colspan="2" nowrap="nowrap"><div align="center" class="BlueBold_20">DISCONTINUE Medication </div></td>
        </tr>
        <tr>
          <td colspan="2"><div align="center" class="BlueBold_1414">Prescription</div></td>
        </tr>
        <tr>
          <td><input name="item" type="text" id="item" size="30" maxlength="100" disabled="disabled" value="<?php echo $row_ordered['item']; ?>" /></td>
          <td nowrap="nowrap"><input name="nunits" type="text" id="nunits" class="center" size="3" maxlength="3" disabled="disabled" value="<?php echo $row_ordered['nunits']; ?>" />
                <input name="unit" type="text" size="6" disabled="disabled" value="<?php echo $row_ordered['unit']; ?>" />
<?php if(is_numeric($row_ordered['every'])){?>
            Every
            <input name="every" type="text" id="every" size="3" class="center" disabled="disabled" value="<?php echo $row_ordered['every']; ?>" />
<?php }?> 
            <input name="evperiod" type="text" id="every" size="6" disabled="disabled" value="<?php echo $row_ordered['evperiod']; ?>" />
            for
            <input name="fornum" type="text" id="fornum" size="3" class="center" disabled="disabled" value="<?php echo $row_ordered['fornum']; ?>" />
            <input name="forperiod" type="text" id="forperiod" size="6" disabled="disabled" value="<?php echo $row_ordered['forperiod']; ?>" />          </td>
        </tr>
        <tr>
          <td nowrap="nowrap"><div align="right">Order Comments: </div></td>
          <td nowrap="nowrap"><textarea name="ordcomments" cols="60" rows="2" id="ordcomments" disabled="disabled" ><?php echo $row_ordered['comments']; ?></textarea></td>
        </tr>
        <tr>
          <td nowrap="nowrap"> <div align="right">Add Medication  Comments:</div></td>
          <td nowrap="nowrap"><textarea name="comments" cols="60" rows="2" id="comments"></textarea></td>
        </tr>
        <tr>
          <td colspan="3" align="right" nowrap="nowrap">Currently scheduled medications with status of 'ordered' will have 'discontinued' Status.<br />
Discontinued By:
<select name="givenby" id="givenby">
<?php do { ?>
		  <option value="<?php echo $row_users['userid']?>"<?php if (!(strcmp(trim($_SESSION['user']), $row_users['userid']))) {echo "selected=\"selected\"";} ?>><?php echo $row_users['userid']?></option>
					  <?php
		} while ($row_users = mysql_fetch_assoc($users));
		  $rows = mysql_num_rows($users);
		  if($rows > 0) {
			  mysql_data_seek($users, 0);
			  $row_users = mysql_fetch_assoc($users);
		  }
?>
            </select>
        <input name="SubmitAll" type="submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="discontinue" /></td>
          </tr>
        <input name="ordid" type="hidden" id="ordid" value="<?php echo $row_ordered['ordid']; ?>" />
        <input type="hidden" name="status" value="discontinued"/>
        <input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
        <input name="entrydt" type="hidden" id="entrydt" value="<?php echo date("Y-m-d H:i"); ?>" />
        <input type="hidden" name="MM_Update" value="formrxd" />
      </table>
    </form></td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
