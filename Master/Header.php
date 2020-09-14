<?php ob_start(); ?>
<?php //date_default_timezone_set('AMERICA/DETROIT')?>
<?php date_default_timezone_set('AFRICA/LAGOS')?>
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
} ?>
<?php
// if(!isset($_SESSION['sysconn'])) {
//    header('Location: ../security/stop.php');
//	} else { 
 require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']);
//	}
 ?>
<?php mysql_select_db($database_swmisconn, $swmisconn); ?>

<?php // https://stackoverflow.com/questions/19046812/multiple-count-for-multiple-conditions-in-one-query-mysql
		mysql_select_db($database_swmisconn, $swmisconn);
    $query_msg = "SELECT SUM(IF(urgent = 'R', 1, 0)) AS ROUT, SUM(IF(urgent = 'A', 1, 0)) AS ASAP, SUM(IF(urgent = 'U', 1, 0)) AS URGE FROM usrmsg WHERE INSTR(touser, 'x".$_SESSION['uid'].",') > 0 and status = 'new' and fromuser != '".$_SESSION['user']."'";
		$msg = mysql_query($query_msg, $swmisconn) or die(mysql_error());
		$row_msg = mysql_fetch_assoc($msg);
		$totalRows_msg = mysql_num_rows($msg);	

?>

<!--include function for security permit checks-->
<?php include_once($_SERVER['DOCUMENT_ROOT'].'/Len/functions/functions.php'); ?>
<!--check whether db token number is same as session token number-->
<?php  include_once($_SERVER['DOCUMENT_ROOT'].'/Len/functions/check_token.php'); ?>

<!--update users date time in the token table-->
<?php // query db for token to display in show session variables
mysql_select_db($database_swmisconn, $swmisconn);
$query_token = "SELECT id, userid, token, entrydt FROM token WHERE userid = '".$_SESSION['user']."'";
$token = mysql_query($query_token, $swmisconn) or die(mysql_error());
$row_token = mysql_fetch_assoc($token);
$totalRows_token = mysql_num_rows($token);

if($totalRows_token > 0){
// update user activity date time in token table
  $updateSQL = "UPDATE token SET entrydt = '".strtotime("now")."' WHERE id= '".$row_token['id']."'";
  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());	
	}
?> 

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta charset="utf-8" />
<title>MIS Header</title>
<link href="/Len/CSS/menustyle.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
   var mins = 30;  //Set the number of minutes you need
    var secs = mins * 60;
    var currentSeconds = 0;
    var currentMinutes = 0;
    setTimeout('Decrement()',1000);

    function Decrement() {
        currentMinutes = Math.floor(secs / 60);
        currentSeconds = secs % 60;
        if(currentSeconds <= 9) currentSeconds = "0" + currentSeconds;
        secs--;
        document.getElementById("timerText").innerHTML = currentMinutes + ":" + currentSeconds; //Set the element id you need the time put into.
        if(secs !== -1) setTimeout('Decrement()',1000);
    }
</script>
<script type="text/javascript">
var timer;
var wait=30;
document.onkeypress=resetTimer;
document.onmousemove=resetTimer;
function resetTimer()
{
    clearTimeout(timer);
    timer=setTimeout("logout()", 60000*wait);
}

function logout()
{
    window.location.href='../Security/Logout.php';
}
</script>
<script language="JavaScript" type="text/JavaScript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
   var win_position = ',left=200,top=300,screenX=200,screenY=300';
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

<style type="text/css">

.style_header24 {
	color: #deeafa;
	font-size: 24px;
	font-weight: bold;
}
.style_header16 {
	color: #deeafa;
	font-size: 16px;
	font-weight: bold;
}
.style_date {
	color: #b7f3b8;
	font-size: 12px;
	font-weight: bold;
}
.style_login {
	color: #FFFFFF;
	font-size: 16px;
	font-weight: bold;
}
</style>
  
</head>
<body onload="blinker();">
  <table width="1020px" height="100px" border="1" align="center">
    <tr>
 <?php if ($_SESSION['sysdata'] == 'BETHANY') {
 		$bkgd = "#577fae";
	 } else {
		$bkgd = "#ff9933";
     } 
     $mysiteurl = $_SERVER['REQUEST_URI'];
     ?>
      <td nowrap bgcolor=<?php echo $bkgd ?> width="800px" height="40px">
       <div id="div-header"><span class="style_header16"><?php echo $_SESSION['sysdata']; ?></span><span class="style_header16"> MEDICAL CENTER </span> <span class="style_date">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php	echo strtoupper(date('l  F d, Y   H:i')); ?> &nbsp; &nbsp; &nbsp;</span>
<?php if(isset($_SESSION['user'])) {  ?>
	     <span class="style_header16">User: &nbsp; 
	     <?php	echo strtoupper($_SESSION['user']); ?>
	     </span> &nbsp; &nbsp; &nbsp; &nbsp;<a href="../Security/Logout.php" class="style_login">Logout </a>
	    &nbsp; &nbsp; &nbsp;<span id= "timerText" class="style_date"></span>&nbsp; &nbsp; &nbsp;
      </div></td>
      <td width="25px" align="center" style="background-color:blue; color:white; font-size:24px;"><a href="javascript:void(0)" onclick="MM_openBrWindow('../Home/MsgPopUp.php?status=new&urg=R','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')"><span style="color:white;"><?php echo $row_msg['ROUT']?></span></a></td>
      <td width="25px" align="center" style="background-color:yellow; color:black; font-size:24px;"><a href="javascript:void(0)" onclick="MM_openBrWindow('../Home/MsgPopUp.php?status=new&urg=A','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')"><span style="color:blue;"><?php echo $row_msg['ASAP']?></span></a></td>
<?php if(isset($row_msg['URGE']) && $row_msg['URGE'] > 0) { ?>
      <td width="25px" align="center" style="background-color:red; color:white; font-size:24px;"><a href="javascript:void(0)" onclick="MM_openBrWindow('../Home/MsgPopUp.php?status=new&urg=U','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')"><blink><span style="color:white;"><?php echo $row_msg['URGE']?></span></blink></a></td>
<?php //} elseif(!isset($row_msg['URGE']))  {?>  <!--if there are no records with status 'new' there is no number displayed and no links avilable - thise line would make a 0 and link available-->
<!--      <td width="25px" align="center" style="background-color:black; color:white; font-size:24px;"><a href="javascript:void(0)" onclick="MM_openBrWindow('../Home/MsgPopUp.php?status=new&urg=U','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')">0</a></td>-->
<?php } else {?>
      <td width="25px" align="center" style="background-color:black; color:white; font-size:24px;"><a href="javascript:void(0)" onclick="MM_openBrWindow('../Home/MsgPopUp.php?status=new&urg=U','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')"><span style="color:white;"><?php echo $row_msg['URGE']?></span></a></td>
<?php }?>
<?php }
 	  else
	 {
	  $insertGoTo = "../Security/login.php";
	   header(sprintf("Location: %s", $insertGoTo));
?>
	  <!--  <a href="../Security/login.php" class="style_login"> Please Login </a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</div> -->

<?php } ?>
   </tr>
   
<?php $showsessionvars = "N" ?>
<?php if($showsessionvars == "Y") {
?> 
<?php // query db for token to display in show session variables
mysql_select_db($database_swmisconn, $swmisconn);
$query_token = "SELECT id, userid, token, entrydt FROM token WHERE userid = '".$_SESSION['user']."'";
$token = mysql_query($query_token, $swmisconn) or die(mysql_error());
$row_token = mysql_fetch_assoc($token);
$totalRows_token = mysql_num_rows($token);
?> 

  <tr>
		<td class="sidebarFooter">variables: &nbsp;&nbsp;&nbsp;&nbsp;     Session Token:<?php	echo($_SESSION['token']); ?>
dbtoken <?php echo $row_token['token']; ?>
<?php 
//echo $showsessionvars;
 
?>
**
<?php 
echo allow('11','4');
 
?>

<?php if (isset($_SESSION["user"])) {  ?>
		<?php echo 'session user = ' . $_SESSION['user']."<br/>"; ?>
		<?php echo 'session menuA = ' . $_SESSION['menuA']."<br/>"; ?>
		<?php echo 'session menu1 = ' . $_SESSION['menu1']."<br/>"; ?>
		<?php echo 'session menu2 = ' . $_SESSION['menu2']; ?>
		<?php echo 'session menu3 = ' . $_SESSION['menu3']."<br/>"; ?>
		<?php echo 'session swmis:  level : permit = ' . $_SESSION['swmis']; ?>
		
<?php } ?>		</td>
   </tr>
<?php } ?>
   
   <tr>
   <td rowspan="3">
<div>
<ul>
  <?php
// Creating query to fetch main information from mysql database table.
	$main_query = "select * from menu_main order by m_seq";
	$main_result = mysql_query($main_query);
	while($r = mysql_fetch_array($main_result)){
	if (isset($_SESSION["menuA"])) {
		$STPA = stripos($_SESSION["menuA"],':' . $r['m_permit']);
			If ($STPA > -1) {
				$url_link = "";
				if(strpos($mysiteurl,"Jiloa/medical/home/"))
				{
					$url_link = str_replace("../","../../",$r['m_link']);
				}
				else{
					$url_link = $r['m_link'];
				}
				?>
  <li><a href=<?php echo $url_link; ?>><?php echo $r['m_name'];?></a>
    <?php }}
?>
      <ul>
        <?php
	$sub1_query = "select * from menu_sub1 where m_id=".$r['id'];
	$sub1_result = mysql_query($sub1_query);
	while($r1 = mysql_fetch_array($sub1_result)){ 
		if (isset($_SESSION["menu1"])) {
			$STP1 = stripos($_SESSION["menu1"],':' . $r1['s1_permit']);
			If ($STP1 > -1) {
				$url_link = "";
				// echo $mysiteurl;
				if(strpos($mysiteurl,"Jiloa/medical/home"))
				{
					$url_link = str_replace("../","../../",$r1["s1_link"]);
				}
				else{
					$url_link = $r1['s1_link'];
				}
				?>
        <li><a href=<?php echo $url_link; ?>><?php echo $r1['s1_seq'] . "--" . $r1['s1_name']  ;?></a></li> 
		<!-- . "PM: " . $r1['s1_permit'] . "STRPOS: " . $STP1 -->
        <?php }}} ?>
      </ul>
  </li>
  <?php } ?>
</ul>
</div>	 </td>
     <td align="center" valign="top" class="Black_9_9">Routine</td>
     <td align="center" valign="top" class="Black_9_9">ASAP</td>
     <td align="center" valign="top" class="Black_9_9">Urgent</td>
   </tr>
   <tr>
			<td colspan="3" align="center" valign="top" class="Black_14">New Messages</td>
   </tr>
   <tr>
	     <td colspan="3" align="center"><strong><a href="javascript:void(0)" onClick="MM_openBrWindow('../Home/MsgCompose.php','StatusView','scrollbars=yes,resizable=yes,width=1000,height=600')">Compose</a></strong></td>
   </tr>
</table>
<?php
mysql_free_result($main_result);
mysql_free_result($sub1_result);
//ob_end_flush();  // different from testing server to make it work 
?></body></html>