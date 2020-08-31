<?php error_reporting(E_ALL ^ E_DEPRECATED);?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
// check that form was submitted and the start date and time are entered
if (isset($_POST['SubmitAll']) AND $_POST['SubmitAll']  == 'Re-Schedule' AND !empty($_POST['scheddt']) AND !empty($_POST['schedtime']))  {
	$schedt = strtotime($_POST['scheddt'].' '.$_POST['schedtime']);
	$interval = 0;
 if(isset($schedt)) {
	if($_POST['every'] == 'tab'){
		if(isset($_POST['evperiod'])) {  // calculate interval in seconds
		   if($_POST['evperiod'] == 'OD') {
				$interval = 60 * 60 * 24; }
		   if($_POST['evperiod'] == 'Nocte') {
				$interval = 60 * 60 * 24; }
		   if($_POST['evperiod'] == 'BD') {
				$interval = 60 * 60 * 12; }
		   if($_POST['evperiod'] == 'TDS') {
				$interval = 60 * 60 * 8; }
		   if($_POST['evperiod'] == 'QDS') {
				$interval = 60 * 60 * 6; }
		   if($_POST['evperiod'] == 'PRN') {
				$interval = 60 * 60 * 24; }
		   if($_POST['evperiod'] == 'STAT') {
				$interval = 60 * 60 * 24; }
		} 
	
	} else {
		//echo 'Begin: '.$begin. ' - '. date('Y-m-d h:i', $begin)."<br />";
		if(isset($_POST['evperiod'])) {  // calculate interval in seconds
		   if($_POST['evperiod'] == 'minutes') {
				$interval = $_POST['every'] * 60; }
		   if($_POST['evperiod'] == 'hour(s)') {
				$interval = $_POST['every'] * 60 * 60; }
		   if($_POST['evperiod'] == 'day(s)') {
				$interval = $_POST['every'] * 60 * 60 * 24; }
		   if($_POST['evperiod'] == 'week(s)') {
				$interval = $_POST['every'] * 60 * 60 * 24 * 7; }
		} 
	}
	////echo 'end: '.$end. ' - '. date('Y-m-d h:i', $end)."<br />"."<br />";
	//	for ($dt = $begin; $dt < $end; $dt+=($interval)) {
 			////echo 'med: '.$dt . ' - '. date('Y-m-d h:i', $dt)."<br />";
	//// add to ipmeds table   id, visitid, orderid, med, status, unit, nunits, schedt, comments, givenby, entryby, entrydt }

mysql_select_db($database_swmisconn, $swmisconn);
$query_reorder = "SELECT id medid, orderid, status, schedt, comments FROM ipmeds WHERE orderid ='".$_POST['ordid']."' and status = 'ordered'";
$reorder = mysql_query($query_reorder, $swmisconn) or die(mysql_error());
$row_reorder = mysql_fetch_assoc($reorder);
$totalRows_reorder = mysql_num_rows($reorder);

$dt = $schedt;

do {
  $UpdateSQL = sprintf("UPDATE ipmeds SET schedt=%s, entryby=%s, entrydt=%s, comments=%s WHERE id=%s",
                       GetSQLValueString($dt, "int"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['comments'], "text"),
											 GetSQLValueString($row_reorder['medid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($UpdateSQL, $swmisconn) or die(mysql_error());
	$dt = $dt + $interval;
//echo 'ok';
//exit;  
        } while ($row_reorder = mysql_fetch_assoc($reorder));
 

  $saved = "true";
 }  //if schedt
} //if form

 ?>

<?php
if (isset($_SESSION['vid'])) {
  $colname_vid = (get_magic_quotes_gpc()) ? $_SESSION['vid'] : addslashes($_SESSION['vid']);
	}
if (isset($_GET['ordid'])) {
  $colname_ordid = (get_magic_quotes_gpc()) ? $_GET['ordid'] : addslashes($_GET['ordid']);
	}
mysql_select_db($database_swmisconn, $swmisconn);
$query_ordered = "SELECT o.id, o.id ordid, o.medrecnum, o.visitid, o.item, o.nunits, o.unit, o.every, o.evperiod, o.fornum, o.forperiod, o.quant, DATE_FORMAT(o.entrydt,'%d%b%y %H:%i') entrydt, o.entryby, o.amtpaid, o.comments FROM orders o WHERE o.visitid ='".$colname_vid."' AND o.id ='".$colname_ordid."'";
$ordered = mysql_query($query_ordered, $swmisconn) or die(mysql_error());
$row_ordered = mysql_fetch_assoc($ordered);
$totalRows_ordered = mysql_num_rows($ordered);

?>

<?php  //define 15 minute intervals from current time for dropdown selection
  $colname_schedate = date("Y-m-d H:i"); // current time
  $mkmin = date('i',strtotime($colname_schedate)) ;  // current minute
  if($mkmin >=0 && $mkmin <=15 ){
  	$onhour = strtotime($colname_schedate) - ((int)($mkmin*60)-(15*60));  //show 15 minutes past hour 
  }
  elseif($mkmin >15 && $mkmin <=30 ){
  	$onhour = strtotime($colname_schedate) - ((int)($mkmin*60)-(30*60));
  }
  elseif($mkmin >30 && $mkmin <=45 ){
  	$onhour = strtotime($colname_schedate) - ((int)($mkmin*60)-(45*60));
  }
  elseif($mkmin >45 && $mkmin <=59 ){
  	$onhour = strtotime($colname_schedate) - ((int)($mkmin*60)-(60*60));
  } 
?>

<!DOCTYPE html>
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
    <td><form id="formrxs" name="formrxs" method="post" action="PatInPatMedReShed.php?ordid=$colname_ordid">
      <table border="1" align="center" bgcolor="#BCFACC">
        <tr>
          <td colspan="5" nowrap="nowrap"><div align="center" class="BlueBold_20">Re-Schedule Medication </div></td>
        </tr>
        <tr>
          <td colspan="5"><div align="center" class="BlueBold_1414">Prescription</div></td>
        </tr>
        <tr>
          <td><input name="item" type="text" id="item" size="30" maxlength="100" readonly="readonly" value="<?php echo $row_ordered['item']; ?>" /></td>
          <td colspan="4" nowrap="nowrap"><input name="nunits" type="text" id="nunits" class="center" size="3" maxlength="3" readonly="readonly" value="<?php echo $row_ordered['nunits']; ?>" />
          <input name="unit" type="text" size="6" readonly="readonly" value="<?php echo $row_ordered['unit']; ?>" />
<?php if(is_numeric($row_ordered['every'])){?>
            Every
            <input name="every" type="text" id="every" size="3" class="center" readonly="readonly" value="<?php echo $row_ordered['every']; ?>" />
<?php } else {?> 
        	<input name="every" type="hidden" id="every" value="tab"/>
<?php }?> 
            <input name="evperiod" type="text" id="evperiod" size="6" readonly="readonly" value="<?php echo $row_ordered['evperiod']; ?>" />
            for
            <input name="fornum" type="text" id="fornum" size="3" class="center" readonly="readonly" value="<?php echo $row_ordered['fornum']; ?>" />
            <input name="forperiod" type="text" id="forperiod" size="6" readonly="readonly" value="<?php echo $row_ordered['forperiod']; ?>" />          </td>
        </tr>
        <tr>
          <td>Order Comments:</td>
          <td colspan="6">
          <textarea name="comments" id="comments"  nowrap="nowrap" cols="60" rows="2"  readonly="readonly"> <?php echo $row_ordered['comments']; ?></textarea></td>
        </tr>
        <tr>
      <td nowrap="nowrap">Select date &amp; time to start the new schedule.</td>
      <td  align="right" nowrap="nowrap">
	  <input id="scheddt" name="scheddt" type="text" size="12" maxlength="15">
      Begin
        <input id="schedtime" name="schedtime" type="text" size="8" maxlength="10" /></td>
      <td  align="left" nowrap="nowrap">
	   Must be a valid time</td>
          <td align="right" nowrap="nowrap">.
  <input name="SubmitAll" type="submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="Re-Schedule" /></td>
  </tr>
        <input name="quant" type="hidden" id="quant" value=""/>
        <input name="medrecnum" type="hidden" id="medrecnum" value="<?php echo $_SESSION['mrn']; ?>" />
        <input name="visitid" type="hidden" id="visitid" value="<?php echo $row_ordered['visitid']; ?>" />
        <input name="ordid" type="hidden" id="ordid" value="<?php echo $row_ordered['ordid']; ?>" />
        <input type="hidden" name="status" value="ordered"/>
        <input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
        <input name="entrydt" type="hidden" id="entrydt" value="<?php echo date("Y-m-d H:i"); ?>" />
        <input type="hidden" name="MM_insert" value="formrxs" />
      </table>
    </form></td>
  </tr>
</table>

<p>&nbsp;</p>

<table>
  	<td>Sheduled meds with staus 'Ordered' will be Re-Scheduled.
    </td>
  </tr>
  	<td>If any should not be rescheduled, change status to 'Skip'.
    </td>
  </tr>
	<tr>
  	<td>
     Note:  When OD, Nocte, PRN, or STAT are ordered, the internally assigned date/time scheduled is every 24 hrs beginning with the start date/time. </td>
  </tr>
  <tr>
     <td>				For BD, every 12 hrs, for TDS, every 8 hrs, for QDS every 6 hrs, beginning with the start date/time.
    </td>
  </tr>
</table>

<p>
  <script type="text/javascript" src="../../nogray_js/1.2.2/ng_all.js"></script>
  <script type="text/javascript" src="../../nogray_js/1.2.2/components/calendar.js"></script>
  <script type="text/javascript" src="../../nogray_js/1.2.2/components/timepicker.js"></script>
</p>

<script type="text/javascript">
ng.ready( function() {
    var my_cal = new ng.Calendar({
        input:'scheddt'
    });
    var my_timepicker = new ng.TimePicker({
        input:'schedtime',
    });   
});

  </script>
</body>
</html>
