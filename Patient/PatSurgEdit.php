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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
// if form1 submitted, this will run	
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
// chck for '' value in form; if '' set db value to NULL, otherwise it will save current date in database<br>
//convert date/time to timestamp (database stores timestamp)	
	if($_POST['scheddt'] == ''){$schedt = NULL;} else {$schedt = strtotime($_POST['scheddt'].' '.$_POST['scheddt_alt']);}
//  echo $_POST['sid'].'----'.'----';
//	echo $_POST['scheddt'].' '.$_POST['schedtime'].'----';
//	echo $schedt;
//	exit; 
 //update data in datbae from form1 

$_SESSION['sid'] = "";
if(isset($_POST['sid']))
$_SESSION['sid'] = $_POST['sid'];


  $updateSQL = sprintf("UPDATE surgery SET surgdate=%s, surgeon=%s, surgeonassist=%s, anesthetist=%s, preopdiag=%s, postopdiag=%s, incision=%s, findings=%s, procedures=%s, difficulties=%s, closure=%s, postoporders=%s, status=%s, entryby=%s, entrydt=%s WHERE id=%s",
                       GetSQLValueString($schedt, "int"),
                       GetSQLValueString($_POST['surgeon'], "int"),
                       GetSQLValueString($_POST['surgeonassist'], "int"),
                       GetSQLValueString($_POST['anesthetist'], "int"),
                       GetSQLValueString($_POST['preopdiag'], "text"),
                       GetSQLValueString($_POST['postopdiag'], "text"),
                       GetSQLValueString($_POST['incision'], "text"),
                       GetSQLValueString($_POST['findings'], "text"),
                       GetSQLValueString($_POST['procedures'], "text"),
                       GetSQLValueString($_POST['difficulties'], "text"),
                       GetSQLValueString($_POST['closure'], "text"),
                       GetSQLValueString($_POST['postoporders'], "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['sid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

// keep ansthesia status same as surgery
  $updateSQL = sprintf("UPDATE anesthesia SET status=%s WHERE surgid=%s",
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['sid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

  $updateSQL = sprintf("UPDATE orders SET status=%s WHERE id=%s",
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['ordid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());


// redirect browser to page just saved
  $updateGoTo = "PatShow1.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING']; 
  header(sprintf("Location: %s", $updateGoTo));
}
}
// if opening page without saving data, set mrn, visitid for query
$colname_mrn_surgupdate = "-1";
if (isset($_SESSION['mrn'])) {
  $colname_mrn_surgupdate = $_SESSION['mrn'];
}
// only used to provide visit for adding a procedure -  not needed
$colname_vid_surgupdate = "-1";
if (isset($_SESSION['vid'])) {
  $colname_vid_surgupdate = $_SESSION['vid'];
}
// echo $_SESSION['sid'];
// exit; 
$colname_sid_surgupdate = "-1";
  if(isset($_POST['sids'])) {  // When selected from procedure list
	   $colname_sid_surgupdate = $_POST['sids']; 
  } elseif(isset($_GET['sids'])){ // when coming from schedule
	   $colname_sid_surgupdate = $_GET['sids'];
  } elseif(isset($_GET['sid'])){   // When coming from Anesthesia
	   $colname_sid_surgupdate = $_GET['sid'];
  } elseif(isset($_SESSION['sid']) && $_SESSION['sid'] > 0){  // When saving form1
	   $colname_sid_surgupdate = $_SESSION['sid'];  
	} else {
                                            // qry to find first surgery not 'Complete' when coming from 'surgery' button
mysql_select_db($database_swmisconn, $swmisconn);
$query_firstord = sprintf("SELECT id selsid, MIN(entrydt) FROM surgery WHERE status != 'Complete' and status != 'Cancelled' and medrecnum = %s", GetSQLValueString($colname_mrn_surgupdate, "int"));
$firstord  = mysql_query($query_firstord , $swmisconn) or die(mysql_error());
$row_firstord  = mysql_fetch_assoc($firstord );
$totalRows_firstord  = mysql_num_rows($firstord );

	$colname_sid_surgupdate = $row_firstord['selsid'];
	}
// query to display the selected surgery
mysql_select_db($database_swmisconn, $swmisconn);
$query_surgupdate = sprintf("SELECT s.id sid, s.medrecnum, s.visitid, s.feeid, s.ordid, s.surgdate, s.surgeon, s.surgeonassist, s.anesthetist, s.anesttechnique, s.preopdiag, s.postopdiag, s.incision, s.findings, s.procedures, s.difficulties, s.closure, s.postoporders, s.status, s.begincount, s.endcount, s.entrydt, s.entryby, s.origvisitid, f.name, f.descr, DATE_FORMAT(v.visitdate,'%%d-%%b-%%Y') visitdate FROM surgery s join fee f on s.feeid = f.id join patvisit v on s.visitid = v.id WHERE s.medrecnum = %s and s.id = %s", GetSQLValueString($colname_mrn_surgupdate, "int"), GetSQLValueString($colname_sid_surgupdate, "int"));
$surgupdate = mysql_query($query_surgupdate, $swmisconn) or die(mysql_error());
$row_surgupdate = mysql_fetch_assoc($surgupdate);
$totalRows_surgupdate = mysql_num_rows($surgupdate);

//Query for list of surgery(s) not complete for DDL
mysql_select_db($database_swmisconn, $swmisconn);
$query_allsurg = sprintf("SELECT s.id selsid, s.medrecnum, s.visitid,  f.name, f.descr, DATE_FORMAT(p.visitdate,'%%d-%%b-%%Y') visitdate FROM surgery s join fee f on s.feeid = f.id join patvisit p on p.id = s.visitid WHERE f.section in('Minor', 'Intermediate', 'Major') and s.status != 'Complete' and s.status != 'Cancelled' and s.medrecnum = %s", GetSQLValueString($colname_mrn_surgupdate, "int"));
$allsurg = mysql_query($query_allsurg, $swmisconn) or die(mysql_error());
$row_allsurg = mysql_fetch_assoc($allsurg);
$totalRows_allsurg = mysql_num_rows($allsurg);

// query for surgeon and assisting surgeon ddl
mysql_select_db($database_swmisconn, $swmisconn);
$query_surgeon = "SELECT id uid, userid FROM users WHERE (docflag = 'Y' or anflag = 'Y') and active = 'Y' ORDER BY userid";
$surgeon = mysql_query($query_surgeon, $swmisconn) or die(mysql_error());
$row_surgeon = mysql_fetch_assoc($surgeon);
$totalRows_surgeon = mysql_num_rows($surgeon);

//  query for anesthetist ddl
mysql_select_db($database_swmisconn, $swmisconn);
$query_anesthetist = "SELECT id uid, userid FROM users WHERE (anflag = 'Y' or docflag = 'Y') and active = 'Y' ORDER BY userid";
$anesthetist = mysql_query($query_anesthetist, $swmisconn) or die(mysql_error());
$row_anesthetist = mysql_fetch_assoc($anesthetist);
$totalRows_anesthetist = mysql_num_rows($anesthetist);

$colname_cnt = "-1";
if (isset($_GET['sids'])) {
  $colname_cnt = $_GET['sids'];
} elseif(isset($_GET['sid']))  {   // When coming from Anesthesia
	$colname_cnt = $_GET['sid'];
}
		 mysql_select_db($database_swmisconn, $swmisconn);
$query_begincnt = sprintf("SELECT id, surgid, swabs, arteryforceps, aliceforceps, toothdissectingforceps, plaindisectingforceps, abdominalmops, scissors, bladehandles, blades, grayaimitage, langendeckretractor, doyensretractor, needleholders, needles, entryby, entrydt FROM surgcountbegin WHERE surgid = %s", GetSQLValueString($colname_cnt, "int"));
$begincnt = mysql_query($query_begincnt, $swmisconn) or die(mysql_error());
$row_begincnt = mysql_fetch_assoc($begincnt);
$totalRows_begincnt = mysql_num_rows($begincnt);


mysql_select_db($database_swmisconn, $swmisconn);
$query_endcnt = sprintf("SELECT id, surgid, swabs, arteryforceps, aliceforceps, toothdissectingforceps, plaindisectingforceps, abdominalmops, scissors, bladehandles, blades, grayaimitage, langendeckretractor, doyensretractor, needleholders, needles, entryby, entrydt FROM surgcountend WHERE surgid = %s", GetSQLValueString($colname_cnt, "int"));
$endcnt = mysql_query($query_endcnt, $swmisconn) or die(mysql_error());
$row_endcnt = mysql_fetch_assoc($endcnt);
$totalRows_endcnt = mysql_num_rows($endcnt);
?>


<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SURGERY UPDATE</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
   var win_position = ',left=10,top=50,screenX=10,screenY=50';
   var newWindow = window.open(theURL,winName,features+win_position);
   newWindow.focus();
}
</script>

<script language="JavaScript" src="../../javascript_form/gen_validatorv4.js" type="text/javascript" xml:space="preserve"></script>
<script rel="text/javascript" src="../../jquery-1.11.1.js"></script>
<!-- for autosave-->
<script src="../../autosaveform.js">
//***********************************************
//* Auto Save Form script (c) Dynamic Drive (www.dynamicdrive.com)
//* This notice MUST stay intact for legal use
//* Visit http://www.dynamicdrive.com/ for this script and 100s more.
//**********************************************
//http://www.dynamicdrive.com/dynamicindex16/autosaveform.htm
</script>
<script>
		var formsave1=new autosaveform({
		formid: 'formpn1',
		pause: 1000 //<--no comma following last option!
    })
</script>

<!-- tinymce text editor-->
<script src="../../tinymce/js/tinymce/tinymce.js"></script>  <!--(tinymce.min.js does not work on this PC)-->
<script>
tinymce.init({ 
  	//selector:'textarea#diagnosis', 
	mode : 'textareas',
	editor_selector : 'surgdata',   //textarea  must have class="notes"
	content_css : '../../CSS/content.css',
	min_height: 10,
	width: 875,
	autoresize_max_height: 400,
	autoresize_min_height: 10,
	autoresize_bottom_margin: 0,
	autoresize_top_margin: 0,
	menubar: false,
	statusbar: false,
	toolbar: false,
/*	toolbar_items_size : 'small',
	toolbar: 'bold italic underline | bullist  numlist | indent outdent | alignleft aligncenter alignright | superscript subscript | preview',
*/
    plugins: 'autoresize, autosave',     /*preview,*/
  	autosave_ask_before_unload: false,
		autosave_interval: "20s",
		autosave_restore_when_empty: true,
		autosave_retention: "30m"
	 });
</script>

<!-- for date & time picker-->
<link rel="stylesheet" href="../../js/jquery-ui-1.12.1.custom/jquery-ui.min.css">
<link rel="stylesheet" href="../../js/jquery-ui-timepicker-addon.css">
<script src="../../js/jquery.js"></script>
<script src="../../js/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script src="../../js/jquery-ui-timepicker-addon.js"></script>

<!-- postopexamdt-->
<script>
$( function() {
$('#scheddt').datetimepicker({
	altField: "#scheddt_alt",
	controlType: 'select',
	oneLine: true,
	dateFormat: 'D M d, yy',
	timeFormat: 'hh:mm tt', 
  stepMinute: 5,
	buttonImage: "../../js/jquery-ui-1.12.1.custom/images/calendar.gif",
	showOn: "both", 
  buttonImageOnly: false
});
});
</script>

</head>
<body>

<div align="right"> <span class="Black_12">Visit Date: </span><span class="BlackBold_16"><?php echo $row_surgupdate['visitdate'] ?></span><?php echo str_repeat("&nbsp;", 15);?> <span class="BlueBold_24">UPDATE SURGERY</span> 
         <!--  link to Anesthersia page-->
        <span><?php echo str_repeat("&nbsp;", 15);?></span>
        <span><a href="PatShow1.php?mrn=<?php echo $row_surgupdate['medrecnum'] ?>&vid=<?php echo $row_surgupdate['visitid'] ?>&sid=<?php echo $row_surgupdate['sid'] ?>&visit=PatVisitView.php&act=lab&pge=PatAnestPreopEdit.php"  style="background-color:green; border-color:blue; color:white; text-align:center; border-radius:5px;" class="BlueBold_24">Anest Preop</a></span>&nbsp;&nbsp;
        <span><a href="PatShow1.php?mrn=<?php echo $row_surgupdate['medrecnum'] ?>&vid=<?php echo $row_surgupdate['visitid'] ?>&sid=<?php echo $row_surgupdate['sid'] ?>&visit=PatVisitView.php&act=lab&pge=PatAnestIntraopEdit.php" style="background-color:yellow; border-color:blue; color:black; text-align:center;border-radius:5px;" class="BlueBold_24">Anest Intraop</a></span>&nbsp;&nbsp;
        <span><a href="PatShow1.php?mrn=<?php echo $row_surgupdate['medrecnum'] ?>&vid=<?php echo $row_surgupdate['visitid'] ?>&sid=<?php echo $row_surgupdate['sid'] ?>&visit=PatVisitView.php&act=lab&pge=PatAnestPostopEdit.php" style="background-color:orange; border-color:blue; color:white; text-align:center; border-radius:5px;" class="BlueBold_24">Anest Postop</a></span>
</div>
<table bgcolor="#f9d2f9">
  <tr>
  	<td valign="top">
      <table>
      	<tr>
          <td>
  <!--Surgery selection table-->
            <table align="center" width="20%" border="1" cellspacing="1" cellpadding="1" >
      <form name="sidselect" id="sidselect" method="POST" action="PatShow1.php?mrn=<?php echo $row_allsurg['medrecnum']?>&vid=<?php echo $row_allsurg['visitid']?>&visit=PatVisitView.php&act=lab&pge=PatSurgEdit.php">
             <tr>
                <td align="center">Patient Surgery(s) - Not Complete <?php //echo $colname_sid_surgupdate ?></td>
              </tr>
              <tr> 
              <?php if($totalRows_allsurg == 0){?>
              <td nowrap="nowrap" class="RedBold_24"> ALL are Complete</td>
             <?php  } else {?>
                <td><select name="sids" size="5" onChange="document.sidselect.submit();">&nbsp;
               <?php
      do {  
      ?>
               <option value="<?php echo $row_allsurg['selsid']?>"<?php if (!(strcmp($row_allsurg['selsid'], $row_surgupdate['sid']))) {echo "selected=\"selected\"";} ?>><?php echo 'visit: '.$row_allsurg['visitdate'].' Surg: '.$row_allsurg['name']?></option>
               <?php
      } while ($row_allsurg = mysql_fetch_assoc($allsurg));
        $rows = mysql_num_rows($allsurg);
        if($rows > 0) {
            mysql_data_seek($allsurg, 0);
          $row_allsurg = mysql_fetch_assoc($allsurg);
        }
      ?>
            </select></td>
            <?php  } ?>
            </tr>
             </form>
            </table>
          </td>
        </tr>
        <tr>
        	<td align="center">
						<table>
            	<tr>
              	<td align="center">
                  <table>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td  colspan="3" align="center"><span class="BlueBold_16">Surgical Items Count</span></td>
                    </tr>
                     <tr>
                      <td align="left"  class="BlueBold_12">Item</td>
                      <td align="center"  class="BlueBold_12">Begin</td>
                      <td align="center"  class="BlueBold_12">End</td>
                      </tr>
                      <tr>
                      <td>Swabs</td>
                      <td><input name="swabs" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['swabs']; ?>"></td>
                      <td><input name="swabs" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['swabs']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Arteryforceps</td>
                      <td><input name="arteryforceps" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['arteryforceps']; ?>"></td>
                      <td><input name="arteryforceps" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['arteryforceps']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Aliceforceps</td>
                      <td><input name="aliceforceps" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['aliceforceps']; ?>"></td>
                      <td><input name="aliceforceps" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['aliceforceps']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Toothdissectingforceps</td>
                      <td><input name="toothdissectingforceps" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['toothdissectingforceps']; ?>"></td>
                      <td><input name="toothdissectingforceps" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['toothdissectingforceps']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Plaindisectingforceps</td>
                      <td><input name="plaindisectingforceps" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['plaindisectingforceps']; ?>"></td>
                      <td><input name="plaindisectingforceps" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['plaindisectingforceps']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Abdominalmops</td>
                      <td><input name="abdominalmops" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['abdominalmops']; ?>"></td>
                      <td><input name="abdominalmops" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['abdominalmops']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Scissors</td>
                      <td><input name="scissors" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['scissors']; ?>"></td>
                      <td><input name="scissors" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['scissors']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Bladehandles</td>
                      <td><input name="bladehandles" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['bladehandles']; ?>"></td>
                      <td><input name="bladehandles" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['bladehandles']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Blades</td>
                      <td><input name="blades" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['blades']; ?>"></td>
                      <td><input name="blades" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['blades']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Grayaimitage</td>
                      <td><input name="grayaimitage" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['grayaimitage']; ?>"></td>
                      <td><input name="grayaimitage" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['grayaimitage']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Langendeckretractor</td>
                      <td><input name="langendeckretractor" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['langendeckretractor']; ?>"></td>
                      <td><input name="langendeckretractor" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['langendeckretractor']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Doyensretractor</td>
                      <td><input name="doyensretractor" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['doyensretractor']; ?>"></td>
                      <td><input name="doyensretractor" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['doyensretractor']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Needleholders</td>
                      <td><input name="needleholders" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['needleholders']; ?>"></td>
                      <td><input name="needleholders" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['needleholders']; ?>"></td>
                    </tr>
                    <tr>  
                      <td>Needles</td>
                      <td><input name="needles" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_begincnt['needles']; ?>"></td>
                      <td><input name="needles" type="text" size="1" maxlength="3" disabled style="text-align:center;" autocomplete="off" value="<?php echo $row_endcnt['needles']; ?>"></td>
                    </tr>
                  </td>
                </tr>
              </table>
                <tr>
                  <td>
                    <table align="center">
                      <tr>
                        <td colspan="2"></td>
                      </tr>
                      <tr>
                        <td nowrap align="center"  class="BlueBold_18">Begin Total</td>
                        <td nowrap align="center"  class="BlueBold_18">End Total</td>
                      </tr>
                      <tr>
                        <td><input style="background-color:#fffdda; text-align:center; font-size:24px;"  name="begin" type="text" size="5" maxlength="5" readonly value=<?php echo $row_surgupdate['begincount'] ?>></td>
                        <td><input style="background-color:#fffdda; text-align:center; font-size:24px;"  name="begin" type="text" size="5" maxlength="5" readonly value=<?php echo $row_surgupdate['endcount'];?>></td>
                      </tr>
                      <tr>
                        <td align="center">	  <a href="javascript:void(0)" onClick="MM_openBrWindow('PatSurgCount.php?surgid=<?php echo $row_surgupdate['sid'];?>&begin=<?php echo $row_surgupdate['begincount'] ?>&end=<?php echo $row_surgupdate['endcount'];?>&edit=begin','StatusView','scrollbars=yes,resizable=yes,width=400,height=600')"><div style="background-color:aqua; border-color:blue; color:black; text-align: center; border-radius:4px;">Update</div></a></td>
                        <td align="center">	  <a href="javascript:void(0)" onClick="MM_openBrWindow('PatSurgCount.php?surgid=<?php echo $row_surgupdate['sid']; ?>&begin=<?php echo $row_surgupdate['begincount'] ?>&end=<?php echo $row_surgupdate['endcount'];?>&edit=end','StatusView','scrollbars=yes,resizable=yes,width=400,height=600')"><div style="background-color:aqua; border-color:blue; color:black; text-align: center; border-radius:4px;">Update</div></a></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
			</td>    
   <!-- surgery information table/form-->
			<td valign="top">
				<table width="80%" border="1" align="center" cellpadding="1" cellspacing="1">
          <form name="form1" method="POST" action="<?php echo $editFormAction; ?>">
      <?php   $bgc = "#CCCCCC";
				if($row_surgupdate['status'] == 'Ordered'){ $bgc = "#7ac701"; }
				if($row_surgupdate['status'] == 'Scheduled'){ $bgc = "#FFFDDA"; }
				if($row_surgupdate['status'] == 'In-Progress'){ $bgc = "#d3e4f4"; }
				if($row_surgupdate['status'] == 'Recovery'){ $bgc = "#ff9800"; }
				if($row_surgupdate['status'] == 'Complete'){ $bgc = "#e6442e"; }
				if($row_surgupdate['status'] == 'Cancelled'){ $bgc = "#e6442e"; }
			
			?>
      
					<tr>
						<td align="right" title="Surg Id: <?php echo $row_surgupdate['sid'] ?>&#10;MRN: <?php echo $row_surgupdate['medrecnum'] ?>&#10;Visit Id: <?php echo $row_surgupdate['visitid'] ?>&#10;Order ID: <?php echo $row_surgupdate['ordid'] ?>&#10; OrigVisitid: <?php echo $row_surgupdate['origvisitid'] ?>" bgcolor="#FFFDDA">Surgery:</td>
            <td title="Surg Id: <?php echo $row_surgupdate['sid'] ?>&#10;MRN: <?php echo $row_surgupdate['medrecnum'] ?>&#10;Visit Id: <?php echo $row_surgupdate['visitid'] ?>&#10;Order ID: <?php echo $row_surgupdate['ordid'] ?>" bgcolor=<?php echo $bgc ?>>Status: <input type="text" name="statusdisp" id="statusdisp" size="7" readonly value= "<?php echo $row_surgupdate['status'] ?>"></td>
  <!--Display Surgery name-->
            <td align="right" scope="row">Surgery Name:</td>
            <td colspan="2" title ="Feeid : <?php echo $row_surgupdate['feeid'] ?>">
              <input type="text" name="surgname" id="surgname" size="50" readonly value="<?php echo $row_surgupdate['name'].': '.$row_surgupdate['descr']?>">
            </td>
            <td><input type="button" name = "button25" class="btngradblu85" value="Add Surgery" onClick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&visit=PatVisitView.php&act=lab&pge=PatSurgOrder.php'" /></td>
  
          </tr>
          <tr>
   <!-- Display/Update Surg date/Time-->
             <td height="18" align="right" scope="row">Surgery Date/Time:</td>
       <?php if(isset($row_surgupdate['surgdate'])) { ?>    
             <td align="left" nowrap="nowrap">
               <input id="scheddt" name="scheddt" type="text" size="12" maxlength="15" autocomplete= "off" value="<?php echo  date('D M d, Y', $row_surgupdate['surgdate']);?>" />
               <input id="scheddt_alt" name="scheddt_alt" type="text" size="8" maxlength="15" autocomplete= "off" value="<?php echo  date('h:i a', $row_surgupdate['surgdate']) ;?>" />
             </td>
        <?php } else {?>
             <td align="left" nowrap="nowrap">
               <input id="scheddt" name="scheddt" type="text" size="12" maxlength="15" autocomplete= "off" value="" />       
               <input id="scheddt_alt" name="scheddt_alt" type="text" size="8" maxlength="10" autocomplete= "off" value="" />
						</td>
            
      <?php }?>
<!--  link to Add Procedure page-->          
<!--  Select Surgeon-->          
						<td nowrap="nowrap">Surgeon:<select name="surgeon">
                  <option value=""  <?php if (!(strcmp('', $row_surgupdate['surgeon']))) {echo "selected=\"selected\"";} ?>>Select</option>
            <?php
      do {  
      ?>
            <option value="<?php echo $row_surgeon['uid']?>"<?php if (!(strcmp($row_surgeon['uid'], $row_surgupdate['surgeon']))) {echo "selected=\"selected\"";} ?>><?php echo $row_surgeon['userid']?></option>
            <?php
      } while ($row_surgeon = mysql_fetch_assoc($surgeon));
        $rows = mysql_num_rows($surgeon);
        if($rows > 0) {
            mysql_data_seek($surgeon, 0);
          $row_surgeon = mysql_fetch_assoc($surgeon);
        }
      ?>
          </select></td>
<!--  Select Surgeon Assistant-->          
						<td nowrap>Surgeon Assistant:
            <select name="surgeonassist">
                  <option value=""  <?php if (!(strcmp('', $row_surgupdate['surgeonassist']))) {echo "selected=\"selected\"";} ?>>Select</option>

            <?php
      do {  
      ?>
            <option value="<?php echo $row_surgeon['uid']?>"<?php if (!(strcmp($row_surgeon['uid'], $row_surgupdate['surgeonassist']))) {echo "selected=\"selected\"";} ?>><?php echo $row_surgeon['userid']?></option>
            <?php
      } while ($row_surgeon = mysql_fetch_assoc($surgeon));
        $rows = mysql_num_rows($surgeon);
        if($rows > 0) {
            mysql_data_seek($surgeon, 0);
          $row_surgeon = mysql_fetch_assoc($surgeon);
        }
      ?>      
          </select></td>
 
<!--  Select Anesthetist-->          
						<td nowrap>Anesthetist:
  <select name="anesthetist">
    <option value=""  <?php if (!(strcmp('', $row_surgupdate['anesthetist']))) {echo "selected=\"selected\"";} ?>>Select</option>
 
    <?php do {   ?>
    <option value="<?php echo $row_anesthetist['uid']?>"<?php if (!(strcmp($row_anesthetist['uid'], $row_surgupdate['anesthetist']))) {echo "selected=\"selected\"";} ?>><?php echo $row_anesthetist['userid']?></option>
    <?php
} while ($row_anesthetist = mysql_fetch_assoc($anesthetist));
  $rows = mysql_num_rows($anesthetist);
  if($rows > 0) {
      mysql_data_seek($anesthetist, 0);
	  $row_anesthetist = mysql_fetch_assoc($anesthetist);
  }
?>
  </select></td>

						<td class><input type="button" name = "button25" class="btngradblu85" value="Add Anesthesia" onClick="parent.location='PatShow1.php?mrn=<?php echo $row_surgupdate['medrecnum']; ?>&user=<?php echo $_SESSION['user'];?>&visitid=<?php echo $row_surgupdate['visitid']?>&sid=<?php echo $row_surgupdate['sid'];?>&visit=PatVisitView.php&act=lab&pge=PatAnestOrd.php'" />

						</td>
          </tr>
  <!--enter/display preop diagnosis-->
          <tr>
            <td align="right" scope="row">Pre-Op Diagnosis</td>
            <td colspan="5" valign="top"><textarea name="preopdiag" id="preopdiag" class="surgdata" ><?php echo $row_surgupdate['preopdiag']?></textarea></td>
          </tr>
          <tr>
            <td align="right">Incision:</td>
            <td colspan="5"><textarea name="incision" id="incision" class="surgdata"><?php echo $row_surgupdate['incision']?></textarea></td>
          </tr>
          <tr>
            <td align="right">Findings:</td>
            <td colspan="5"><textarea name="findings" id="findings" class="surgdata"><?php echo $row_surgupdate['findings']?></textarea></td>
          </tr>
          <tr>
            <td align="right">Procedure:</td>
            <td colspan="5"><textarea name="procedures" id="procedures" class="surgdata"><?php echo $row_surgupdate['procedures']?></textarea></td>
          </tr>
          <tr>
            <td align="right">Difficulties:</td>
            <td colspan="5"><textarea name="difficulties" id="difficulties" class="surgdata"><?php echo $row_surgupdate['difficulties']?></textarea></td>
          </tr>
          <tr>
            <td align="right">Closure:</td>
            <td colspan="5"><textarea name="closure" id="closure" class="surgdata"><?php echo $row_surgupdate['closure']?></textarea></td>
          </tr>
          <tr>
            <td align="right" scope="row">Post-Op Diagnosis:</td>
            <td colspan="5"><textarea name="postopdiag" id="postopdiag" class="surgdata"><?php echo $row_surgupdate['postopdiag']?></textarea></td>
          </tr>
          <tr>
            <td align="right" scope="row">Post-Op Orders:</td>
            <td colspan="5"><textarea name="postoporders" id="postoporders" class="surgdata"><?php echo $row_surgupdate['postoporders']?></textarea></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="5">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          <!--Status selector -->         
            <td>Status:
          <select name="status" id="status">
            <option value="Ordered" <?php if (!(strcmp("Ordered", $row_surgupdate['status']))) {echo "selected=\"selected\"";} ?>>Ordered</option>
            <option value="Scheduled" <?php if (!(strcmp("Scheduled", $row_surgupdate['status']))) {echo "selected=\"selected\"";} ?>>Scheduled</option>
            <option value="In-Progress" <?php if (!(strcmp("In-Progress", $row_surgupdate['status']))) {echo "selected=\"selected\"";} ?>>In-Progress</option>
            <option value="Recovery" <?php if (!(strcmp("Recovery", $row_surgupdate['status']))) {echo "selected=\"selected\"";} ?>>Recovery</option>
            <option value="Complete" <?php if (!(strcmp("Complete", $row_surgupdate['status']))) {echo "selected=\"selected\"";} ?>>Complete</option>
            <option value="Cancelled" <?php if (!(strcmp("Cancelled", $row_surgupdate['status']))) {echo "selected=\"selected\"";} ?>>Cancelled</option>
          </select>
						</td>
<!--make sid number available as $_POST['sid'] -->        
      <input type="hidden" name="sid" id="sid" value="<?php echo $row_surgupdate['sid'] ?>">
<!--make ordid number available as $_POST['ordid'] -->        
      <input type="hidden" name="ordid" id="ordid" value="<?php echo $row_surgupdate['ordid'] ?>">
<!--make formname available as $_POST['MM_update'] -->
      <input type="hidden" name="MM_update" value="form1">
<!--Submit form-->
        <input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
        <input name="entrydt" type="hidden" id="entrydt" value="<?php echo date("Y-m-d H:i:s"); ?>" />

						<td>  <input type="submit" name="Add" id="Add" class="BlueBold_16" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="Update Surgery"></td>
         </tr>
  </form>
			</table>
    </td>
  </tr>
</table>

</body>
</html><?php
mysql_free_result($surgupdate);

mysql_free_result($surgeon);

mysql_free_result($begincnt);

?>
