<?php  $pt = "Patient Show"; ?>
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>
<?php include_once($_SERVER['DOCUMENT_ROOT'].'/Len/functions/functions.php'); ?>

<?php // find server name
function curPageURL() {
$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
return $url;
}

?>

<?php
// *** Get the patient permanent record and the ID of the patient info record
$colname_patperm = "3";
if (isset($_GET['mrn'])) {
  $colname_patperm = (get_magic_quotes_gpc()) ? $_GET['mrn'] : addslashes($_GET['mrn']);
  $_SESSION['mrn'] = $colname_patperm;  //set session variable
}
else {
	if (isset($_SESSION['mrn'])) {
  $colname_patperm = (get_magic_quotes_gpc()) ? $_SESSION['mrn'] : addslashes($_SESSION['mrn']);
	}
}
mysql_select_db($database_swmisconn, $swmisconn);
$query_patperm = "SELECT pp.medrecnum, pp.hospital, pp.active, DATE_FORMAT(pp.ddate,'%d-%b-%Y') ddate, pp.entrydt, pp.entryby, pp.lastname, pp.firstname, pp.othername, pp.gender, pp.ethnicgroup, pp.est, pp.dob, pp.photofile, pp.employeegroup, pp.employeemrn, pi.id FROM patperm pp left outer join patinfo pi on pp.medrecnum = pi.medrecnum where pp.medrecnum = '".$colname_patperm."'";
$patperm = mysql_query($query_patperm, $swmisconn) or die(mysql_error());
$row_patperm = mysql_fetch_assoc($patperm);
$totalRows_patperm = mysql_num_rows($patperm);

mysql_select_db($database_swmisconn, $swmisconn);
$query_patnotice = "SELECT p.id nid, notice, tooltip, bkgcolor FROM patnotices p join notices n on p.noticeid = n.id where p.medrecnum = '".$colname_patperm."'";
$patnotice = mysql_query($query_patnotice, $swmisconn) or die(mysql_error());
$row_patnotice = mysql_fetch_assoc($patnotice);
$totalRows_patnotice = mysql_num_rows($patnotice);
//#########
mysql_select_db($database_swmisconn, $swmisconn);
$query_patvisit = "SELECT v.id, v.medrecnum,v.location, v.visitdate, v.discharge FROM patvisit v where v.id = (Select max(v.id) from patvisit v where v.medrecnum = '".$colname_patperm."')";
$patvisit = mysql_query($query_patvisit, $swmisconn) or die(mysql_error());
$row_patvisit = mysql_fetch_assoc($patvisit);
$totalRows_patvisit = mysql_num_rows($patvisit);

$_SESSION['visitdt'] = $row_patvisit['visitdate'];	 // for subsequent pages (PatAnte....php


//#########
// count the number of visits for this patient
  $_SESSION['mrn'] == $colname_patperm;  //set session variable
mysql_select_db($database_swmisconn, $swmisconn);
$query_visits = sprintf("SELECT count(id) visits FROM patvisit WHERE medrecnum = %s", $colname_patperm);
$visits = mysql_query($query_visits, $swmisconn) or die(mysql_error());
$row_visits = mysql_fetch_assoc($visits);
$totalRows_visits = mysql_num_rows($visits);
$_SESSION['vnum'] = $row_visits['visits'];  //set session variable

//mysql_select_db($database_swmisconn, $swmisconn);
//$query_AcctBal = "SELECT p.lastname, p.firstname, p.othername, DATE_FORMAT(p.DOB,'%d-%c-%Y') DOB, p.gender, o.id, o.medrecnum, SUM(CASE WHEN r.amtdue IS NULL THEN o.amtdue ELSE r.amtdue End) as amtdue, IFNULL(SUM(r.amtpaid),0) amtpaid, SUM(IFNULL(r.amtpaid,0))-SUM(CASE WHEN r.amtdue IS NULL THEN o.amtdue ELSE IFNULL(r.amtdue,0) End) bal FROM patperm p join `orders` o on o.medrecnum = p.medrecnum left outer join`rcptord` r on o.id = r.ordid WHERE o.billstatus <> 'Refunded' and o.medrecnum = '".$colname_patperm."' GROUP BY medrecnum";
//$query_AcctBal = "SELECT p.lastname, p.firstname, p.othername, DATE_FORMAT(p.DOB,'%d-%c-%Y') DOB, p.gender, o.id, o.medrecnum, SUM(o.amtdue) sumamtdue, SUM(o.amtpaid) sumamtpaid, SUM(IFNULL(o.amtpaid,0)) - SUM(IFNULL(o.amtdue,0)) bal FROM patperm p join `orders` o on o.medrecnum = p.medrecnum WHERE o.billstatus <> 'Refunded' and o.medrecnum = '".$colname_patperm."' GROUP BY medrecnum";

//$AcctBal = mysql_query($query_AcctBal, $swmisconn) or die(mysql_error());
//$row_AcctBal = mysql_fetch_assoc($AcctBal);
//$totalRows_AcctBal = mysql_num_rows($AcctBal);

// amount deposited by patient MRN (where ro.status = 'Deposited'
$AcctBal = 0;
$query_AcctDeposits = "SELECT SUM(IFNULL(ro.amtpaid,0)) sumamtpaid FROM rcptord ro join receipts r on r.id = ro.rcptid WHERE ro.status = 'Deposited' and r.medrecnum = '".$colname_patperm."' GROUP BY r.medrecnum";
$AcctDeposits = mysql_query($query_AcctDeposits, $swmisconn) or die(mysql_error());
$row_AcctDeposits = mysql_fetch_assoc($AcctDeposits);
$totalRows_AcctDeposits = mysql_num_rows($AcctDeposits);  //ok

//  amount paid by patient where ro.status = 'Paid' or 'Partaid' and nbc = 'Deposited'
$query_AcctPdByDep = "SELECT SUM(IFNULL(ro.amtpaid,0)) sumamtpaid FROM rcptord ro join receipts r on r.id = ro.rcptid WHERE (ro.status = 'Paid' OR ro.status = 'PartPaid') and r.nbc = 'Deposit' and r.medrecnum = '".$colname_patperm."' GROUP BY r.medrecnum"; //ok
$AcctPdByDep = mysql_query($query_AcctPdByDep, $swmisconn) or die(mysql_error());
$row_AcctPdByDep = mysql_fetch_assoc($AcctPdByDep);
$totalRows_AcctPdByDep = mysql_num_rows($AcctPdByDep);

// amount refunded by patient wherero.status = 'Refunded' and r.nbc = 'Deposit' 
$query_AcctRefToDep = "SELECT SUM(IFNULL(ro.amtpaid,0)) sumamtpaid FROM rcptord ro join receipts r on r.id = ro.rcptid WHERE ro.status = 'Refunded' and r.nbc = 'Deposit' and r.medrecnum = '".$colname_patperm."' GROUP BY r.medrecnum"; //ok
$AcctRefToDep = mysql_query($query_AcctRefToDep, $swmisconn) or die(mysql_error());
$row_AcctRefToDep = mysql_fetch_assoc($AcctRefToDep);
$totalRows_AcctRefToDep = mysql_num_rows($AcctRefToDep);

// amount of Deposit refunded
$query_AcctDepRefund = "SELECT SUM(IFNULL(ro.amtpaid,0)) sumamtpaid FROM rcptord ro join receipts r on r.id = ro.rcptid WHERE ro.status = 'DepRefund' and r.medrecnum = '".$colname_patperm."' GROUP BY r.medrecnum"; //ok
$AcctDepRefund = mysql_query($query_AcctDepRefund, $swmisconn) or die(mysql_error());
$row_AcctDepRefund = mysql_fetch_assoc($AcctDepRefund);
$totalRows_AcctDepRefund = mysql_num_rows($AcctDepRefund);

// Calculate 
$AcctBal = $row_AcctDeposits['sumamtpaid'] + $row_AcctDepRefund['sumamtpaid'] + (0 - $row_AcctRefToDep['sumamtpaid']) - $row_AcctPdByDep['sumamtpaid'];
$_SESSION['AcctBal'] = $AcctBal;

$query_OrdBal = "SELECT p.lastname, p.firstname, p.othername, DATE_FORMAT(p.DOB,'%d-%c-%Y') DOB, p.gender, o.id, o.medrecnum, SUM(o.amtdue) sumamtdue, SUM(o.amtpaid) sumamtpaid, SUM(IFNULL(o.amtpaid,0)) - SUM(IFNULL(o.amtdue,0)) bal FROM patperm p join `orders` o on o.medrecnum = p.medrecnum WHERE o.billstatus <> 'Refunded' and o.feeid <> 279 and o.medrecnum = '".$colname_patperm."' GROUP BY medrecnum";

$OrdBal = mysql_query($query_OrdBal, $swmisconn) or die(mysql_error());
$row_OrdBal = mysql_fetch_assoc($OrdBal);
$totalRows_OrdBal = mysql_num_rows($OrdBal);
?>

<?php  //calculate patient Age and assign it to $patage variable
$patage = 0;
$patdob = 0;
$est = "";
if ($row_patperm['est'] === "Y") {
	$est = "*";
}
if (strtotime($row_patperm['dob'])) {
	$c= date('Y');
	$y= date('Y',strtotime($row_patperm['dob']));
	$patage = $c-$y;
//format date of birth
	$datetime = strtotime($row_patperm['dob']);
	$patdob = $est.date("d-M-Y", $datetime);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
   var win_position = ',left=300,top=25,screenX=300,screenY=25';
   var newWindow = window.open(theURL,winName,features+win_position);
   newWindow.focus();
}
//-->
</script>
</head>

<body>

<!-- Display PATIENT PERMANENT Data  -->
<table border="10" align="center" bordercolor="#336699" bgcolor="#F5F5F5">
<?php if(strpos($_SERVER["REQUEST_URI"],'PatPermEdit.php') > 0) {  ?>
  <tr>
    <td><div><a href="javascript:void(0)" onclick="MM_openBrWindow('PatPermAddNotice.php?mrn=<?php echo $_SESSION['mrn']; ?>','StatusView','scrollbars=yes,resizable=yes,width=500,height=300')">Add Notice</a></div><br/>
</td>
<?php } ?>
<?php do { ?>	
		<td class ="<?php echo $row_patnotice['bkgcolor']; ?>" Title="<?php echo $row_patnotice['tooltip']; ?>"><?php echo $row_patnotice['notice']; ?>
<?php if(strpos($_SERVER["REQUEST_URI"],'PatPermEdit.php') > 0 AND $totalRows_patnotice > 0) {  ?>
	    <div><a href="javascript:void(0)" onclick="MM_openBrWindow('PatPermDeleteNotice.php?id=<?php echo $row_patnotice['nid']; ?>','StatusView','scrollbars=yes,resizable=yes,width=500,height=300')">Delete</a></div>
<?php } ?>
		</td>

<?php } while ($row_patnotice = mysql_fetch_assoc($patnotice)); ?>
	
    <td nowrap="nowrap" Title="Active= <?php echo $row_patperm['active']; ?>&#10;EnteredBy: <?php echo $row_patperm['entryby']; ?>&#10;Entry Date: <?php echo $row_patperm['entrydt']; ?>">Name: <span class="subtitlebl"><?php echo $row_patperm['lastname']; ?></span><span class="subtitlebl">, <?php echo $row_patperm['firstname']; ?></span>  <span class="subtitlebl">(<?php echo $row_patperm['othername']; ?>)</span><br /> 
	<?php if (!empty($row_patperm['ddate'])) { ?>
       <span class="RedBold_18">DECEASED: <?php echo $row_patperm['ddate']; ?></span><br />
	<?php }?>
      Medical Record Number: <span class="subtitlebl"><?php echo $row_patperm['medrecnum']; ?></span><br />
<?php //if(allow(9,2) == 1) { ?>   <!-- disabled to allopw ALL users to see Order and Acct Balance of Patient-->

	<?php if($row_OrdBal['bal'] < 0) { ?>
		<a href="javascript:void(0)" onclick="MM_openBrWindow('../Patient/PatAcctSum.php?mrn=<?php echo $row_patperm['medrecnum']; ?>','StatusView','scrollbars=yes,resizable=yes,width=800,height=700')">Order Balance:</a> <span class="flagWhiteonRed"> <?php echo $row_OrdBal['bal']; ?> </span>
	<?php } else {?>
		<a href="javascript:void(0)" onclick="MM_openBrWindow('../Patient/PatAcctSum.php?mrn=<?php echo $row_patperm['medrecnum']; ?>','StatusView','scrollbars=yes,resizable=yes,width=800,height=700')">Order Balance: </a><span class="flagWhiteonGreen"> <?php echo $row_OrdBal['bal']; ?> </span>
	<?php }?>

	<?php if($AcctBal < 0) { ?>
		<a href="javascript:void(0)" onclick="MM_openBrWindow('../Patient/PatAcctSum.php?mrn=<?php echo $row_patperm['medrecnum']; ?>','StatusView','scrollbars=yes,resizable=yes,width=800,height=700')">Acct Balance: </a> <span class="flagWhiteonRed"> <?php echo $AcctBal; ?> </span>
	
	<?php } else {?>
		<a href="javascript:void(0)" onclick="MM_openBrWindow('../Patient/PatAcctSum.php?mrn=<?php echo $row_patperm['medrecnum']; ?>','StatusView','scrollbars=yes,resizable=yes,width=800,height=700')">Acct Balance: </a> <span class="flagWhiteonGreen"> <?php echo $AcctBal; ?> </span>

	<?php }?>

	<?php //}?>	</td>
	
	<td align="center"><img src="<?php echo "../../DATA_SWMIS/imgthumbs/".$row_patperm['photofile']; ?>" /></td>
	
    <td nowrap="nowrap">Age: <span class="subtitlebl"><?php echo $patage; ?></span>&nbsp;&nbsp;&nbsp;Gender:<span class="subtitlebl"><?php echo $row_patperm['gender']; ?></span><br />DOB: <span class="subtitlebl"><?php echo $patdob; ?></span><br />
    Ethnic Group: <span class="subtitlebl"><?php echo $row_patperm['ethnicgroup']; ?></span></td>
    <td nowrap="nowrap">
<?php if(stripos(curPageURL(),'PatShow1') > 0) { ?>	  <!-- This will allow edit only in Patient area-->	</td>
	<?php if(allow(107,2) == 1) {  // verify user had permissionto edit patient perm ?>
    <td nowrap="nowrap" align="center" class="subtitlebl" ><a href="../Patient/PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&amp;show=../Patient/PatPermEdit.php">Edit</a></td>
	<?php  }
	} ?>

<?php // display pink background if patient info record does not exist
	  $bkg = "#f5f5f5";
		if ($row_patperm['id'] <=0) {	
	  $bkg = "#EFA0E1";
	  }
?>
	<?php if(allow(21,1) == 1) {  // verify user had permissionto view patient info ?>
    <td nowrap="nowrap" align="center" bgcolor="<?php echo $bkg; ?>"  class="subtitlebl" ><a href="../Patient/PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&amp;show=../Patient/PatInfoView.php">INFO</a></td>
	<?php  } ?>
		<td bgcolor="##336699">&nbsp;&nbsp;&nbsp;</td>
    <td>
    	<table bgcolor="#fffff0">
      	<tr>
    			<td nowrap="nowrap" title="This is Current Visit or Last Visit&#10; This will not change when selecting previous visits.">Visit #: <?php echo $row_patvisit['id'] ?></td>
    			<!--<td nowrap="nowrap"><a href="PatShow1.php?mrn=<?php echo $row_patvisit['medrecnum']?>&visit=PatVisitView.php&vid=<?php echo $row_patvisit['id']?>&disp=on">Full View</a></td>-->
 			<?php if(isset($row_patvisit['id'])) { ?>
          <td title="Current Visit or Last Visit&#10; Will not change when selecting previous visits."><input type="button" name = "button1" class="btngradblu85" value="Full View" onclick="parent.location='../Patient/PatShow1.php?<?php echo $_SERVER['QUERY_STRING']; ?>&disp=on'" /></td>
      <?php } ?>
        </tr>
      	<tr>
    			<td nowrap="nowrap" colspan="2">Location: <span class="flagWhiteonBlue"><?php echo $row_patvisit['location'] ?></span></td>
        </tr>
      	<tr>
						<td align="left"><a href="PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&visit=PatVisitList.php&disp=on">Visits: <?php echo $_SESSION['vnum']; ?></a></td>
        </tr>
      	<tr>
        <?php if(isset($row_patvisit['discharge'])){?>
    			<td nowrap="nowrap" colspan="2" class="flagWhiteonRed"title="This is for Current Visit or Last Visit&#10; This will not change when selecting previous visits.">Discharged:<span class="flagBlackonYellow"><?php echo $row_patvisit['discharge'] ?></span></td>
        <?php } else {?>
    			<td nowrap="nowrap" colspan="2"title="This is for Current Visit or Last Visit&#10; This will not change when selecting previous visits.">Discharged:<?php echo $row_patvisit['discharge'] ?></td>    
        <?php }?>
        </tr>
      </table>
    </td>
		<td bgcolor="##336699">&nbsp;&nbsp;&nbsp;</td>
    
<?php if(!empty($row_patperm['employeegroup'])){
					if($row_patperm['employeemrn'] > 2) {?>
    <td title="This is designation of Employee Group.&#10;e.g. PH = Peace House, CA = Calvary Arrows, etc. &#10; * indicates this patient is a dependent of someone in this group"bgcolor="#e6f2ff"><span class="BlueBold_18"><?php echo $row_patperm['employeegroup'] ?>*</span>
	<?php } else {?>
    <td title="This is designation of Employee Group.&#10;e.g. PH = Peace House, CA = Calvary Arrows, etc."  bgcolor="#e6f2ff"><span class="BlueBold_18"><?php echo $row_patperm['employeegroup'] ?></span>
<?php }
}?>
    </td>
  </tr>
</table>
<!--   DISPLAY PATIENT INFORMATION  -->
<?php
// If link includes a value for "show', set $applic to show PATIENT INFORMATION, otherwise patient info is not displayed by default
$applic="";
if (isset($_GET["show"])) {
	$applic = $_GET['show'];
?>
		<table align="center">
			<tr>
				<td valign="top">
					<?php require_once($applic); ?></td>
			</tr>
		</table>
<?php } ?>

</body>
</html>
