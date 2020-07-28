<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>
<?php //require_once('../../Connections/swmisconn.php'); ?>
<?php //session_start()?>

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

	$editFormAction = $_SERVER['PHP_SELF'];
	if (isset($_SERVER['QUERY_STRING'])) {
		$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
  if ((isset($_POST["MM_Update"])) && ($_POST["MM_Update"] == "formrx3")) {

/*mysql_select_db($database_swmisconn, $swmisconn);
$query_myfee = "Select fee from fee where id = ".$_POST['feeid']."";
$myfee = mysql_query($query_myfee, $swmisconn) or die(mysql_error());
$row_myfee = mysql_fetch_assoc($myfee);
$totalRows_myfee = mysql_num_rows($myfee);
*/
/*	if($row_myfee['fee'] == -1) {
		$thisofee = ($_POST['ofee']	* -1);
		$mystatus = "Refund";			
	}  else {
		$thisofee = $_POST['ofee'];
		$mystatus = "ordered";
	}
*/
  $updateSQL = sprintf("UPDATE orders SET status=%s, billstatus=%s, amtdue=%s, entryby=%s, entrydt=%s, comments=%s WHERE id=%s",
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['billstatus'], "text"),
GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['ordid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());
  $saved = "true";
}

 $_SESSION['CurrDateTime'] =  date("Y-m-d H:i:s"); 
 ?>

<!--query for an order record-->
<?php if(isset($_GET['ordid'])) {  
  $colname_ordid = (get_magic_quotes_gpc()) ? $_GET['ordid'] : addslashes($_GET['ordid']);

mysql_select_db($database_swmisconn, $swmisconn);
$query_editdel = "SELECT o.id, o.medrecnum, o.visitid, o.feeid, o.item, o.quant, o.ofee, o.rate, o.doctor, substr(o.status,1,7) status, o.comments, o.urgency, DATE_FORMAT(o.entrydt,'%d%b%y %H:%i') entrydt, o.entryby, Format(f.fee*(o.rate/100),0) as amtdue, o.amtpaid, f.dept, f.section, f.name, f.descr, f.fee FROM orders o, fee f WHERE o.feeid = f.id and f.dept in ('pharm', 'admin') and o.id = '". $colname_ordid ."'";
$editdel = mysql_query($query_editdel, $swmisconn) or die(mysql_error());
$row_editdel = mysql_fetch_assoc($editdel);
$totalRows_editdel = mysql_num_rows($editdel);
	}
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
</head>
<?php if($saved == "true") {?>
	<body onload="out()">
<?php }?>

<body>

<table align="center">
  <tr>
   <td>
<form id="formrx3" name="formrx3" method="post" action="<?php echo $editFormAction; ?>"> 
    <table border="1" align="center"  bgcolor="#F8FDCE">
   
     <tr>
       <td colspan="4" nowrap="nowrap" class="BlueBold_20" title="Order status will become RxReferred &#10;Billing status will become Cancelled"><div align="center">REFER A DRUG </div></td>
       </tr>
     <tr>
		<td colspan="2" nowrap="nowrap">Drugs/Other Orders</td>
		<td colspan="2" nowrap="nowrap">Urg:
		  <input type="text" name="urgency" id="urgency" disabled="disabled" value = "<?php echo $row_editdel['urgency']; ?>" />
          <input name="doctor" type="text" id="doctor" disabled="disabled" value="<?php echo $row_editdel['doctor']; ?>" /></td>
		</tr>
	    <tr>
		  <td colspan="2">
          <input name="name" type="text" id="name" disabled="disabled" value="<?php echo $row_editdel['name']; ?>" /></td>
		<td nowrap="nowrap">Item:
		  <input name="item" type="text" id="item" disabled="disabled" value="<?php echo $row_editdel['item']; ?>" size="30" maxlength="100" />      </td>
		<td nowrap="nowrap">Quant:
		  
		  <input name="quant" type="text" id="quant" disabled="disabled" value="<?php echo $row_editdel['quant']; ?>" size="8" />      </td>
		</tr>
	  <tr>
		<td rowspan="2" nowrap="nowrap">Order Comments:<br />Order #: <?php echo $row_editdel['id']; ?></td>
		<td colspan="2" rowspan="2" nowrap="nowrap"><textarea name="comments" cols="25" rows="3" id="comments"><?php echo $row_editdel['comments']; ?></textarea></td>
	    <td nowrap="nowrap">Naira:          
	      <input name="ofee" type="text" disabled="disabled" value="<?php echo $row_editdel['ofee']; ?>" size="8" /></td>
	    </tr>
	  <tr>
	    <td nowrap="nowrap"><input name="Submit" type="submit" id="Submit" value="Refer" /></td>
	    </tr>
	  
				<input name="medrecnum" type="hidden" id="medrecnum" value="<?php echo $_SESSION['mrn']; ?>" />
				<input name="visitid" type="hidden" id="visitid" value="<?php echo $row_editdel['visitid']; ?>; ?>" />
				<input name="billstatus" type="hidden" id="billstatus" value="RxCancelled" />	  
				<input name="status" type="hidden" id="status" value="RxReferred" />
				<input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
				<input name="entrydt" type="hidden" id="entrydt" value="<?php echo $_SESSION['CurrDateTime']; ?>" />
				<input name="ordid" type="hidden" id="ordid" value="<?php echo $_GET['ordid']; ?>" />
				<input type="hidden" name="MM_Update" value="formrx3">
	</table>
   </form>
   </td>
  </tr>
</table>
<script  type="text/javascript">
// var frmvalidator = new Validator("formplv1");
 //frmvalidator.EnableMsgsTogether();

 frmvalidator.addValidation("doctor","dontselect=Select", "Please Select Doctor");
</script>



</body>
</html>
