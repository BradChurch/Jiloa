<?php  ob_start();?>
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
 ?>

     <!-- Nigerian Naira  &#8358;     &micro;  keyboard hold down alt & press 230 -->
<?php $pt = "Home";  //Jiloa/home.index ?>
<?php //echo $_COOKIE['loggedin'] ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/Len/Jiloa/Master/Header.php'); ?> 
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
} ?>

<?php // find server name
function curPageURL() {
$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
return $url;
}
?>
<!--Update message status when selectiong read,done, new links on message lists-->
<?php if(isset($_GET['statuschg']) && isset($_GET['msgid'])){
 
  //mysql_select_db($database_swmisconn, $swmisconn);
  $updateSQL = sprintf("UPDATE usrmsg SET status=%s WHERE id=%s",
                       GetSQLValueString($_GET['statuschg'], "text"),
                       GetSQLValueString($_GET['msgid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());	
}?>
<!-- look for openflag value set to Y when message is created (causing link display rather than the message)
     and set it to 'N' (which will cause message to be displayed).-->
<?php 
	if(isset($_GET['msgid']) and isset($_GET['openflag']) and $_GET['openflag'] == 'Y') {
		$updateSQL = sprintf("UPDATE usrmsg SET openflag=%s WHERE id=%s",
												 GetSQLValueString('N', "text"),
												 GetSQLValueString($_GET['msgid'], "int"));
	
		mysql_select_db($database_swmisconn, $swmisconn);
		$Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());	
		
	}
?>
<!--select query by status-->
<?php $col_status = "new";
   if(isset($_GET['status'])) {
   $col_status = $_GET['status'];
}
?>
<?php // a separate query for SENT  // 'sent means 'fromuser' i.e. $_SESSION['user']
  if($col_status == 'sent'){
		mysql_select_db($database_swmisconn, $swmisconn);
		$query_msg = "SELECT id, fromuser, touser, status, urgent, openflag, reply, msg, entrydt FROM usrmsg WHERE fromuser = '".$_SESSION['user']."' ORDER BY id DESC";
		$msg = mysql_query($query_msg, $swmisconn) or die(mysql_error());
		$row_msg = mysql_fetch_assoc($msg);
		$totalRows_msg = mysql_num_rows($msg);	
	
} else {
// find 'id' number of current User 
		mysql_select_db($database_swmisconn, $swmisconn);
		$query_user = "SELECT id, userid FROM users WHERE userid = '".$_SESSION['user']."'";
		$user = mysql_query($query_user, $swmisconn) or die(mysql_error());
		$row_user = mysql_fetch_assoc($user);
		$totalRows_user = mysql_num_rows($user);
		$myid = 'x'.$row_user['id']; // ('x' prefix + Current user id number + ',' suffix - tomake it unique) 

// a separate query for newread
// select records from usrmsg table where $myid is in 'touser' field and new+read status
		if($col_status == 'newread'){	
			mysql_select_db($database_swmisconn, $swmisconn);
			$query_msg = "SELECT id, fromuser, touser, status, urgent, openflag, reply, msg, entrydt FROM usrmsg WHERE fromuser != '".$_SESSION['user']."' and INSTR(touser, 'x".$row_user['id'].",') > 0 and status IN ('new','read') ORDER BY id DESC";
			$msg = mysql_query($query_msg, $swmisconn) or die(mysql_error());
			$row_msg = mysql_fetch_assoc($msg);
			$totalRows_msg = mysql_num_rows($msg);
		} else {  // if selected status = 'new, 'read' or 'done'
// select records from usrmsg table where $myid is in 'touser' field and selected status
			mysql_select_db($database_swmisconn, $swmisconn);
			$query_msg = "SELECT id, fromuser, touser, status, urgent, openflag, reply, msg, entrydt FROM usrmsg WHERE fromuser != '".trim($_SESSION['user'])."' and INSTR(touser, 'x".$_SESSION['uid'].",') > 0 and status = '".$col_status."' ORDER BY id DESC";
			$msg = mysql_query($query_msg, $swmisconn) or die(mysql_error());
			$row_msg = mysql_fetch_assoc($msg);
			$totalRows_msg = mysql_num_rows($msg);
		}
}	
?>
<?php  //count new and read mesages to activare blinking 'Your Messages'
			mysql_select_db($database_swmisconn, $swmisconn);
			$query_msgcnt = "SELECT id, fromuser, touser, status, urgent, openflag, reply, msg, entrydt FROM usrmsg WHERE fromuser != '".$_SESSION['user']."' and INSTR(touser, 'x".$_SESSION['uid'].",') > 0 and status IN ('new','read') ORDER BY id DESC";
			$msgcnt = mysql_query($query_msgcnt, $swmisconn) or die(mysql_error());
			$row_msgcnt = mysql_fetch_assoc($msgcnt);
			$totalRows_msgcnt = mysql_num_rows($msgcnt);
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="/Len/css/Level3_1.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript"></script>
<script language="JavaScript" type="text/JavaScript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
   var win_position = ',left=300,top=400,screenX=300,screenY=400';
   var newWindow = window.open(theURL,winName,features+win_position);
   newWindow.focus();
}
</script>
<script type="text/javascript">
    function blinker()
    {
        if(document.querySelector("blink"))
        {
            var d = document.querySelector("blink") ;
            d.style.visibility= (d.style.visibility=='hidden'?'visible':'hidden');
            setTimeout('blinker()', 500);
        }
		}
    </script>
<script src="../../tinymce/js/tinymce/tinymce.min.js"></script>
<script src="../../tinymce/tinymce-emoji-master/dist/tinymce-emoji/plugin.min.js"></script>
<script>
tinymce.init({ 
  selector:'textarea#msg', 
	//mode : 'textareas',
	//editor_selector : 'msg',   //textarea  must have class="msg"
	content_css : '../../CSS/content.css',
	force_p_newlines : true,
	min_height: 0,
	width: 400,
  autoresize_max_height: 400,
	autoresize_min_height: 20,
	autoresize_bottom_margin: 1,
	menubar: false,
	statusbar: false,
	emoji_add_space: false, // emoji are quite wide, so a space is added automatically after each by default; this disables that extra space
  emoji_show_groups: false,   // hides the tabs and dsiplays all emojis on one page
  emoji_show_subgroups: true,    // hides the subheadings
  emoji_show_tab_icons: true, // hides the icon on each tab label
	toolbar_items_size : 'small',
	toolbar: 'tinymceEmoji | bold italic underline | bullist  numlist | indent outdent | alignleft aligncenter alignright | superscript subscript',
	toolbar:false,
    plugins: 'tinymceEmoji, autoresize'
	 });
</script>

</head>

<body onLoad="blinker();">
<table width="800" border="0" align="center">
	<tr>  <!--#151B8D  orig dk Blue  #99CCFF lt blue-->
		<td colspan="9" height="20" bgcolor="#2196f3" align="center"><span class="flagWhiteonBlue">Home Page&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Not I, But Christ! </span></td>
  </tr>
<!--<p>The structure Rule:<br /> 
      1. Use Master/Header on the top of each page. Provides Banner, Menu, Date Time, Login &amp; Logout <br/>2. Use Master/Footer on bottom of page  as needed </p>
  <tr>
    <td height="20" align="center" colspan="9" >
<?php if (allow(29,1) == 1) {	?>
	<input type="button" value = "MRBS" onclick="window.open(http://mrbs.sourceforge.net/)">&nbsp;&nbsp;&nbsp;&nbsp;
<button onclick="window.location.href='http://mrbs.sourceforge.net/'">CSS page</button>&nbsp;&nbsp;&nbsp;&nbsp;
-->
<!--	<a href="../mrbs-1.4.10/web/index.php" target="_blank">Scheduling </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php }?>
<?php if (allow(29,2) == 1) {	?>
	<a href="http://mrbs.sourceforge.net/" target="_blank">                MRBS</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php }?>
<?php if (allow(29,4) == 1) {	?>
	<a href="../HelpnDoc/Output/Build pdf documentation/SWMIS Developer Documentation.pdf" target="_blank">DevDocuments</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<?php }?>

	<?php if (allow(29,1) == 1) {	?>
	<a href="../HelpnDoc/Output/Build pdf documentation/SWMIS User Documents.pdf" target="_blank">User Documents </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php }?>
<?php if (allow(27,1) == 1) {	?>
				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../Patient/PatPtOrders.php">PT Pending</a>
<?php }?>
<?php if (allow(27,1) == 1) {	?>
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../Home/SurgREOrders.php">Surg Results</a>
<?php }?>
<?php if (allow(27,1) == 1) {	?>
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../Home/AnesthesiaREOrders.php">Anesthesia Results</a>
<?php }?>
<?php if (allow(27,1) == 1) {	?>
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../Home/RadREOrders.php">Radiology Results</a>
    <?php }?>
<?php if (allow(27,1) == 1) {	?>
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../Home/DentalREOrders.php">Dental Results</a>
    <?php }?>
<?php if (allow(27,1) == 1) {	?>
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../Home/ImmunizeREOrders.php">Immunization Results</a>
    <?php }?>
<?php if (allow(27,1) == 1) {	?>
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../Patient/PatVisitReturn.php"> Return Dates</a>
    <?php }?>
<?php if (allow(27,1) == 1) {	?>
			 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../Home/InpatientBeds.php"> Inpatient Beds</a>
       </td>
    <?php }?>
  </tr>
-->
<!--  <tr>  onclick=window.open("http://www.someone.com/") to open in a new tab
  	<td> <?php if (allow(29,1) == 1) {	?><button onclick=window.open("../mrbs-1.4.10/web/index.php")>Scheduling&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></button></td>
    <td> <?php if (allow(29,2) == 1) {	?><button onclick=window.open("http://www.mrbs.sourceforge.net/")>MRBS&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></button></td>
   <td> <?php if (allow(27,1) == 1) {	?><button onClick=window.location.href='../Patient/PatPtOrders.php'>PT Pending</button>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></td>
    <td> <?php if (allow(27,1) == 1) {	?><button onClick=window.location.href='../Home/SurgREOrders.php'>Surg Results</button>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></td>
    <td> <?php if (allow(27,1) == 1) {	?><button onClick=window.location.href='../Home/AnesthesiaREOrders.php'>Anesthesia Results</button>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></td>
    <td> <?php if (allow(27,1) == 1) {	?><button onClick=window.location.href='../Home/RadREOrders.php'>Radiology Results</button>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></td>
    <td> <?php if (allow(27,1) == 1) {	?><button onClick=window.location.href='../Home/DentalREOrders.php'>Dental Results</button>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></td>
    <td> <?php if (allow(27,1) == 1) {	?><button onClick=window.location.href='../Home/ImmunizeREOrders.php'>Immunization Results</button>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></td>
    <td> <?php if (allow(27,1) == 1) {	?><button onClick=window.location.href='../Patient/PatVisitReturn.php'>Return Dates</button>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></td>
  </tr>-->
</table>
<table width="50%" align="center" border="1" bordercolor="#FFFFFF" cellpadding="1" cellspacing="1" style="border-collapse:collapse">
  <tr>
    <td width="5%" align="center" class="BlueBold_1414"><a href="../../mrbs-1.4.10/web/index.php" target="_blank">Scheduling </a></td>
    <td width="5%" align="center" class="BlueBold_1414"><a href="http://mrbs.sourceforge.net/" target="_blank">MRBS</a></td>
    <td width="5%" align="center" class="BlueBold_1414"><a href="../Patient/PatPtOrders.php">PT<br>Pending</a></td>
    <!--<td width="5%" align="center"><a href="../Home/SurgREOrders.php">Surgery<br>Results</a></td>-->
    <!--<td width="5%" align="center"><a href="../Home/AnesthesiaREOrders.php">Anesthesia<br>Results</a></td>-->
    <td width="5%" align="center" class="BlueBold_1414"><a href="../Home/PatSurgSchedule.php">Surg<br>Sched</a></td>
    <td width="5%" align="center" class="BlueBold_1414"><a href="../Home/RadREOrders.php">Radiology<br>Results</a></td>
    <td width="5%" align="center" class="BlueBold_1414"><a href="../Home/DentalREOrders.php">Dental<br>Results</a></td>
    <td width="5%" align="center" class="BlueBold_1414"><a href="../Patient/PatVisitReturn.php"> Return<br>Dates</a></td>
    <td width="5%" align="center" class="BlueBold_1414"><a href="../Home/InpatientBeds.php"> Inpatient<br>Beds</a></td>
  </tr>
</table>


<table align="center">
  <tr>
    <td height="20" bgcolor="#deeafa" align="center"><a href="index.php?act=spurgeon">Inspiration</span></a>
<?php if(isset($_GET['act']) and $_GET['act'] == 'spurgeon') {
 ?>	
		 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?act=close">Close</a>
 <?php }?>	</td>
  </tr>
  <tr>
  	<td align="center">
<?php $actapp = "";
   if (isset($_GET['act']) and $_GET['act'] == 'spurgeon') { 
   		$actapp = 'Spurgeon.php';?>
					<?php require_once($actapp); ?>
<?php }?>
		</td>
  </tr>
</table>
<!--footer - footer - footer - footer - -->
<table align="center">
  <tr>
    <td>
<?php include($_SERVER['DOCUMENT_ROOT'].'/Len/Jiloa/Master/Footer.php'); ?>
		</td>
	</tr>
</table>
<!--picture of BMC-->
<table width="600px" height="260px" align="center">
  <tr>
  	<td colspan="3" align="center"><img src="cc.jpg" alt="Bethany" width="600" height="240" /></td> <!--IMG_4743c-->
  </tr>
<!--  <tr>
  	<td><img src="IMG_4742c.jpg" alt="Dr Aba" width="200" height="270" /></td>
  	<td><img src="IMG_4734c.jpg" alt="Grace" width="200" height="270" /></td>
  	<td><img src="IMG_4842c.jpg" alt="Luper" width="200" height="270" /></td>
  </tr>
-->
<!--
  <?php //if (allow(29,4) == 1) {	?>
  <tr>
	<td colspan="3" ><?php //  echo curPageURL(); ?>     <a href="../../PrintTestButton.php" target="_blank">Print Receipt Test</a></td>
   <td>-->
	</td>
  </tr>
</table>
<?php //initialize session variables used on Compose page
		  $_SESSION['seldusr'] = '';
			$_SESSION['userid'] = '';
			$_SESSION['subj'] = '';
			$_SESSION['msg'] = '';
			$_SESSION['urgent'] = '';
?>
<table  align="center">
	<tr>
<?php if($totalRows_msgcnt > 0){ ?>  
    <td><div align="center" nowrap="nowrap" ><blink>Your Messages</blink></div></td> 
<?php } else { ?> 
    <td><div align="center" nowrap="nowrap" >Your Messages</div></td> 
<?php }?>
  </tr>
</table>
<!--   MESSAGE - MESSAGE - MESSAGE - MESSAGE - MESSAGE - MESSAGE - MESSAGE - MESSAGE - MESSAGE - MESSAGE - MESSAGE ---> 
<!-- SELECT messages to display according to status-->
<table align="center">
<form name="selstatus" id="selstatus" method="GET" action="index.php?status="$_GET['status']>
	<tr>
  	<td class="BlackBold_16">BMC Message System</td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td><strong><a href="javascript:void(0)" onClick="MM_openBrWindow('MsgCompose.php','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')">Compose</a></strong></td>
    <td><strong>Display:</strong></td>
    <td colspan="6"><select name="status" id="status" size="1" onChange="document.selstatus.submit()">
      <option value="newread"  <?php if (!(strcmp("newread", $col_status))) {echo "selected=\"selected\"";} ?>>new+read</option>
      <option value="new" <?php if (!(strcmp("new", $col_status))) {echo "selected=\"selected\"";} ?>>new</option>
      <option value="read" <?php if (!(strcmp("read", $col_status))) {echo "selected=\"selected\"";} ?>>read</option>
      <option value="sent" <?php if (!(strcmp("sent", $col_status))) {echo "selected=\"selected\"";} ?>>sent</option>
      <option value="done" <?php if (!(strcmp("done", $col_status))) {echo "selected=\"selected\"";} ?>>done</option>
    </select></td>
    <td><!--<input type="submit" name="submit" value="Go">--></td>  
  </tr>
    <input type="hidden" name="MM_update" value="selstatus" />
</form>
</table>

<?php 
//echo 'userid '.$myid.'	<br>';		
////echo '|'.var_dump($row_msg['touser']).'|<br>';
//echo '<pre>';'|'.print_r($row_msg['touser']); echo '</pre>'; echo '<br>';
//echo $row_msg['id'].'...'.'<br>'; 
//echo $row_msg['fromuser'];			
?>

 <!-- SENT - SENT - SENT - SENT - SENT - SENT - SENT -->
<?php if($col_status == 'sent'){?>
<table width="700px" border="1" align="center" cellpadding="1" cellspacing="1">
  <tr>
    <td>id</td>
    <td align="center"><strong>To User</strong></td>
    <td align="center"><strong>Status</strong></td>
    <td align="center"><strong>Message</strong></td>
    <td align="center"><strong>Sent Date</strong></td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_msg['id']; ?></td>
      
<?php
  $senders = "";
	
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_usr = "SELECT userid from users u where instr('".$row_msg['touser']."', concat('x',id,','))";
	$usr = mysql_query($query_usr, $swmisconn) or die(mysql_error());
	$row_usr = mysql_fetch_assoc($usr);
	$totalRows_usr = mysql_num_rows($usr);

	do {
  		$senders = $senders.$row_usr['userid'].', ';	
	} while ($row_usr = mysql_fetch_assoc($usr)); ?>
		
      <td bgcolor="#cce0ff"><?php echo $senders; ?></td>
<?php if($row_msg['openflag'] == 'N'){?>
      <td bgcolor="#cce0ff"><?php echo $row_msg['status']; ?><br>Rec'd</td>
<?php } else { ?>
      <td bgcolor="#cce0ff"><?php echo $row_msg['status']; ?></td>
<?php } ?>
      <td bgcolor="#cce0ff"><textarea cols="60" readonly> <?php echo $row_msg['msg']; ?></textarea></td>
    <?php $date = date_create($row_msg['entrydt']);?>
      <td align="center" bgcolor="#cce0ff" nowrap><?php echo date_format($date,'M-d-Y'); ?><br><?php echo date_format($date,'h:s A'); ?></td>
    </tr>
    <?php } while ($row_msg = mysql_fetch_assoc($msg)); ?>
</table>

  <!--RECEIVED - RECEIVED -RECEIVED -RECEIVED -RECEIVED -RECEIVED -RECEIVED -RECEIVED -RECEIVED -RECEIVED - -->- 
<?php } else {  // if status is not send ?>
<table border="1" align="center" cellpadding="1" cellspacing="1">
  <tr>
    <td>id</td>
    <td align="center"><strong>From User*</strong></td>
    <!--<td align="center"><strong>To User</strong></td>-->
    <td align="center"><strong>Status</strong></td>
    <td align="center"><strong>Message..<?php echo $_SESSION['uid'] ?> </strong></td>
    <td colspan="2" align="center"><strong>Change To:</strong></td>
    <td align="center"><strong>Sent Date</strong></td>
  </tr>
  <?php 	mysql_select_db($database_swmisconn, $swmisconn);
do {
	// find the names of users who received this message (beside current user)- for display in tooltip
  $toids = '';
	$touserids = '';
		$tousers = explode(',',$row_msg['touser']);
    foreach ($tousers as $value) {  // get separted id of user to look up 'userid'
		if($value != 'x'.$_SESSION['uid']){  //get users for reply to all except current user
			$toids = $toids.$value.','; }  
		$userid = ltrim($value, ' x'); // remove space and x from the value so lookup can be done with the number part of the 
				$query_touser = "SELECT trim(userid) userid from users u where id = '".trim($userid)."'";
				$touser = mysql_query($query_touser, $swmisconn) or die(mysql_error());
				$row_touser = mysql_fetch_assoc($touser);
				$totalRows_touser = mysql_num_rows($touser);
			//	echo $row_touser['userid'].'<br>';
					if($row_touser['userid'] != $_SESSION['user']){ // prevent listing current user as recipient of message
		$touserids = $touserids.$row_touser['userid'].', ';
					}
 }

// look up the record id in user table matching userid in Fromuser field	
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_fromusr = "SELECT id from users u where userid = '".$row_msg['fromuser']."'";
	$fromusr = mysql_query($query_fromusr, $swmisconn) or die(mysql_error());
	$row_fromusr = mysql_fetch_assoc($fromusr);
	$totalRows_fromusr = mysql_num_rows($fromusr);
  ?>
<?php // set background color by status
if($row_msg['status'] == 'new'){
	$bgcol = "#CCFFCC";
}elseif($row_msg['status'] == 'read'){ 	
	$bgcol = "#FFFFCC";
}elseif($row_msg['status'] == 'done'){ 	
	$bgcol = "#FFCCCC";
}else{ 	
	$bgcol = "#FFFFFF";
}?>      
<?php // set background color of fromuser by urgency
if($row_msg['urgent'] == 'R'){
	$bgcolurg = "lightblue";
}elseif($row_msg['urgent'] == 'A'){ 	
	$bgcolurg = "yellow";
}elseif($row_msg['urgent'] == 'U'){ 	
	$bgcolurg = "#ff6666";
}else{ 	
	$bgcolurg = "#FFFFFF";
}?>      

<?php if($totalRows_msg > 0){ ?>  <!--do not display if no record-->
    <tr>
      <td><?php echo $row_msg['id']; ?><br><?php echo $row_msg['urgent']; ?></td>
      <td bgcolor=<?php echo $bgcolurg ?> title="Also sent to: <?php echo $touserids;?>"><?php echo $row_msg['fromuser']; ?><br>
      <?php echo $row_msg['reply']; ?></td>  <!--tooltip of other user who received the message-->
      <!--<td bgcolor=<?php echo $bgcol ?>><strong><?php echo $row_msg['touser']; ?></strong></td>-->

      <td align="center" bgcolor=<?php echo $bgcol ?>  title="<?php echo $touserids;?>"><strong><?php echo $row_msg['status']; ?></strong></td>
<?php if($row_msg['openflag'] == 'Y'){?>
      <td align="center" bgcolor="#ececec" class="flagWhiteonBlue"><a href=index.php?msgid=<?php echo $row_msg['id']; ?>&openflag=Y class="flagWhiteonBlue"> Click to open new message </a></td>
<?php } else {?>
      <td bgcolor=<?php echo $bgcol ?>><textarea cols="60" readonly> <?php echo $row_msg['msg']; ?></textarea></td>
<?php } ?>
<!--select links to be displayed accordint to current selected status-->
<?php if($row_msg['status'] == 'new'){?>      
    <td bgcolor=<?php echo $bgcol ?>><a href="index.php?status=<?php echo $col_status ?>&statuschg=read&msgid=<?php echo $row_msg['id'] ?>">read</a></td>
    <td bgcolor=<?php echo $bgcol ?>><a href="index.php?status=<?php echo $col_status ?>&statuschg=done&msgid=<?php echo $row_msg['id'] ?>">done</a></td>
<?php } elseif($row_msg['status'] == 'read') { ?>  
    <td bgcolor=<?php echo $bgcol ?>><a href="index.php?status=<?php echo $col_status ?>&statuschg=new&msgid=<?php echo $row_msg['id'] ?>">new</a></td>
    <td bgcolor=<?php echo $bgcol ?>><a href="index.php?status=<?php echo $col_status ?>&statuschg=done&msgid=<?php echo $row_msg['id'] ?>">done</a></td>
<?php } elseif($row_msg['status'] == 'done') { ?>    <?php $date = date_create($row_msg['entrydt']);?>
    <td bgcolor=<?php echo $bgcol ?>><a href="index.php?status=<?php echo $col_status ?>&statuschg=new&msgid=<?php echo $row_msg['id'] ?>">new</a></td>
    <td bgcolor=<?php echo $bgcol ?>><a href="index.php?status=<?php echo $col_status ?>&statuschg=read&msgid=<?php echo $row_msg['id'] ?>">read</a></td>
<?php } else {?>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
<?php }?>
    <?php if(isset($row_msg['entrydt'])){  // set to blank if no entrydt otherwise it displays current date/time
			 $date = date_create($row_msg['entrydt']);?>
      <td bgcolor=<?php echo $bgcol ?>><?php echo date_format($date,'M-d-Y'); ?><br><?php echo date_format($date,'h:i A'); ?></td> <!--put date and time on separate lines-->
<?php } else { ?> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
<?php }?>

<!--display Reply link-->
<?php if($row_msg['status'] == 'new' || $row_msg['status'] == 'read'){ ?>      
     <td bgcolor=<?php echo $bgcol ?>><a href="javascript:void(0)" onClick="MM_openBrWindow('MsgCompose.php?msgid=<?php echo $row_msg['id']?>&idnum=<?php echo $row_fromusr['id'] ?>&touserid=<?php echo $row_msg['fromuser'];?>&subj=Reply','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')">Reply</a></td>
<?php }?>
<!--display Reply to All link-->
<?php if($row_msg['status'] == 'new' || $row_msg['status'] == 'read'){ ?>      
     <td bgcolor=<?php echo $bgcol ?>><a href="javascript:void(0)" onClick="MM_openBrWindow('MsgCompose.php?msgid=<?php echo $row_msg['id']?>&idnum=<?php echo $row_fromusr['id'].','.$toids;?>&touserid=<?php echo $row_msg['fromuser'].','.$touserids;?>&subj=Reply','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')">Reply <br>
      to All</a></td>
<?php }?>
<!--    <td>ReplyAll: <?php //echo $row_msg['touser'] ?></td> for testing
    <td>toids: <?php //echo $toids ?></td> for testing-->
  </tr>
  <?php }?>  <!-- from do not display if no record-->
    <?php } while ($row_msg = mysql_fetch_assoc($msg)); ?>
</table>
<?php 	}?>

<strong><?php echo $row_msg['fromuser']; ?></strong>
</body>
</html>
<?php
//mysql_free_result($msg);
//
//mysql_free_result($user);
//
//mysql_free_result($sender);

ob_end_flush();

?>
