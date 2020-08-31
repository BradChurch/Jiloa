<?php ob_start(); ?>
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>

<?php
if (isset($_SESSION['mrn'])) {
  $colname_mrn = (get_magic_quotes_gpc()) ? $_SESSION['mrn'] : addslashes($_SESSION['mrn']);
	}
if (isset($_SESSION['vid'])) {
  $colname_vid = (get_magic_quotes_gpc()) ? $_SESSION['vid'] : addslashes($_SESSION['vid']);
	}
mysql_select_db($database_swmisconn, $swmisconn);
$query_ordered = "SELECT o.id, o.visitid, o.item, o.nunits, o.unit, o.every, o.evperiod, o.fornum, o.forperiod, o.doctor, o.status, o.comments, substr(o.urgency,1,1) urg, DATE_FORMAT(o.entrydt,'%d%b%y %H:%i') entrydt, o.entryby, o.amtpaid FROM orders o WHERE o.medrecnum ='". $colname_mrn."' and o.visitid ='". $colname_vid."' and feeid = '30' ORDER BY o.id ASC";
$ordered = mysql_query($query_ordered, $swmisconn) or die(mysql_error());
$row_ordered = mysql_fetch_assoc($ordered);
$totalRows_ordered = mysql_num_rows($ordered);

if (isset($_GET['medid'])) {
  $colname_medid = (get_magic_quotes_gpc()) ? $_GET['medid'] : addslashes($_GET['medid']);

mysql_select_db($database_swmisconn, $swmisconn);
$query_medsched = "SELECT i.id, i.visitid, i.orderid, i.med, i.status, i.unit, i.nunits, i.schedt, i.comments, CASE WHEN i.givendt is NULL THEN 0 ELSE i.givendt END as givendt, i.givenby, i.entryby, i.entrydt, o.evperiod from ipmeds i join orders o on i.orderid = o.id where i.orderid like '".$colname_medid."%' and i.visitid ='". $colname_vid."' ORDER BY schedt ASC";
$medsched = mysql_query($query_medsched, $swmisconn) or die(mysql_error());
$row_medsched = mysql_fetch_assoc($medsched);
$totalRows_medsched = mysql_num_rows($medsched);
	}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>InPatient Meds</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
   var win_position = ',left=300,top=350,screenX=550,screenY=200';
   var newWindow = window.open(theURL,winName,features+win_position);
   newWindow.focus();
}
//-->
</script>
<style type="text/css">
 input.center{
 text-align:center;
 }
</style>

</head>

<body>
<table width="80%" border="1">
  <tr>
    <td colspan="2">Medications (Hover on medication for tool tip order comments.) </td>
    <td>&nbsp;</td>
    <td>Dispense (Hover on medication for tool tip medication comments.) </td>
  </tr>
  <tr>
    <td><input name="item" id="item" size="20" class="btngradblu100" value="ALL" onclick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&visit=PatVisitView.php&act=inpat&pge=PatInPatMeds.php&medid=%'" />
    </td>
    <td>Orders<em> <strong>Font</strong></em><strong>:</strong> Blue = Un-Scheduled, Green = Scheduled<br />
    <em><strong>Background:</strong></em> Green = Selected </td>
    <td>--</td>
<?php 	mysql_select_db($database_swmisconn, $swmisconn);
	$query_ctsched = "Select count(id) mmid from ipmeds where orderid = '".$colname_medid."' and status = 'ordered'";
	$ctsched = mysql_query($query_ctsched, $swmisconn) or die(mysql_error());
	$row_ctsched = mysql_fetch_assoc($ctsched);
	$totalRows_ctsched = mysql_num_rows($ctsched);
?>
<?php if($colname_medid <> '%' and $row_ctsched['mmid'] > 0 and allow(41,3) == 1){ ?>
    <td> <a href="javascript:void(0)" onclick="MM_openBrWindow('PatInPatMedReShed.php?ordid=<?php echo $colname_medid; ?>','StatusView','scrollbars=yes,resizable=yes,width=950,height=600')"><strong>Reschedule:</strong> <span class="BlackBold_12"><?php echo $row_medsched['med'] ?></span></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong> <a href="javascript:void(0)" onclick="MM_openBrWindow('PatInPatMedDiscont.php?ordid=<?php echo $colname_medid; ?>','StatusView','scrollbars=yes,resizable=yes,width=800,height=250')">Discontinue:</a></strong><a href="javascript:void(0)" class="BlackBold_12" onclick="MM_openBrWindow('PatInPatMedDiscont.php?ordid=<?php echo $colname_medid; ?>','StatusView','scrollbars=yes,resizable=yes,width=800,height=250')"> <?php echo $row_medsched['med'] ?></a> </td>
<?php } else {?>
    <td>&nbsp;</td>
<?php }?>

  </tr>
  <tr>
    <td colspan="2" valign="top">
	  <table>
       <?php do { //check to se if it is scheduled
	   mysql_select_db($database_swmisconn, $swmisconn);
		$query_sched = "Select max(id) mmid from ipmeds where orderid = '".$row_ordered['id']."'";
		$sched = mysql_query($query_sched, $swmisconn) or die(mysql_error());
		$row_sched = mysql_fetch_assoc($sched);
		$totalRows_sched = mysql_num_rows($sched);
		
		// change med bkg color if not scheuled
		    $bkds="btngradblu100";
		if($row_sched['mmid'] > 0){
			$bkds="btngradgrn";
		}
	   
			$bkgd="#F5F5F5";
	   	if($colname_medid == $row_ordered['id']){
			$bkgd="#32ff32";  //green
		} 
	   ?>
	     <tr>
		   <td bgcolor="<?php echo $bkgd ?>" title="Order#: <?php echo $row_ordered['id']; ?>&#10; Doctor: <?php echo $row_ordered['doctor']; ?>&#10; EntryDt: <?php echo $row_ordered['entrydt']; ?>&#10; EntryBy: <?php echo $row_ordered['entryby']; ?>&#10; Order Comments: <?php echo $row_ordered['comments']; ?>&#10;Class <?php echo $bkds; ?>"><input name="item2" id="item2" size="20" class="<?php echo $bkds; ?>" value="<?php echo $row_ordered['item']; ?>" onclick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&visit=PatVisitView.php&act=inpat&pge=PatInPatMeds.php&medid=<?php echo $row_ordered['id']; ?>'" /></td>
		   <!--adding type="button" centers the text-->
		<td nowrap="nowrap" bgcolor="<?php echo $bkgd ?>"><input name="nunits" type="text" id="nunits" size="1" disabled="disabled" class="center" Value="<?php echo $row_ordered['nunits']; ?>" />
		  <input name="unit" type="text" id="unit" size="5" disabled="disabled" Value="<?php echo $row_ordered['unit']; ?>" />
<?php if(is_numeric($row_ordered['every'])){?>
		  Every
		  <input name="every" type="text" id="every" size="1" class="center" disabled="disabled" Value="<?php echo $row_ordered['every']; ?>" />
<?php } else {?>
		  <input name="every" type="text" id="every" size="1" class="center" disabled="disabled" Value="" />
		  <input name="every" type="text" id="every" size="1" class="center" disabled="disabled" Value="" />
<?php }?>
		  <input name="every" type="text" id="every" size="3" disabled="disabled" Value="<?php echo $row_ordered['evperiod']; ?>" />
		  for
		  <input name="fornum" type="text" id="fornum" size="1"  class="center" disabled="disabled" Value="<?php echo $row_ordered['fornum']; ?>" />
		  <input name="forperiod" type="text" id="forperiod" size="3" disabled="disabled" Value="<?php echo $row_ordered['forperiod']; ?>" /></td>
		 </tr>
     <?php } while ($row_ordered = mysql_fetch_assoc($ordered)); ?>
    </table>	</td>
    <td valign="top">&nbsp;</td>
	<td valign="top">
	 <table bgcolor="#F5F5F5" border = "1">
      <tr bgcolor="#DCDCDC">
        <td><div align="center"></div></td>
        <td><div align="center">Date:Time</div></td>
        <td><div align="center">Med</div></td>
        <td><div align="center">#</div></td>
        <td><div align="center"></div></td>
        <td><div align="center">Status</div></td>
        <td><div align="center">GivenDt</div></td>
        <td><div align="center">GivenBy</div></td>
      </tr>
	  <tr>
        <?php if($totalRows_medsched > 0){
	    do { ?>
    <?php if(allow(41,2) == 1) { ?>	
        <td><a href="javascript:void(0)" onclick="MM_openBrWindow('PatInPatMedStatus.php?mmid=<?php echo $row_medsched['id']; ?>','StatusView','scrollbars=yes,resizable=yes,width=1000,height=500')">Edit</a></td>
      <?php	 }?>
	    <!--id, visitid, orderid, med, status, unit, nunits, schedt, comments, givenby, entryby, entrydt-->
        <?php 		if(date('A',$row_medsched['schedt']) == 'AM') { // set color of time display
				$dtbkgd = '#d5effc';
			} else {
				$dtbkgd = '#DFDFDF';
			}
 		   if($row_medsched['status'] == 'ordered') { // set color of time display
				$stbkgd = '#F5F5F5';
			} else {
				$stbkgd = '#DCDCDC"';
			}
?>
<?php if(strpos("OD Nocte BD TDS QDS PRN STAT ", $row_medsched['evperiod'])>0) {?>
        <td bgcolor="<?php echo $dtbkgd ?>" nowrap="nowrap" title="<?php echo date('D M-d h:i A',$row_medsched['schedt']); ?>"><?php echo date('D M-d',$row_medsched['schedt']) .'  '.$row_medsched['evperiod']; ?></td>
<?php } else {?>
        <td bgcolor="<?php echo $dtbkgd ?>" nowrap="nowrap"><?php echo date('D M-d h:i A',$row_medsched['schedt']); ?>
<?php }?>
	    <td nowrap="nowrap" bgcolor="<?php echo $stbkgd ?>" title="ID: <?php echo $row_medsched['id']; ?>&#10;VisitID: <?php echo $row_medsched['visitid']; ?>&#10;OrderID: <?php echo $row_medsched['orderid']; ?>&#10;EntryDt: <?php echo $row_medsched['entrydt']; ?>&#10;EntryBy: <?php echo $row_medsched['entryby']; ?>&#10;Medication Comments: <?php echo $row_medsched['comments']; ?>"><?php echo $row_medsched['med']; ?></td>
	    <td bgcolor="<?php echo $stbkgd ?>"><?php echo $row_medsched['nunits']; ?></td>
	    <td bgcolor="<?php echo $stbkgd ?>"><?php echo $row_medsched['unit']; ?></td>
	    <td bgcolor="<?php echo $stbkgd ?>"><?php echo $row_medsched['status']; ?></td>
        <td bgcolor="<?php echo $stbkgd ?>" nowrap="nowrap"><?php if($row_medsched['givendt'] == 0) { echo ' --- ';} else { echo date('D h:i_A' ,$row_medsched['givendt']); } ?></td>
	    <td bgcolor="<?php echo $stbkgd ?>" title="<?php if($row_medsched['givendt'] == 0) { echo ' --- ';} else { echo date('D M-d_h:i_A' ,$row_medsched['givendt']); } ?>" nowrap="nowrap"><?php echo $row_medsched['givenby']; ?></td>
<?php if(strlen($row_medsched['comments'])>0){  ?>
	    <td bgcolor="<?php echo $stbkgd ?>"><?php echo $row_medsched['comments']; ?></td>  
<?php   } 		?>
	    </tr>
			<?php } while ($row_medsched = mysql_fetch_assoc($medsched)); 
			
	 } else {
      if($colname_medid == '%'){ ?>
       <tr>
        <td></td>
      </tr>
			<?php	 } else { ?>
      <tr>
        <td><a href="javascript:void(0)" onclick="MM_openBrWindow('PatInPatMedShed.php?vid=<?php echo $_SESSION['vid']; ?>&ordid=<?php echo $colname_medid; ?>','StatusView','scrollbars=yes,resizable=yes,width=950,height=500')">Schedule</a> </td>
      </tr>
      <?php	 }
	 } ?>
    </table></td>
  </tr>
</table>
<?php
mysql_free_result($ordered);
mysql_free_result($medsched);
mysql_free_result($ctsched);
ob_end_flush();
?>
</body>
</html>
