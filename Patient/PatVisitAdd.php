 <?php //require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>
<?php $pt = "Add Patient Visit"; ?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>
<?php $_SESSION['today'] = date("Y-m-d");  // H:i:s ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1") && !empty($_POST['radio'])) {

// find pat_type & location from fee table
mysql_select_db($database_swmisconn, $swmisconn);
$query_typ_loc = sprintf("Select id, section, name from fee where id = %s", $_POST['radio']);
$typ_loc = mysql_query($query_typ_loc, $swmisconn) or die(mysql_error());
$row_typ_loc = mysql_fetch_assoc($typ_loc);
$totalRows_typ_loc = mysql_num_rows($typ_loc);

//echo 'Feeid'.$_POST['radio'];
//echo 'Pat_Type'.$row_typ_loc['section'];
//exit;

//// set visitreason and diagnosis to 'Circumcision if left blank by user
//$visitreason = $_POST['visitreason'];
//if(empty($_POST['visitreason']) && $row_typ_loc['name'] == 'Circumcision') {
//$visitreason = "Circumcision";
//}
// 
//$diagnosis = $_POST['diagnosis'];
//if(empty($_POST['diagnosis']) && $row_typ_loc['name'] == 'Circumcision') {
//$diagnosis = "Circumcision";
//}


	  $_SESSION['MRN'] = $_POST['medrecnum'];
	  $insertSQL = sprintf("INSERT INTO patvisit (medrecnum, visitdate, status, vfeeid, pat_type, location, urgency, height, weight, visitreason, diagnosis, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
											GetSQLValueString($_POST['medrecnum'], "int"),
											GetSQLValueString($_POST['visitdate'], "date"),
                      GetSQLValueString($_POST['vstatus'], "text"),
					   					GetSQLValueString($_POST['radio'], "int"),
                      GetSQLValueString($row_typ_loc['section'], "text"),
                      GetSQLValueString($row_typ_loc['name'], "text"),
                      GetSQLValueString($_POST['urgency'], "text"),
                      GetSQLValueString($_POST['height'], "text"),
                      GetSQLValueString($_POST['weight'], "text"),
                      GetSQLValueString($_POST['visitreason'], "text"),
                      GetSQLValueString($_POST['diagnosis'], "text"),
                      GetSQLValueString($_POST['entryby'], "text"),
                      GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

$colname_NewVisitid = "-1";
if (isset($_POST['medrecnum'])) {
  $colname_NewVisitid = (get_magic_quotes_gpc()) ? $_POST['medrecnum'] : addslashes($_POST['medrecnum']);
}
mysql_select_db($database_swmisconn, $swmisconn);
$query_NewVisitid = sprintf("SELECT MAX(id) newvisitid FROM patvisit WHERE medrecnum = '%s'", $colname_NewVisitid);
$NewVisitid = mysql_query($query_NewVisitid, $swmisconn) or die(mysql_error());
$row_NewVisitid = mysql_fetch_assoc($NewVisitid);
$totalRows_NewVisitid = mysql_num_rows($NewVisitid);

if(isset($_POST['surgery']) && $_POST['surgery'] == 'on'){

	  $updateSQL = sprintf("UPDATE surgery SET visitid=%s, origvisitid=%s WHERE id=%s",
                       GetSQLValueString($row_NewVisitid['newvisitid'], "int"),
                       GetSQLValueString($_POST['surgvisitid'], "int"),
                       GetSQLValueString($_POST['surgid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

	  $updateSQL = sprintf("UPDATE anesthesia SET visitid=%s WHERE id=%s",
                       GetSQLValueString($row_NewVisitid['newvisitid'], "int"),
                       GetSQLValueString($_POST['surgid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

	}

//echo $_POST['pat_type']."--";
//echo $_POST['location']."--";
//echo $getfeeid;
//exit;
mysql_select_db($database_swmisconn, $swmisconn);
$query_Fee = sprintf("SELECT fee from fee where id = '".$_POST['radio']."'");
$Fee = mysql_query($query_Fee, $swmisconn) or die(mysql_error());
$row_Fee = mysql_fetch_assoc($Fee);
$totalRows_Fee = mysql_num_rows($Fee);

$amtdue = $row_Fee['fee']*($_POST['rate']/100); 	

  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, doctor, comments, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "int"),
					        $row_NewVisitid['newvisitid'],
					        GetSQLValueString($_POST['radio'], "int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "int"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['vordstatus'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['doctor'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

// if Antenatal:booking
	if($row_typ_loc['section'] == 'Antenatal' AND $row_typ_loc['name'] == 'AnteBooking ')  {
	   $ante = array(
		   "0" => "51",
		   "1" => "36",   
		   "2" => "37",   
		   "3" => "32",   
		   "4" => "33",   
		   "5" => "15",   
		   "6" => "466",   
	   );
    	$N = count($ante);
//    echo("You selected $N order(s): ");
		for($j=0; $j < $N; $j++) {  //loop to add orders
		
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_aFee = sprintf("SELECT fee from fee where id = '".$ante[$j]."'");
	$aFee = mysql_query($query_aFee, $swmisconn) or die(mysql_error());
	$row_aFee = mysql_fetch_assoc($aFee);
	$totalRows_aFee = mysql_num_rows($aFee);
	
	$amtdue = $row_aFee['fee']*($_POST['rate']/100); 	

	
	  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, doctor, comments, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       $row_NewVisitid['newvisitid'],
                       $ante[$j],
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['lordstatus'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['doctor'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

		 // echo($N.' - '.$ante[$j] . " ");
    } //  FOR loop
   } 
// if Antenatal:booking
	if($row_typ_loc['section'] == 'Antenatal' AND $row_typ_loc['name'] == 'AnteFollowUp ')  {
	   $ante = array(
		   "0" => "51",
		   "1" => "466",   
	   );
    	$N = count($ante);
//    echo("You selected $N order(s): ");
		for($j=0; $j < $N; $j++) {  //loop to add orders
		
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_aFee = sprintf("SELECT fee from fee where id = '".$ante[$j]."'");
	$aFee = mysql_query($query_aFee, $swmisconn) or die(mysql_error());
	$row_aFee = mysql_fetch_assoc($aFee);
	$totalRows_aFee = mysql_num_rows($aFee);
	
	$amtdue = $row_aFee['fee']*($_POST['rate']/100); 	

	
	  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, doctor, comments, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       $row_NewVisitid['newvisitid'],
                       $ante[$j],
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['lordstatus'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['doctor'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

		 // echo($N.' - '.$ante[$j] . " ");
    } //  FOR loop
   } 	
//<!--********************************************circumcision***************************************************-->	 	
//echo $row_typ_loc['section'];
//echo $row_typ_loc['name'];
//exit;
// create circumcision orders if pat_pype:location =Outpatient:circumcision - feeid 116 = surgury circumcision, drug = 30
	if($row_typ_loc['section'] == 'OutPatient' AND $row_typ_loc['name'] == 'Circumcision ')  { 

	mysql_select_db($database_swmisconn, $swmisconn);
	$query_aFee = sprintf("SELECT fee from fee where id = 116");
	$aFee = mysql_query($query_aFee, $swmisconn) or die(mysql_error());
	$row_aFee = mysql_fetch_assoc($aFee);
	$totalRows_aFee = mysql_num_rows($aFee);
	
	$amtdue = $row_aFee['fee']*($_POST['rate']/100); 	

	
	  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
					        $row_NewVisitid['newvisitid'],
                       GetSQLValueString(116, "int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['lordstatus'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
  
//drug order for circumcision
	  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, item, quant, nunits, unit, every, evperiod, fornum, forperiod, ofee, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
					        $row_NewVisitid['newvisitid'],
                       GetSQLValueString(30, "int"),
                       GetSQLValueString('Penicillin Ointment', "text"),
                       GetSQLValueString(1, "int"),
                       GetSQLValueString(350, "int"),
                       GetSQLValueString('mg', "text"),
                       GetSQLValueString(12, "int"),
                       GetSQLValueString('hours', "text"),
                       GetSQLValueString(10, "int"),
                       GetSQLValueString('days', "text"),
                       GetSQLValueString(200, "int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString(200, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString('Due', "text"),
                       GetSQLValueString('RxCosted', "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
   } 	// if circumcision

// create Delivery Advance orders if pat_pype:location =Inpatient:Labour Ward - feeid 439 = labor_delivery Delivery Advance, 
	if($row_typ_loc['section'] == 'InPatient' AND $row_typ_loc['name'] == 'Labour Ward')  { 

	mysql_select_db($database_swmisconn, $swmisconn);
	$query_aFee = sprintf("SELECT fee from fee where id = 439");
	$aFee = mysql_query($query_aFee, $swmisconn) or die(mysql_error());
	$row_aFee = mysql_fetch_assoc($aFee);
	$totalRows_aFee = mysql_num_rows($aFee);
	
	$amtdue = $row_aFee['fee']*($_POST['rate']/100); 	

	
	  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
					                   $row_NewVisitid['newvisitid'],
                       GetSQLValueString(439),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['lordstatus'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
	}

 // $insertGoTo = "PatShow1.php?mrn=".$_POST['medrecnum'];  creates error on godaddy
  $insertGoTo = "PatShow1.php";
//  if (isset($_SERVER['QUERY_STRING'])) {
//    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
//    $insertGoTo .= $_SERVER['QUERY_STRING'];
//  }
  header(sprintf("Location: %s", $insertGoTo));
  } 
?>

<?php //to display selection
mysql_select_db($database_swmisconn, $swmisconn);
$query_reason = "Select id, list, name, seq from dropdownlist where list = 'Rate Reason' Order By seq";
$reason = mysql_query($query_reason, $swmisconn) or die(mysql_error());
$row_reason = mysql_fetch_assoc($reason);
$totalRows_reason = mysql_num_rows($reason);
?>
<?php // Select the first surgery for the visit that is incomplete
mysql_select_db($database_swmisconn, $swmisconn);
$query_origsurgvisit = "SELECT s.id, s.visitid, f.name FROM surgery s join fee f on s.feeid = f.id WHERE medrecnum = '".$_SESSION['mrn']."' and visitid = '".$_SESSION['vid']."' and s.id = (SELECT MIN(s.id) FROM surgery WHERE s.visitid = '".$_SESSION['vid']."') and status != 'Completed' and status != 'Cancelled' ORDER BY s.id ASC";
$origsurgvisit = mysql_query($query_origsurgvisit, $swmisconn) or die(mysql_error());
$row_origsurgvisit = mysql_fetch_assoc($origsurgvisit);
$totalRows_origsurgvisit = mysql_num_rows($origsurgvisit);
?>

<?php //to display selections 
mysql_select_db($database_swmisconn, $swmisconn);
$query_OutPatient = "SELECT id, dept, section, name, fee FROM fee WHERE Active = 'Y' and dept = 'Records' and section = 'OutPatient' ORDER BY name ASC";
$OutPatient = mysql_query($query_OutPatient, $swmisconn) or die(mysql_error());
//$row_OutPatient = mysql_fetch_assoc($OutPatient);
//$totalRows_OutPatient = mysql_num_rows($OutPatient);
?>
<?php
mysql_select_db($database_swmisconn, $swmisconn);
$query_InPatient = "SELECT id, dept, `section`, name, fee FROM fee WHERE Active = 'Y' and dept = 'Records' and section = 'InPatient' ORDER BY name ASC";
$InPatient = mysql_query($query_InPatient, $swmisconn) or die(mysql_error());
//$row_InPatient = mysql_fetch_assoc($InPatient);
//$totalRows_InPatient = mysql_num_rows($InPatient);
?>
<?php
mysql_select_db($database_swmisconn, $swmisconn);
$query_Antenatal = "SELECT id, dept, `section`, name, fee FROM fee WHERE Active = 'Y' and dept = 'Records' and section = 'Antenatal' ORDER BY name ASC";
$Antenatal = mysql_query($query_Antenatal, $swmisconn) or die(mysql_error());
//$row_Antenatal = mysql_fetch_assoc($Antenatal);
//$totalRows_Antenatal = mysql_num_rows($Antenatal);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table align="center">
	<form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
 <!-- TOP Line -->
	  <tr>
			<td>
				<table align="center">
					<tr>
						<td align="right" class="subtitlebl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status:&nbsp;</td>
						<td align="left" class="subtitlebl">
						  <select name="vstatus">
					  	  <option value="HERE">HERE</option>
						    <!--<option value="Scheduled">Scheduled</option>-->
				      </select>
				    </td>
						<td><input name="visitdate" type="text" size="18" maxlength="20" value="<?php echo date("Y-m-d H:i"); ?>"/></td>
						<td align="center" class="subtitlebl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="center" nowrap="nowrap" class="subtitlebl">Add Patient Visit</td>
				    <td align="center" class="subtitlebl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				    <td align="center" class="subtitlebl">&nbsp;</td>
			  <?php  if ($_SESSION['vnum'] > 0) { ?>
						<td nowrap="nowrap">Visits: &nbsp; </td>
					<?php if(allow(20,1) == 1) { ?>
				  	<td align="center"><a href="PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&visit=PatVisitList.php"><?php echo $_SESSION['vnum']; ?></a></td>
					<?php  } ?>
				<?php  } 
						else {?>
						<td nowrap="nowrap">Visits: &nbsp; 0 &nbsp;</td>
				<?php  } ?>
						<td align="center" class="subtitlebl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				    <td align="center" class="subtitlebl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
		  	</table>
			</td>
		</tr>
	<!--Type & location selection table-->
		<tr>
			<td>
<!--  Begin radio select -->	
				<table width ="600px" border="1" align="center" class="tablebc">
				  <tr>
<!-- ***************** Out Pat ******************************* -->		  
   			    <td valign="top" align="left"  height="20"> <span class="BlackBold_11_11"> OutPatient Clinics: </span> <br/>
	<?php  if(isset($_SESSION['Photo']) && $_SESSION['Photo'] != 'NP'){?>  				
	    				<table> <!--The container table with $cols columns-->
				<?php  $cols=2; 		// Here we define the number of columns
	   			 do{  ?>
		 					<tr>
				<?php   
					for($i=1;$i<=$cols;$i++){	// All the rows will have $cols columns even if the records are less than $cols
							$row=mysql_fetch_array($OutPatient);
							if($row){ ?>      
							<td>
								<table>
								  <tr valign="top"> <!-- substr($row['name'], 0, strpos($substr($row['name'], ' ')))-->
									<td><input name="radio" type="radio" class="BlackBold_11_11" value="<?php echo $row['id']; ?>" /></td>
									<!--Note:  single word name must be followed by a space to appear on the location selection	-->								
										<td class="BlackBold_11_11"><?php echo substr($row['name'], 0, strpos($row['name'], ' ')) ?></td>
										<td class="BlackBold_11_11"> (<?php echo $row['fee'] ?>)</td>
										<td class="BlackBold_11_11" width="20">&nbsp;</td>	<!-- Create gap between columns -->
									</tr>
				   			</table>
		    			</td>
				<?php }
				  }?>	

		 				</tr>
					<?php
					 } while($row); ?>	
	      	</table>
	<?php  } else { ?>
 								<table>
								  <tr valign="top"> <!-- substr($row['name'], 0, strpos($substr($row['name'], ' ')))-->
										<td nowrap="nowrap" class="flagBlackonYellow">Photo Required</td>	<!-- Create gap between columns -->
									</tr>
				   			</table>
				<?php } ?>	

    		</td>		  
<!-- ***************** In Pat ******************************* -->		  
	  		<td valign="top" height="20"> <span class="BlackBold_11_11">  InPatient Wards: </span> <br/>
	     		<table> <!--The container table with $cols columns-->
			<?php   $cols=2; 		// Here we define the number of columns
						do { ?>	
					  <tr>		
				<?php	for($i=1;$i<=$cols;$i++){	// All the rows will have $cols columns even if the records are less than $cols
						$row=mysql_fetch_array($InPatient);
						if($row) {	?>      
		      		<td valign="top">
								<table>
									<tr>
										<td><input type="radio" name="radio" class="BlackBold_11_11" value="<?php echo $row['id']; ?>" /></td>
										<td class="BlackBold_11_11"><?php echo substr($row['name'], 0, strpos($row['name'], ' ')) ?></td>
										<td class="BlackBold_11_11"> (<?php echo $row['fee'] ?>)</td>						
										<td class="BlackBold_11_11" width="20">&nbsp;</td>	<!-- Create gap between columns -->
									</tr>
				   			</table>
			  			</td>
					<?php	}
//			else{
//				echo "<td class='BlackBold_11_11'>&nbsp;</td>";	//If there are no more records at the end, add a blank column
//			}
				} ?>	
 	  			  </tr>
			<?php	} while($row);  ?>			  
	    		</table>
		    </td>
		  
<!-- ***************** Antenatal ******************************* -->		  
       		<td valign="top" height="20"> <span class="BlackBold_11_11">  Antenatal Clinics: </span> <br/>
	<?php  if(isset($_SESSION['Photo']) && $_SESSION['Photo'] != 'NP'){?>  				
	          <table> <!--The container table with $cols columns-->
				<?php  $cols=1; 		// Here we define the number of columns
	  		  do{
 ?>				 		<tr>
		
				<?php	for($i=1;$i<=$cols;$i++){	// All the rows will have $cols columns even if
									// the records are less than $cols
						$row=mysql_fetch_array($Antenatal);
						if($row){ ?>      
		   					<td valign="top">
									<table>
										<tr>
											<td><input type="radio" class="BlackBold_11_11" name="radio" value="<?php echo $row['id']; ?>" /></td>
											<td class="BlackBold_11_11"><?php echo substr($row['name'], 0, strpos($row['name'], ' ')) ?></td>
											<td class="BlackBold_11_11"> (<?php echo $row['fee'] ?>)</td>
											<td class="BlackBold_11_11" width="20">&nbsp;</td>	<!-- Create gap between columns -->
										</tr>
			      			</table>
			   				</td>
				<?php }
				  }?>	

		 				</tr>
					<?php
					 } while($row); ?>	
	      	</table>
	<?php  } else { ?>
 								<table>
								  <tr valign="top"> <!-- substr($row['name'], 0, strpos($substr($row['name'], ' ')))-->
										<td nowrap="nowrap" class="flagBlackonYellow">Photo Required</td>	<!-- Create gap between columns -->
									</tr>
				   			</table>
				<?php } ?>	
	          </td>
		<!-- end of antenatal -->

		    </tr>
	    </table>	 
	  </td>
	</tr>	
<!--  end of type-location section-->	
   <tr>
    <td>
      <table width="100%" border="0" bgcolor="#BCFACC">
<?php       if(isset($row_origsurgvisit['visitid'])) {  ?>
				<tr>
        	<td colspan="8" bgcolor="#ff99aa" >Is this visit for <strong><?php echo $row_origsurgvisit['name'] ?> </strong> surgery?
            <input type="checkbox" name="surgery" checked="checked" /> <strong>Uncheck this box if this visit is not for surgery.</strong></td>
						<input type="hidden"   name="surgid" id="surgid" value="<?php echo $row_origsurgvisit['id'] ?>" />
						<input type="hidden"   name="surgvisitid" id="surgvisitid" value="<?php echo $row_origsurgvisit['visitid'] ?>" />
        </tr>
<?php } ?>
        <tr>
          <td>&nbsp;</td>   <!--<div align="right">Urgency:</div>-->
          <td>&nbsp;</td>   
						<input name="urgency" type="hidden" value="Routine" />
				  <!--select name="urgency" id="urgency">
								<option value="Routine">Routine</option>
								<option value="Scheduled">Scheduled</option>
								<option value="ASAP">ASAP</option>
								<option value="STAT">STAT</option>
						  </select>          -->
          <!--<td nowrap="nowrap">&nbsp;</td>-->
          <td nowrap="nowrap">Rate &amp;</td>
		   		<td nowrap="nowrap">Reason:
            <select name="rate" id="rate">
			  			<option value="200">200</option>
			  			<option value="150">150</option>
			  			<option value="125">125</option>
			  			<option value="100" selected="selected">Standard</option>
			  			<option value="75">75%</option>
			  			<option value="50">50%</option>
			  			<option value="25">25%</option>
			  			<option value="0">None</option>
		        </select>		</td>
					<td valign="center"><select name="ratereason">
        	  <option value="103">None</option>
        <?php do {  ?>
          <option value="<?php echo $row_reason['id']?>"><?php echo $row_reason['name']?></option>
       	<?php	} while ($row_reason = mysql_fetch_assoc($reason));
  					$rows = mysql_num_rows($reason);
  					if($rows > 0) {
      			mysql_data_seek($reason, 0);
	  				$row_reason = mysql_fetch_assoc($reason);
 						}
?>
        </select></td>
		  		<td>&nbsp;&nbsp;</td>
          <td><a href="PatShow1.php?mrn=<?php echo $_SESSION['mrn'] ?>">Close</a></td>
        </tr>
        <tr>
          <td valign="middle" nowrap="nowrap"><div align="right">*Visit<br />
            Reason </div></td>
          <td colspan="4"><textarea id="visitreason" name="visitreason" cols="35" rows="2" data-validation="required" data-validation-length="min3" data-validation-error-msg="Visit Reason Required - Min = 3 Characters ********************************" ></textarea></td>
						<td>
							<table width="100%">
								<tr>
									<td>Height</td>
					  			<td nowrap="nowrap"><input name="height" type="text" size="5" data-validation="number"  data-validation-allowing="range[1;200]" data-validation-optional="true"  data-validation-error-msg="Height: Number between 1 and 200 Required ********************************" />&nbsp;cm</td>
								</tr>
								<tr>
									<td>Weight</td>
					  			<td nowrap="nowrap"><input name="weight" type="text" size="5" data-validation="number"  data-validation-allowing="range[1;300]" data-validation-optional="true"   data-validation-error-msg="Weight: Number between 1 and 300 Required ********************************" /> &nbsp;kg</td>
								</tr>
			  			</table>
						</td>
          	<td><div align="right">Diag-<br />nosis:</div></td>
          	<td colspan="3"><textarea name="diagnosis" cols="35" rows="2" id="diagnosis" data-validation-optional="true" data-validation="length" data-validation-length="min3"></textarea></td>
          	<td nowrap="nowrap"><input type="submit" name="Submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="Add Visit" /></td>
        	</tr>
      	</table>
				<input type="hidden" name="MM_insert" value="form1" />
				<input name="billstatus" type="hidden" id="billstatus" value="Due" />
				<input name="vordstatus" type="hidden" id="vordstatus" value="Visit" />
				<input name="lordstatus" type="hidden" id="lordstatus" value="Ordered" />
				<input name="doctor" type="hidden" id="doctor" value="NA" />
				<input name="amtdue" type="hidden" id="amtdue" value="0" />
				<input name="comments" type="hidden" id="comments" value="none" />
				<input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
				<input name="entrydt" type="hidden" id="entrydt" value="<?php echo date("Y-m-d H:i:s"); ?>" />
				<input name="medrecnum" type="hidden" id="medrecnum" value="<?php echo $_SESSION['mrn']; ?>" />
    	</td>
    </tr>
  </form>
</table>
<script src="../../jquery-1.11.1.js"></script>
<script src="../../jQuery-Form-Validator-master/form-validator/jquery.form-validator.min.js"></script>
<script>
/* important to locate this script AFTER the closing form element, so form object is loaded in DOM before setup is called */
    $.validate({
	//modules : 'date, security'    //options: 'date',  'security', 'location', 'file'
	validateOnBlur : false, // disable validation when input looses focus
    errorMessagePosition : 'top', // Instead of 'element' which is default
    scrollToTopOnError : false // Set this property to true if you have a long form

 });</script>

</body>
</html>
<?php
mysql_free_result($reason);
?>
<!--     	<table>
		   <tr>
             <td valign="top" class="subtitlebk">
              <table>
                <tr>
                  <td colspan="3"><div align="center">OutPatient</div></td>
                </tr>
                <?php // do { ?>
                <tr>
                  <td ><input type="radio" name="radio" value="<?php // echo $row_OutPatient['id']; ?>" /></td>
                  <td nowrap="nowrap" class="BlackBold_11" title="<?php // echo $row_OutPatient['descr']; ?>"><?php // echo $row_OutPatient['name']; ?></td>
                  <td class="BlackBold_11"><?php // echo $row_OutPatient['fee']; ?></td>
                </tr>
                <?php // } while ($row_OutPatient = mysql_fetch_assoc($OutPatient)); ?>
              </table>
           </td>
           <td valign="top" class="subtitlebk">
              <table>
                <tr>
                  <td colspan="3"><div align="center">InPatient</div></td>
                </tr>
                <?php // do { ?>
                <tr>
                  <td><input type="radio" name="radio" value="<?php // echo $row_InPatient['id']; ?>" /></td>
                  <td class="BlackBold_11" nowrap="nowrap" title="<?php // echo $row_InPatient['descr']; ?>"><?php // echo $row_InPatient['name']; ?></td>
                  <td class="BlackBold_11"><?php // echo $row_InPatient['fee']; ?></td>
                </tr>
                <?php // } while ($row_InPatient = mysql_fetch_assoc($InPatient)); ?>
              </table>
			  
		   </td>
		   <td valign="top" class="subtitlebk">
			  <table>
				<tr>
				  <td colspan="3"><div align="center">Antenatal</div></td>
				</tr>
				<?php // do { ?>
				<tr>
				  <td><input type="radio" name="radio" value="<?php // echo $row_Antenatal['id']; ?>" /></td>
				  <td class="BlackBold_11" nowrap="nowrap" title="<?php // echo $row_Antenatal['descr']; ?>"><?php // echo $row_Antenatal['name']; ?></td>
				  <td class="BlackBold_11"><?php // echo $row_Antenatal['fee']; ?></td>
				</tr>
			<?php // } while ($row_Antenatal = mysql_fetch_assoc($Antenatal)); ?>
              </table>
			  
		     </td>
		   </tr>	
	      </table>
-->