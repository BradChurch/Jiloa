<?php  $pt = "POC Result Entry Add"; ?>
<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>

<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
 $colname_feeid = -1;
if (isset($_POST['feeid'])) {
  $colname_feeid = (get_magic_quotes_gpc()) ? $_POST['feeid'] : addslashes($_POST['feeid']);
//check that thre is a result to save	by  count number of blank results to determine resulted status of order
	mysql_select_db($database_swmisconn, $swmisconn);
  $query_tests = "SELECT t.id tid FROM tests t Where '".$colname_feeid."' in (t.feeid1, t.feeid2, t.feeid3, t.feeid4, t.feeid5, t.feeid6, t.feeid7, t.feeid8, t.feeid9, t.feeid0, t.feeidA, t.feeidB, t.feeidC, t.feeidD, t.feeidE, t.feeidF, t.feeidG, t.feeidH, t.feeidI, t.feeidJ, t.feeidK, t.feeidL, t.feeidM, t.feeidN ) and t.active = 'Y' and flag1 is null ORDER BY reportseq ";
	$result = mysql_query($query_tests, $swmisconn) or die(mysql_error());
	$row_result = mysql_fetch_assoc($result);	
	$totalRows_result = mysql_num_rows($result);
//echo 'TID:'.$row_result['tid'].'<br>';

$noresult = 0; 
 do {
			if (strlen(strval($_POST['result'.$row_result['tid']])) == 0) {
			$noresult = $noresult + 1 ;
			}
		} while ($row_result = mysql_fetch_assoc($result)); 

 if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1" && $noresult == 0)) {
// find fee for ordered test
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_Fee = sprintf("SELECT fee from fee where id = '".$colname_feeid."'");
	$Fee = mysql_query($query_Fee, $swmisconn) or die(mysql_error());
	$row_Fee = mysql_fetch_assoc($Fee);
	$totalRows_Fee = mysql_num_rows($Fee);
	
echo 'NORESULT:'.$noresult.'<br>';
//echo 'Fee:'.$row_Fee['fee'];
	 
	$amtdue = $row_Fee['fee'];
echo 'amtdue:'.$amtdue.'<br>';
//exit;	
// add an order for the ordered test		
  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, doctor, comments, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($_POST['visitid'], "int"),
                       GetSQLValueString($colname_feeid, "int"),
                       GetSQLValueString(100, "int"),
                       GetSQLValueString(103, "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString('paylater', "text"),
                       GetSQLValueString('Resulted', "text"),
                       GetSQLValueString('Routine', "text"),
                       GetSQLValueString($_POST['doctor'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));


  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
	 
// find last order mumber
	mysql_select_db($database_swmisconn, $swmisconn);   // find the receipt number
	$query_maxid = "SELECT MAX(id) mxoid from orders WHERE medrecnum = '".$_POST['medrecnum']."' and visitid = '".$_POST['visitid']."'";  
	$maxid = mysql_query($query_maxid, $swmisconn) or die(mysql_error());
	$row_maxid = mysql_fetch_assoc($maxid);
	$totalRows_maxid = mysql_num_rows($maxid);

//  find list of comonent tests for the Feeid (ordered test)
	mysql_select_db($database_swmisconn, $swmisconn);
  $query_tests = "SELECT t.id tid FROM tests t Where '".$colname_feeid."' in (t.feeid1, t.feeid2, t.feeid3, t.feeid4, t.feeid5, t.feeid6, t.feeid7, t.feeid8, t.feeid9, t.feeid0, t.feeidA, t.feeidB, t.feeidC, t.feeidD, t.feeidE, t.feeidF, t.feeidG, t.feeidH, t.feeidI, t.feeidJ, t.feeidK, t.feeidL, t.feeidM, t.feeidN ) and t.active = 'Y' and flag1 is null ORDER BY reportseq ";
	$addtests = mysql_query($query_tests, $swmisconn) or die(mysql_error());
	$row_addtests = mysql_fetch_assoc($addtests);	
	$totalRows_addtests = mysql_num_rows($addtests); 

 // determine result flag 
 do { 
	    $normflag = "";
	if(is_numeric($_POST['result'.$row_addtests['tid']])) {  //verify test value is numeric
		if(is_numeric($_POST['NL'.$row_addtests['tid']]) AND  $_POST['result'.$row_addtests['tid']] < $_POST['NL'.$row_addtests['tid']]) {
			$normflag = "LO";
		}	
		if(is_numeric($_POST['PL'.$row_addtests['tid']]) AND $_POST['result'.$row_addtests['tid']] < $_POST['PL'.$row_addtests['tid']]) {
			$normflag = "PL";
		}	
		if(is_numeric($_POST['NH'.$row_addtests['tid']]) AND $_POST['result'.$row_addtests['tid']] > $_POST['NH'.$row_addtests['tid']]) {
			$normflag = "HI";
		}	
		if(is_numeric($_POST['PH'.$row_addtests['tid']]) AND $_POST['result'.$row_addtests['tid']] > $_POST['PH'.$row_addtests['tid']]) {
			$normflag = "PH";
		}
      }
// insert results int results table	
  $insertSQL = sprintf("INSERT INTO results (testid, feeid, ordid, result, normflag, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['tid'.$row_addtests['tid']], "int"),
                       GetSQLValueString($_POST['feeid'], "int"),
                       GetSQLValueString($row_maxid['mxoid'], "int"),
                       GetSQLValueString($_POST['result'.$row_addtests['tid']], "text"),
                       GetSQLValueString($normflag, "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

 } while ($row_addtests = mysql_fetch_assoc($addtests)); 
}
// Go back to POCREadd.php
  $insertGoTo = "PatShow1.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];		
//    $insertGoTo .= str_replace('LabREadd.php&','LabREview.php&',$_SERVER['QUERY_STRING']); // replace function takes &notepage=PatNotesAdd.php out of $_SERVER['QUERY_STRING'];
  }
 echo $noresult;
  header(sprintf("Location: %s", $insertGoTo));

} // end if feeid
?>

<!--end of action *****************************************************************
***********************************************************************************
begin display ******************************************************************-->

<!--POCSelect sends mrn, vid, feeid, and nameof test in URL-->
<?php
 $colname_feeid = -1;
if (isset($_GET['feeid'])) {
  $colname_feeid = (get_magic_quotes_gpc()) ? $_GET['feeid'] : addslashes($_GET['feeid']);
	}
// select individual tests for the ordered test
mysql_select_db($database_swmisconn, $swmisconn);
$query_tests = "SELECT t.id tid, t.test, t.description, t.formtype, t.ddl, t.units, t.reportseq, t.active FROM tests t Where '".$colname_feeid."' in (t.feeid1, t.feeid2, t.feeid3, t.feeid4, t.feeid5, t.feeid6, t.feeid7, t.feeid8, t.feeid9, t.feeid0, t.feeidA, t.feeidB, t.feeidC, t.feeidD, t.feeidE, t.feeidF, t.feeidG, t.feeidH, t.feeidI, t.feeidJ, t.feeidK, t.feeidL, t.feeidM, t.feeidN ) and t.active = 'Y' and flag1 is null ORDER BY reportseq ";
$atests = mysql_query($query_tests, $swmisconn) or die(mysql_error());
$row_atests = mysql_fetch_assoc($atests);
$totalRows_atests = mysql_num_rows($atests);

// Select patient perm information from mrn
$query_patinfo = "SELECT p.gender, DATE_FORMAT(p.dob,'%d-%b-%Y') dob, DATE_FORMAT(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.dob)),'%y') AS age FROM patperm p  WHERE  p.medrecnum = '".$_GET['mrn']."'";
$patinfo = mysql_query($query_patinfo, $swmisconn) or die(mysql_error());
$row_patinfo = mysql_fetch_assoc($patinfo);
$totalRows_patinfo = mysql_num_rows($patinfo);

// Select orders for current visit and ordered tests
$query_orders = "SELECT o.id ordid, o.feeid, o.entrydt, o.amtpaid FROM orders o join fee f on f.id = o.feeid WHERE o.medrecnum = '".$_GET['mrn']."' and o.visitid = '".$_GET['vid']."' and o.feeid = '".$_GET['feeid']."'";
$orders = mysql_query($query_orders, $swmisconn) or die(mysql_error());
$row_orders = mysql_fetch_assoc($orders);
$totalRows_orders = mysql_num_rows($orders);

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />

<!--Define background colors for use in this program-->
<style type="text/css">
option.ltyellow {background-color:#FFFDDA}
option.red {background-color:red}
option.blue {background-color:blue}
option.white {background-color:white}
</style>

<!-- define JS script to open a popup window-->
<script language="JavaScript" type="text/JavaScript">
  function MM_openBrWindow(theURL,winName,features) { //v2.0
    var win_position = ',left=400,top=5,screenX=400,screenY=5';
    var newWindow = window.open(theURL,winName,features+win_position);
    newWindow.focus();
  }
</script>

</head>

<body>
<table width="80%">
  <tr>
  	<!--add links to Menu and Close POCT  and Display large header-->
  	<td colspan="6"><div align="center" class="BlueBold_24"><a href="PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&visit=PatVisitView.php&act=poc&pge=POCSelect.php" class="nav">Menu</a> <a href="PatShow1.php?mrn=<?php echo $_GET['mrn'] ?>&vid=<?php echo $_GET['vid'] ?>" class="nav">Close</a> Add POC Test Results</div></td>
  </tr>
  <tr>
    <td align="top"> 
	 <form name="form1" id="form1" method="POST" action="<?php echo $editFormAction; ?>">
       <table bgcolor="#BCFACC">
				<!--display orderid, test name, and Point of Care label-->
        <tr>
          <td class="BlackBold_12"><!--Ord#:<?php echo $_GET['ordid']; ?>--></td>
          <td class="BlackBold_12"><?php echo $row_atests['test']; ?></td>
          <td colspan="2"></td>
          <td nowrap="nowrap" class="Black_12"><strong><em>P</em></strong>oint<em><strong>O</strong></em>f<strong><em>C</em></strong>are</td>
          <td></td>
        </tr>
        <!-- display table header-->
        <tr>
          <td>test</td>
          <td>Result </td>
          <td>units</td>
          <td colspan="2"><div align="center">Normal</div></td>
          <td colspan="2"><div align="center">Panic</div></td>
        </tr>
        <!--Display test(s) for ordered test-->
				<?php do { ?>
        <tr>
          <td nowrap="nowrap" title="Fee ID: <?php echo $colname_feeid; ?>&#10;TestID: <?php echo $row_atests['tid']; ?>&#10;Description: <?php echo $row_atests['description']; ?>&#10;Form Type: <?php echo $row_atests['formtype']; ?>&#10Report Seq: <?php echo $row_atests['reportseq']; ?>"><?php echo $row_atests['test']; ?></td>
  <!--Select how to display data inputs if TextField or DropDown form type-->
<?php if ($row_atests['formtype'] == 'TextField') { ?>
          <td><input name="<?php echo 'result'.$row_atests['tid']; ?>" type="text" id="<?php echo 'result'.$row_atests['tid']; ?>" maxlength="30" autocomplete="off" /></td>
<?php } else if ($row_atests['formtype'] == 'DropDown') { 
		// find values for dropdown list
		mysql_select_db($database_swmisconn, $swmisconn);
		$query_ddl = "Select list, name, seq from dropdownlist where list = '".$row_atests['ddl']."' Order By seq";
		$ddl = mysql_query($query_ddl, $swmisconn) or die(mysql_error());
		$row_ddl = mysql_fetch_assoc($ddl);
		$totalRows_ddl = mysql_num_rows($ddl);
?>
<!--Dropdown list for ordered test-->
		<td><select type="text"  name="<?php echo 'result'.$row_atests['tid']; ?>" id="<?php echo 'result'.$row_atests['tid']; ?>" >
<?php if(isset($row_atests['ddl']) && $row_atests['ddl'] == 'Antibiotic') { ?>
	<option class="ltyellow" value="Not Tested">Not Tested</option>
<?php } else {?>  
	<option class="ltyellow" value="">Select</option>
<?php } ?>
  <?php do { ?>
  <option class="ltyellow" value="<?php echo $row_ddl['name']?>"><?php echo $row_ddl['name']?></option>
  <?php
} while ($row_ddl = mysql_fetch_assoc($ddl));
  $rows = mysql_num_rows($ddl);
  if($rows > 0) {
      mysql_data_seek($ddl, 0);
	  $row_ddl = mysql_fetch_assoc($ddl);
	}
?>
</select></td>
<?php } ?>
	        <!--display units for this test-->
          <td><?php echo $row_atests['units']; ?></td>
		  <input type="hidden" id="<?php echo 'tid'.$row_atests['tid']; ?>" name="<?php echo 'tid'.$row_atests['tid']; ?>" autocomplete="off" value="<?php echo $row_atests['tid']; ?>" />

	        <!--display abnormal value flags for this test-->
<?php 
	mysql_select_db($database_swmisconn, $swmisconn); // look up normal ranges
	$query_norms = "Select normlow, normhigh, paniclow, panichigh, interpretation from testnormalvalues where testid = '".$row_atests['tid']."' AND instr(gender,'".$row_patinfo['gender']."') > 0 AND '".$row_patinfo['age']."' >= agemin AND '".$row_patinfo['age']."' <= agemax"; 
	$norms = mysql_query($query_norms, $swmisconn) or die(mysql_error());
	$row_norms = mysql_fetch_assoc($norms);
	$totalRows_norms = mysql_num_rows($norms);
?>		  
          <td colspan="2" nowrap="nowrap" bgcolor="#80ff80"><?php echo $row_norms['normlow']; ?>-<?php echo $row_norms['normhigh']; ?></td>
          <td nowrap="nowrap" bgcolor="#ffcccccc">&lt;<?php echo $row_norms['paniclow']; ?></td>
          <td nowrap="nowrap" bgcolor="#ffcccccc">&gt;<?php echo $row_norms['panichigh']; ?></td>
		  <td><div align="center">
			   <input type="hidden" id="<?php echo 'PL'.$row_atests['tid']; ?>" name="<?php echo 'PL'.$row_atests['tid']; ?>"value="<?php echo $row_norms['paniclow']; ?>" />
			   <input type="hidden" id="<?php echo 'NL'.$row_atests['tid']; ?>" name="<?php echo 'NL'.$row_atests['tid']; ?>"value="<?php echo $row_norms['normlow']; ?>" />
			   <input type="hidden" id="<?php echo 'NH'.$row_atests['tid']; ?>" name="<?php echo 'NH'.$row_atests['tid']; ?>"value="<?php echo $row_norms['normhigh']; ?>" />
			   <input type="hidden" id="<?php echo 'PH'.$row_atests['tid']; ?>" name="<?php echo 'PH'.$row_atests['tid']; ?>"value="<?php echo $row_norms['panichigh']; ?>" />
					<!--Define Hidden Values-->
			   <input name="doctor" type="hidden" value="NA" />
			   <input name="medrecnum" type="hidden" value="<?php echo $_GET['mrn'] ?>" />
			   <input name="visitid" type="hidden" value="<?php echo $_GET['vid'] ?>" />
			   <input name="feeid" type="hidden" value="<?php echo $colname_feeid; ?>" />
			   <input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
			   <input name="entrydt" type="hidden" id="entrydt" value="<?php echo Date('Y-m-d H:i:s'); ?>" />
			   <input type="hidden" name="MM_insert" value="form1">
		    </div></td>
        </tr>
        <?php
			$ordcomments = "";

		 	} while ($row_atests = mysql_fetch_assoc($atests)); ?>
				<tr>
					<!-- Add comments to order-->
          <td>Order<br />
            Comments:</td>
          <td colspan="5"><textarea name="comments" cols="50" rows="2"><?php echo $ordcomments ?></textarea></td>
					<!-- Add SAVE button-->
          <td><input name="submit" type="submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="SAVE" />																				 					</td>
				</tr>
			</table>
      </form>
		</td>

<!--Display previous results for this visit if test is dipstick 11-->   
<?php if($row_orders['feeid'] == 466){ ?>
	<!--Loop to display previous orders-->
  <?php do {?>
<?php
    // query results of tests
 		$query_result = "SELECT r.id rid, r.result, r.normflag, r.entrydt, r.ordid, t.test FROM results r join tests t on r.testid = t.id WHERE r.ordid = '".$row_orders['ordid']."' and '".$row_orders['feeid']."' = 466";
		$result = mysql_query($query_result, $swmisconn) or die(mysql_error());
		$row_result = mysql_fetch_assoc($result);
		$totalRows_result = mysql_num_rows($result);
?>   
    <td valign="top">
    	<table  bgcolor="#faebd7">
      	<tr>
 	<!--//date format requires creating a variable--> 
  <?php $date=date_create($row_result['entrydt'])  ?>
				<!--Display header for results-->
        <td title="Order ID: <?php echo $row_orders['ordid'];?>&#10;Fee ID: <?php echo $row_orders['feeid'];?>&#10;GetFee ID: <?php echo $_GET['feeid'];?>"colspan="3" align="center" nowrap class="BlueBold_14">Previous Results<br />for this visit<br /><span class="BlackBold_14"> <?php echo date_format($date, 'D M d, Y'); ?></span><br /><span class="Black_11"><?php echo date_format($date, 'h:i a'); ?></span></td>
	      </tr>
<!--Loop to display resulted Dipstick tests-->        
<?php do { ?>
      	<tr>
<!--Add time limit to Edit Result-->
<?php if(strtotime(Date('Y-m-d H:i:s')) - strtotime($row_orders['entrydt']) < 3600  && ($row_orders['amtpaid'] == 0 OR is_null($row_orders['amtpaid']) )) { ?>
					<!--Link to Edit result-->
          <td bgcolor="FFFDDA"><a href="javascript:void(0)" onclick="MM_openBrWindow('POCREEdit.php?rid=<?php echo $row_result['rid'];?>&test=<?php echo $row_result['test'];?>','StatusView','scrollbars=yes,resizable=yes,width=850,height=350')">Edit</a></td>        
<?php } else { ?>
					<!--Message (tooltip) Editing is timed out-->
					<td bgcolor="#FFFDDA" title="Editing can only be done within&#10;one hour of data entry if &#10;order is not already paid."> * </td>
<?php } ?>
					<!-- Display test, result, and flag-->
          <td nowrap><?php echo $row_result['test']; ?></td>
          <td bg bgcolor="#FFFFFF" nowrap><?php echo $row_result['result']; ?></td>
          <td nowrap><?php echo $row_result['normflag']; ?></td>   
        </tr>
<?php } while ($row_result = mysql_fetch_assoc($result));?> 
				<tr>
        	<td colspan="3" align="center" title="Deletion can only be done within&#10;one hour of data entry if &#10;order is not already paid.">------------------*</td>
        </tr>
<!--Add time limit to Delete Result-->
<?php if(strtotime(Date('Y-m-d H:i:s')) - strtotime($row_orders['entrydt']) < 3600  && ($row_orders['amtpaid'] == 0 OR is_null($row_orders['amtpaid']) )) { ?>
				<tr>
					<!--Link to Delete result-->
        	<td colspan="3" bgcolor="#FFCCFF"><a href="javascript:void(0)" onclick="MM_openBrWindow('POCREdelete.php?ordid=<?php echo $row_orders['ordid'];?>','StatusView','scrollbars=yes,resizable=yes,width=850,height=350')">Delete Order and Tests</a></td>
        </tr>
<?php }	?>
		</table>
    </td>       
<?php } while ($row_orders = mysql_fetch_assoc($orders));?> 
      </table>
    </td>  


<?php } else { ?>
<!--Display previous results for this visit if test is NOT dipstick 11-->   
    <td valign="top">
    	<table  bgcolor="#faebd7">
        <tr>
          <td nowrap align="center" class="BlueBold_14">Previous Results<br />for this visit</td>
	      </tr>      
  <?php do {?>
      	<tr>

<?php  //query to get test result by order number in result table
 		$query_result = "SELECT r.id rid, r.result, r.normflag, r.entrydt, r.ordid, t.test FROM results r join tests t on r.testid = t.id WHERE r.ordid = '".$row_orders['ordid']."'";
		$result = mysql_query($query_result, $swmisconn) or die(mysql_error());
		$row_result = mysql_fetch_assoc($result);
		$totalRows_result = mysql_num_rows($result);
?>  
 	<!--//date format requires creating a variable--> 
  <?php $date=date_create($row_result['entrydt'])?>
<!--Loop to display previous orders -->  
<?php do { ?>
      	<tr>
  				<td><span class="BlackBold_14"> <?php echo date_format($date, 'D M d, Y'); ?></span><br /><span class="Black_11"><?php echo date_format($date, 'h:i a'); ?></span></td>

<!--Add time limit to Edit Result-->
<?php if(strtotime(Date('Y-m-d H:i:s')) - strtotime($row_orders['entrydt']) < 3600  && ($row_orders['amtpaid'] == 0 OR is_null($row_orders['amtpaid']) )) { ?>
 				<!--Link to Edit result-->
        <td bgcolor="FFFDDA"><a href="javascript:void(0)" onclick="MM_openBrWindow('POCREEdit.php?rid=<?php echo $row_result['rid'];?>&test=<?php echo $row_result['test'];?>','StatusView','scrollbars=yes,resizable=yes,width=850,height=350')">Edit</a></td>        
<?php } else { ?>
					<!--Message (tooltip) Editing is timed out-->
					<td bgcolor="#FFFDDA" title="Deletion can only be done within&#10;one hour of data entry if &#10;order is not already paid."> * </td>
<?php } ?>
          <td  title="Order ID: <?php echo $row_orders['ordid'];?>&#10;Fee ID: <?php echo $row_orders['feeid'];?>&#10;GetFee ID: <?php echo $_GET['feeid'];?>"> <strong><?php echo $row_result['test'];?></strong></td>
       		<td bgcolor="#FFFFFF"><?php echo $row_result['result'];?> </td>
          <td nowrap><?php echo $row_result['normflag']; ?></td>   

<!--Add time limit to Edit Result-->
<?php if(strtotime(Date('Y-m-d H:i:s')) - strtotime($row_orders['entrydt']) < 3600  && ($row_orders['amtpaid'] == 0 OR is_null($row_orders['amtpaid']) )) { ?>

					<!--Link to Delete result-->
          <td nowrap bgcolor="#FFCCFF"><a href="javascript:void(0)" onclick="MM_openBrWindow('POCREdelete.php?ordid=<?php echo $row_orders['ordid'];?>','StatusView','scrollbars=yes,resizable=yes,width=850,height=350')">Del</a></td>   
<?php } else { ?>
					<!--Message (tooltip) Deleting is timed out-->
					<td bgcolor="#FFCCFF" title="Deletion can only be done within&#10;one hour of data entry if &#10;order is not already paid."> * </td>
<?php } ?>
        </tr>
<?php } while ($row_result = mysql_fetch_assoc($result));?> 
       
<?php } while ($row_orders = mysql_fetch_assoc($orders));?> 
      </table>

<?php } ?>
  </tr>
</table>


</body>
</html>
<?php

mysql_free_result($norms);

mysql_free_result($atests);
?>
