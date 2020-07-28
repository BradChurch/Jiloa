<?php  ob_start();?>
<?php require_once('../../Connections/swmisconn.php'); ?>
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
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
<!-- initialize session variables for Compose page-->
<?php $_SESSION['seldusr'] = '';
			$_SESSION['userid'] = '';
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

//-select query by urgency-->
   $col_urg = "R";
   if(isset($_GET['urg'])) {
   $col_urg = $_GET['urg'];
}
?>
<?php 
 // if selected status = 'new, 'read' or 'done'
// select records from usrmsg table where $myid is in 'touser' field and selected status
			mysql_select_db($database_swmisconn, $swmisconn);
			$query_msg = "SELECT id, fromuser, touser, status, urgent, openflag, reply, msg, entrydt FROM usrmsg WHERE fromuser != '".trim($_SESSION['user'])."' and INSTR(touser, 'x".$_SESSION['uid'].",') >0 and status = '".$col_status."' and urgent = '".$col_urg."' ORDER BY id DESC";
			$msg = mysql_query($query_msg, $swmisconn) or die(mysql_error());
			$row_msg = mysql_fetch_assoc($msg);
			$totalRows_msg = mysql_num_rows($msg);

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MSG PopUp</title>
<link href="/Len/css/Level3_1.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript"></script>
<script language="JavaScript" type="text/JavaScript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
   var win_position = ',left=200,top=300,screenX=200,screenY=300';
   var newWindow = window.open(theURL,winName,features+win_position);
   newWindow.focus();
} </script>

<script language="JavaScript" type="text/JavaScript">
function out(){
	opener.location.reload(1); //This updates the data on the calling page
	  self.close();
} </script>
<script src="../../tinymce/js/tinymce/tinymce.min.js"></script>
<script src="../../tinymce/tinymce-emoji-master/dist/tinymce-emoji/plugin.min.js"></script>
<script>
tinymce.init({ 
  selector:'textarea#msg', 
	//mode : 'textareas',
	//editor_selector : 'msg',   //textarea  must have class="msg"
	content_css : '../../CSS/content.css',
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
    plugins: 'tinymceEmoji, autoresize',
	 });
</script>

</head>

<body>
<table align="center">
	<tr>
	  <td class="BlackBold_16">BMC Message System</td>
  </tr>
</table>  
<table align="center" width="500px">
	<tr>
  	<td align="center">
    	<!-- table1 - From plus close plus display selected user ids for testing-->
      <table align="center">
				<tr>
          <td colspan="2" nowrap="nowrap" bgcolor="#00FFFF" class="BlueBold_24">New Message(s) to: 
            <input name="msgto" type="text" class="BlueBold_24" size="20" maxlength="30" readonly  value="<?php echo $_SESSION['user'] ?>"/></td>
					<td class="BlueBold_24">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    			<td><strong><a href="javascript:void(0)" class="BlueBold_20" onclick="MM_openBrWindow('MsgCompose.php','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')">Compose</a></strong></td>
					<td class="BlueBold_24">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    		<td class="RedBold_24" ><div align="center"><input name="button" type="button" style="background-color:#f81829" onclick="out()" value="Close" /></a></div></td>
        </tr>
      </table>
    </td>
  </tr>
</table>


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
  <?php 	//mysql_select_db($database_swmisconn, $swmisconn);
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
<?php //echo $totalRows_msg;?><br>
<?php //echo $_SESSION['uid']?><br>
<?php if($totalRows_msg > 0){ ?>  <!--do not display if no record-->
    <tr>
      <td><?php echo $row_msg['id']; ?><br><?php echo $row_msg['urgent']; ?></td>
      <td bgcolor=<?php echo $bgcolurg ?> title="Also sent to: <?php echo $touserids;?>"><?php echo $row_msg['fromuser']; ?><br>
      <?php echo $row_msg['reply']; ?></td>  <!--tooltip of other user who received the message-->
      <!--<td bgcolor=<?php echo $bgcol ?>><strong><?php echo $row_msg['touser']; ?></strong></td>-->

      <td align="center" bgcolor=<?php echo $bgcol ?>  title="<?php echo $touserids;?>"><strong><?php echo $row_msg['status']; ?></strong></td>
      <?php if($row_msg['openflag'] == 'Y'){?>
      <td align="center" bgcolor="#ececec" class="flagWhiteonBlue"><a href=MsgPopUp.php?msgid=<?php echo $row_msg['id']; ?>&openflag=Y class="flagWhiteonBlue"> Click to open new message </a></td>
<?php } else {?>
      <td bgcolor=<?php echo $bgcol ?>><textarea cols="60" readonly> <?php echo $row_msg['msg']; ?></textarea></td>
<?php } ?>
<!--select links to be displayed accordint to current selected status-->
<?php if($row_msg['status'] == 'new'){?>      
    <td bgcolor=<?php echo $bgcol ?>><a href="MsgPopUp.php?status=<?php echo $col_status ?>&statuschg=read&msgid=<?php echo $row_msg['id'] ?>">read</a></td>
    <td bgcolor=<?php echo $bgcol ?>><a href="MsgPopUp.php?status=<?php echo $col_status ?>&statuschg=done&msgid=<?php echo $row_msg['id'] ?>">done</a></td>
<?php } elseif($row_msg['status'] == 'read') { ?>  
    <td bgcolor=<?php echo $bgcol ?>><a href="MsgPopUp.php?status=<?php echo $col_status ?>&statuschg=new&msgid=<?php echo $row_msg['id'] ?>">new</a></td>
    <td bgcolor=<?php echo $bgcol ?>><a href="MsgPopUp.php?status=<?php echo $col_status ?>&statuschg=done&msgid=<?php echo $row_msg['id'] ?>">done</a></td>
<?php } elseif($row_msg['status'] == 'done') { ?>    <?php $date = date_create($row_msg['entrydt']);?>
    <td bgcolor=<?php echo $bgcol ?>><a href="MsgPopUp.php?status=<?php echo $col_status ?>&statuschg=new&msgid=<?php echo $row_msg['id'] ?>">new</a></td>
    <td bgcolor=<?php echo $bgcol ?>><a href="MsgPopUp.php?status=<?php echo $col_status ?>&statuschg=read&msgid=<?php echo $row_msg['id'] ?>">read</a></td>
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
     <td bgcolor=<?php echo $bgcol ?>><a href="javascript:void(0)" onclick="MM_openBrWindow('MsgCompose.php?msgid=<?php echo $row_msg['id']?>&idnum=<?php echo $row_fromusr['id'] ?>&touserid=<?php echo $row_msg['fromuser'];?>&subj=Reply','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')">Reply</a></td>
<?php }?>
<!--display Reply to All link-->
<?php if($row_msg['status'] == 'new' || $row_msg['status'] == 'read'){ ?>      
     <td bgcolor=<?php echo $bgcol ?>><a href="javascript:void(0)" onclick="MM_openBrWindow('MsgCompose.php?msgid=<?php echo $row_msg['id']?>&idnum=<?php echo $row_fromusr['id'].','.$toids;?>&touserid=<?php echo $row_msg['fromuser'].','.$touserids;?>&subj=reply','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')">Reply <br>
      to All</a></td>
<?php }?>
<!--    <td>ReplyAll: <?php //echo $row_msg['touser'] ?></td> for testing
    <td>toids: <?php //echo $toids ?></td> for testing-->
  </tr>
  <?php }?>  <!-- from do not display if no record-->
    <?php } while ($row_msg = mysql_fetch_assoc($msg)); ?>
</table>


<strong><?php echo $row_msg['fromuser']; ?></strong>

</body>
</html>
<?php 
ob_end_flush();
?>