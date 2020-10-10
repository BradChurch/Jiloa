<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
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

 $saved = '';

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}  

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "form1")) {

	mysql_select_db($database_swmisconn, $swmisconn);
  $updateSQL = sprintf("DELETE FROM results WHERE ordid=%s",
                       GetSQLValueString($_POST['ordid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

  $updateSQL = sprintf("DELETE FROM orders WHERE id=%s",
                       GetSQLValueString($_POST['ordid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());
 $saved = 'true'; 
}
?>

<!--end of action *****************************************************************
***********************************************************************************
begin display ******************************************************************-->

<?php
$query_result = "SELECT r.result, r.normflag, r.entrydt FROM results r join orders o on o.id = r.ordid join patvisit v on o.visitid = v.id WHERE r.ordid = '".$_GET['ordid']."'";
$result = mysql_query($query_result, $swmisconn) or die(mysql_error());
$row_result = mysql_fetch_assoc($result);
$totalRows_result = mysql_num_rows($result);


?>

<!DOCTYPE html">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Delete POCT</title>

</head>

<?php if($saved == "true") {?>
<body onload="out()">
<?php }?>

<body>

<table width="30%" border="1" align="center" cellpadding="1" cellspacing="1"  bgcolor="#FBD0D7">
 <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>"> <tr>
    <td nowrap>Delete POCT</td>
      <td class="Link"><div align="right">
        <a href="POCSelect.php">
        <input name="button" style="background-color:#f81829" type="button" onclick="out()" value="Close" /></a></div></td>
      <td align="right"><input type="submit" name="submit" id="submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="Delete POCT" /></td>

  </tr>
 <tr>
      	<td align="center">Result</td>
      	<td align="center">Flag</td>
      	<td align="center" nowrap>Test Date/time</td>
      </tr>
   <?php do { ?>
    	<tr>
        <td align="center" nowrap><?php echo $row_result['result']; ?></td>
       	<td align="center" nowrap><?php echo $row_result['normflag']; ?></td>   
      	<td align="center" nowrap><?php echo $row_result['entrydt']; ?></td>
  </tr>
   <?php } while ($row_result = mysql_fetch_assoc($result)); ?>
			   <input name="ordid" type="hidden" value="<?php echo $_GET['ordid'] ?>" />
			   <input type="hidden" name="MM_delete" value="form1">

</form>
  </table>
</body>
<script language="JavaScript" type="text/JavaScript">
function out(){
	opener.location.reload(1); //This updates the data on the calling page
	  self.close();
}
 </script>

</html>