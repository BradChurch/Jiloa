<?php    $pt = "Add Patient Visit"; ?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>

<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
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
} ?>

<?php $editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
} ?>
<!--**************************************** BEGIN VISIT UPDATE ****************************************-->

<?php if ((isset($_POST["MM_update"])) && (isset($_POST["radio"])) && ($_POST["MM_update"] == "form1")) {

		$colmrn_preupdt = "-1";
		if (isset($_POST['medrecnum'])) {
			$colmrn_preupdt = (get_magic_quotes_gpc()) ? $_POST['medrecnum'] : addslashes($_POST['medrecnum']);
			//$_SESSION['mrn'] = $colmrn_preupdt;  // set to retrieve MRN in PatShow1.php when it goes back
		}
		$colvid_preupdt = "-1";
		if (isset($_POST['vid'])) {
			$colvid_preupdt = (get_magic_quotes_gpc()) ? $_POST['vid'] : addslashes($_POST['vid']);
		}
		$colvordid_preupdt = "-1";
		if (isset($_POST['vordid'])) {
			$colvordid_preupdt = (get_magic_quotes_gpc()) ? $_POST['vordid'] : addslashes($_POST['vordid']);
		}

?>
<?php // get values of preUpdt patvisit record  
		mysql_select_db($database_swmisconn, $swmisconn);
		$query_preupdt = sprintf("SELECT v.id vid, medrecnum, DATE_FORMAT(visitdate,'%%Y-%%m-%%d   %%H:%%i') visitdate, vfeeid, status, pat_type, location, urgency, height, weight, DATE_FORMAT(v.discharge,'%%Y-%%m-%%d') discharge, visitreason, diagnosis, v.returndt, DATE_FORMAT(v.returndt,'%%Y-%%m-%%d') returndate, v.returnloc, v.entryby, DATE_FORMAT(v.entrydt,'%%Y-%%m-%%d') entrydt FROM patvisit v WHERE v.id = %s AND medrecnum = %s", $colvid_preupdt,$colmrn_preupdt);
		$preupdt = mysql_query($query_preupdt, $swmisconn) or die(mysql_error());
		$row_preupdt = mysql_fetch_assoc($preupdt);
		$totalRows_preupdt = mysql_num_rows($preupdt);
?>

<?php
// find pat_type & location for selected location from fee table
mysql_select_db($database_swmisconn, $swmisconn);
$query_typ_loc = sprintf("Select id feeid, fee, section, name from fee where id = %s", $_POST['radio']);  // radio = fee.id for selected location in form 1
$typ_loc = mysql_query($query_typ_loc, $swmisconn) or die(mysql_error());
$row_typ_loc = mysql_fetch_assoc($typ_loc);
$totalRows_typ_loc = mysql_num_rows($typ_loc);

// record admissions in ipadmit table ************************************************************
		$origlocation = $row_preupdt['location'];
		$origpat_type = $row_preupdt['pat_type'];
		$newlocation = $row_typ_loc['name'];
		$newpat_type = $row_typ_loc['section'];
		$amtdue = $row_typ_loc['fee']*($_POST['rate']/100);
?>
   <!--******************************** Add a record to ipadmit table - no app to view these yet ****************************	-->	 
<?php		if($origpat_type != "InPatient" && $newpat_type == "InPatient") {
			
		// insert a record into ipadmit
				 $insertSQL = sprintf("INSERT INTO ipadmit (visitid, admitfrom, admitto, problist, provdiag, admitby, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
													 GetSQLValueString($_POST['vid'], "int"),
													 GetSQLValueString($row_preupdt['location'], "text"),         
													 GetSQLValueString($row_typ_loc['name'], "text"),
													 GetSQLValueString($row_preupdt['visitreason'], "text"),
													 GetSQLValueString($_POST['diagnosis'], "text"),
													 GetSQLValueString($_POST['entryby'], "text"),
													 GetSQLValueString($_POST['entryby'], "text"),
													 GetSQLValueString($_POST['entrydt'], "date"));
								 
			mysql_select_db($database_swmisconn, $swmisconn);
			$Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
		}
?>
<!--********************************* UPDATE PATVISIT ***************************************************-->

<!--RULE 1 - dont allow Type/Location or discharge change if discharged-->
<?php  if(!empty($_POST['dischgd']) ){    
 
	  $updateSQL = sprintf("UPDATE patvisit SET urgency=%s, height=%s, weight=%s, visitreason=%s, diagnosis=%s, returndt=%s, returnloc=%s, entryby=%s, entrydt=%s WHERE id=%s",
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['height'], "text"),
                       GetSQLValueString($_POST['weight'], "text"),
                       GetSQLValueString($_POST['visitreason'], "text"),
                       GetSQLValueString($_POST['diagnosis'], "text"),
                       GetSQLValueString($_POST['returndt'], "date"),
                       GetSQLValueString($_POST['returnloc'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['vid'], "int"));
	
    } else {  //if not discharged, location can be changed
    
	  $updateSQL = sprintf("UPDATE patvisit SET visitdate=%s, status=%s, vfeeid = %s, pat_type=%s, location=%s, urgency=%s, height=%s, weight=%s, discharge=%s, visitreason=%s, diagnosis=%s, returndt=%s, returnloc=%s, entryby=%s, entrydt=%s WHERE id=%s",
                       GetSQLValueString($_POST['visitdate'], "date"),
                       GetSQLValueString($_POST['vstatus'], "text"),
                       GetSQLValueString($row_typ_loc['feeid'], "text"),
                       GetSQLValueString($row_typ_loc['section'], "text"),
                       GetSQLValueString($row_typ_loc['name'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['height'], "text"),
                       GetSQLValueString($_POST['weight'], "text"),
                       GetSQLValueString($_POST['discharge'], "date"),
                       GetSQLValueString($_POST['visitreason'], "text"),
                       GetSQLValueString($_POST['diagnosis'], "text"),
                       GetSQLValueString($_POST['returndt'], "date"),
                       GetSQLValueString($_POST['returnloc'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['vid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());
 }  
 ?>
 <!--RULE 2 - if the visit fee is already paid, and a new location is selected, add a new visit order.-->
<?php  
			if(!empty($_POST['amtpaid'] ) and $_POST['amtpaid'] > 0 and $row_typ_loc['name'] != $_POST['location']){    //add a new one
				$insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, doctor, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "int"),
					       			 GetSQLValueString($_POST['vid'], "int"),
					        		 GetSQLValueString($_POST['radio'], "int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "int"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString('Due', "text"),
                       GetSQLValueString('Visit', "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString('NA', "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));
					   
					mysql_select_db($database_swmisconn, $swmisconn);
					$Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

         } else { // if not already paid, change type/location

            $updateSQL = sprintf("UPDATE orders SET feeid=%s, amtdue=%s, entryby=%s, entrydt=%s WHERE id=%s",
                       GetSQLValueString($row_typ_loc['feeid'], "int"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['ordid'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());
       }  
?> 
<!--RULE 3 - inpatient is transferred so bed assignment must be deleted-->
  <?php //  echo  $origpat_type.'-'.$newpat_type.'--'.$origlocation.'---'.$newlocation;   exit;?>

  <?php if($origpat_type == "InPatient" && $newpat_type == "InPatient" and $origlocation != $newlocation) {
	
    $updateSQL = sprintf("UPDATE patbed SET medrecnum = %s WHERE medrecnum = %s",
                       GetSQLValueString(Null, "int"),
                       GetSQLValueString($row_preupdt['medrecnum'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());
  } ?> 

<!-- ******************************************************************************************************************************-->
<!--RULE 5 -if vist location changes from AnteBooking to a different location, delete lab orders-->
<?php 
 		if(trim($origlocation) == "AnteBooking" && $newpat_type != 'InPatient' && trim($newlocation) != "AnteBooking" && $_POST['amtpaid'] == 0){  
  //echo $origlocation .' - '. $newlocation.' -- ' .$row_preupdt['medrecnum'].' -- ' .$row_preupdt['vid']; exit;
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
         $deleteOrdSQL = "Delete FROM orders WHERE medrecnum = ".$row_preupdt['medrecnum']." and visitid = ".$row_preupdt['vid']." and feeid = ".$ante[$j];
       mysql_select_db($database_swmisconn, $swmisconn);
       $Result1 = mysql_query($deleteOrdSQL, $swmisconn) or die(mysql_error());		
    } //  FOR loop
   } 	// if AnteBooking
?>
<!-- ******************************************************************************************************************************-->
<!--RULE 5A -if vist location changes from AnteBooking to a different location, delete lab orders-->
<?php 
 		if(trim($origlocation) == "AnteFollowUp" && $newpat_type != 'InPatient' && trim($newlocation) != "AnteFollowup" && $_POST['amtpaid'] == 0){  
  //echo $origlocation .' - '. $newlocation.' -- ' .$row_preupdt['medrecnum'].' -- ' .$row_preupdt['vid']; exit;
	   $ante = array(
		   "0" => "51",
		   "1" => "466",   
	   );
    	$N = count($ante);
//    echo("You selected $N order(s): ");
		for($j=0; $j < $N; $j++) {  //loop to add orders
		
       mysql_select_db($database_swmisconn, $swmisconn);
         $deleteOrdSQL = "Delete FROM orders WHERE medrecnum = ".$row_preupdt['medrecnum']." and visitid = ".$row_preupdt['vid']." and feeid = ".$ante[$j];
       mysql_select_db($database_swmisconn, $swmisconn);
       $Result1 = mysql_query($deleteOrdSQL, $swmisconn) or die(mysql_error());		
    } //  FOR loop
   } 	// if AnteBooking
?>


<!-- ******************************************************************************************************************************-->
<!--RULE 4 Order antenatal lab tests if location = AnteBooking and not previously ordered (get count from booking query)-->

  <?php 	if($row_typ_loc['section'] == 'Antenatal' AND $row_typ_loc['name'] == 'AnteBooking ' and $_POST['booking']== 0)  {  // 
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
	
	$insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($_POST['vid'], "int"),
                       $ante[$j],
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['ordstatus'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
    } //  FOR loop
   } 	// if antenatal

?>
<!-- ******************************************************************************************************************************-->
<!--RULE 4A Order antenatal lab tests if location = AnteBooking and not previously ordered (get count from booking query)-->

  <?php 	if($row_typ_loc['section'] == 'Antenatal' AND $row_typ_loc['name'] == 'AnteFollowUp ' and $_POST['followup']== 0)  {  // 
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
	
	$insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($_POST['vid'], "int"),
                       $ante[$j],
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['ordstatus'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
    } //  FOR loop
   } 	// if antenatal

?>

<<!-- ******************************************************************************************************************************-->
<!--  RULE 6 - if location is Circumcision, add surgery order and lotion from Pharmacy -->
<!-- DO WE NEED TO CREATE A SURGERY RECORD ???  NO-->
<?php 		if(trim($origlocation) != "Circumcision" && trim($newlocation) == "Circumcision"){  // location Circumcision has a space after it in fee table setup
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_aFee = sprintf("SELECT fee from fee where id = 116");
	$aFee = mysql_query($query_aFee, $swmisconn) or die(mysql_error());
	$row_aFee = mysql_fetch_assoc($aFee);
	$totalRows_aFee = mysql_num_rows($aFee);
	
	$amtdue = $row_aFee['fee']*($_POST['rate']/100); 	
	
	  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
					             GetSQLValueString($_POST['vid'], "int"),
                       GetSQLValueString(116, "int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
  
//drug order for circumcision
	  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, item, quant, nunits, unit, every, evperiod, fornum, forperiod, ofee, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
					             GetSQLValueString($_POST['vid'], "int"),
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

?>
<!-- ******************************************************************************************************************************-->
<!--RULE 7 - if location changes from Circumcision to a different location, delete surgery order and lotion from Pharmacy  -->
<!--  WHAT IF IT IS ALREADY PAID????-->

<?php 
    //echo $origlocation .' - '. $newlocation.' -- ' .$row_preupdt['medrecnum'].' -- ' .$row_preupdt['vid']; exit;
		if(trim($origlocation) == "Circumcision" && trim($newlocation) != "Circumcision" && $_POST['amtpaid'] == 0){  
		// location Circumcision has a space after it in fee table setup
       mysql_select_db($database_swmisconn, $swmisconn);
         $deleteOrdSQL = "Delete FROM orders WHERE medrecnum = ".$row_preupdt['medrecnum']." and visitid = ".$row_preupdt['vid']." and (feeid = 116 || item = 'Penicillin Ointment')";
       mysql_select_db($database_swmisconn, $swmisconn);
       $Result1 = mysql_query($deleteOrdSQL, $swmisconn) or die(mysql_error());		
			}
?>
<!--Add query for previous charge of 439 for this visit-->
<?php 
		mysql_select_db($database_swmisconn, $swmisconn);
		$query_PrevDelivAdv = "Select feeid from orders where visitid = '".$_POST['vid']."' and feeid = 439";
		$PrevDelivAdv = mysql_query($query_PrevDelivAdv, $swmisconn) or die(mysql_error());
		$row_PrevDelivAdv = mysql_fetch_assoc($PrevDelivAdv);
		$totalRows_PrevDelivAdv = mysql_num_rows($PrevDelivAdv);

?>
<?php 
// RULE: create Delivery Advance order (feeid 439 = labor_delivery Delivery Advance) if new pat_pype:location =Inpatient:Labour Ward  and patient has not previously been charged 
	if($row_typ_loc['section'] == 'InPatient' AND $row_typ_loc['name'] == 'Labour Ward' and $totalRows_PrevDelivAdv == 0)  { 
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_aFee = sprintf("SELECT fee from fee where id = 439");
	$aFee = mysql_query($query_aFee, $swmisconn) or die(mysql_error());
	$row_aFee = mysql_fetch_assoc($aFee);
	$totalRows_aFee = mysql_num_rows($aFee);
	
	$amtdue = $row_aFee['fee']*($_POST['rate']/100); 	

	
	  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($colvid_preupdt, "int"),
                       GetSQLValueString(439,"int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($_POST['billstatus'], "text"),
                       GetSQLValueString($_POST['ordstatus'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
	}
?>
<?php //find related surgery - current visit id = $row_preupdt['vid'];
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_Surgery = "SELECT id, origvisitid FROM surgery WHERE visitid = '".$row_preupdt['vid']."'";
	$Surgery = mysql_query($query_Surgery, $swmisconn) or die(mysql_error());
	$row_Surgery = mysql_fetch_assoc($Surgery);
	$totalRows_Surgery = mysql_num_rows($Surgery);
?>

<?php
  // update visitid of surgery record to current visit id if surgery1 is checked
if(isset($_POST['surgery1']) && $_POST['surgery1'] == 'on'){

	  $updateSQL = sprintf("UPDATE surgery SET visitid=%s, origvisitid=%s WHERE id=%s",
                       GetSQLValueString($_POST['vid'], "int"),
                       GetSQLValueString($_POST['surgvisitid'], "int"),
                       GetSQLValueString($_POST['surgid'], "int"));

			mysql_select_db($database_swmisconn, $swmisconn);
			$Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

	  $updateSQL = sprintf("UPDATE anesthesia SET visitid=%s WHERE id=%s",
                       GetSQLValueString($_POST['vid'], "int"),
                       GetSQLValueString($_POST['surgid'], "int"));

			mysql_select_db($database_swmisconn, $swmisconn);
			$Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());
	}

 //save orig visitid as current visitid and set orig visit id to 'NULL'
//echo 'ckbox'.$_POST['surgery2'].'br';
//echo 'orig '.$row_Surgery['origvisitid'].'br';
//echo 'curr  '.$row_Surgery['id'].'br';
//exit;
// update visitid of surgery record to previous visitid if surgery2 is checked
if(isset($_POST['surgery2']) && $_POST['surgery2'] == 'on'){

		  $updateSQL = sprintf("UPDATE surgery SET visitid=%s, origvisitid=%s WHERE id=%s",
                       GetSQLValueString($row_Surgery['origvisitid'], "int"),
                       GetSQLValueString(NULL, "int"),
                       GetSQLValueString($row_Surgery['id'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

		  $updateSQL = sprintf("UPDATE anesthesia SET visitid=%s WHERE id=%s",
                       GetSQLValueString($row_Surgery['origvisitid'], "int"),
                       GetSQLValueString($row_Surgery['id'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());
	}



?>

<!--  Update complete -- Display patient again-->
<?php   $updateGoTo = "PatShow1.php?mrn=".$_SESSION['mrn']."& vid=".$_SESSION['vid']."&disp=on";

//echo $row_typ_loc['name'].'<br>'; 
//echo $_POST['booking'].'<br>';
//echo $_POST['followup'].'<br>';
//echo trim($origlocation.'<br>');
//echo trim($newlocation).'<br>';
//exit;

  header(sprintf("Location: %s", $updateGoTo));
  } 

?>
<!-- ******************************************************************************************************************************-->
<!--**************************************  begin display queries ************************************-->
<?php   // get current visit data
$colid_visitedit = "-1";
if (isset($_GET['vid'])) {
  $colid_visitedit = (get_magic_quotes_gpc()) ? $_GET['vid'] : addslashes($_GET['vid']);
  $_SESSION['vid'] = $colid_visitedit;
}
$colmrn_visitedit = "-1";
if (isset($_GET['mrn'])) {
  $colmrn_visitedit = (get_magic_quotes_gpc()) ? $_GET['mrn'] : addslashes($_GET['mrn']);
  $_SESSION['mrn'] = $colmrn_visitedit;  // set to retrieve MRN in PatShow1.php when it goes back
}
$colvordid_visitedit = "-1";
if (isset($_GET['vordid'])) {
  $colvordid_visitedit = (get_magic_quotes_gpc()) ? $_GET['vordid'] : addslashes($_GET['vordid']);
}

// get values of current patvisit record  
mysql_select_db($database_swmisconn, $swmisconn);
$query_visitedit = sprintf("SELECT v.id, v.medrecnum, DATE_FORMAT(visitdate,'%%Y-%%m-%%d   %%H:%%i') visitdate, vfeeid, v.status, v.pat_type, v.location, v.urgency, v.height, v.weight, DATE_FORMAT(v.discharge,'%%Y-%%m-%%d') discharge, v.visitreason, v.diagnosis, v.returndt, DATE_FORMAT(v.returndt,'%%Y-%%m-%%d') returndate, v.returnloc, v.entryby, DATE_FORMAT(v.entrydt,'%%Y-%%m-%%d') entrydt, o.id ordid, o.feeid, o.amtpaid, o.rate, o.ratereason, o.status FROM patvisit v JOIN orders o on v.id = o.visitid WHERE v.id = %s AND v.medrecnum = %s AND o.id = %s", $colid_visitedit, $colmrn_visitedit, $colvordid_visitedit);
$visitedit = mysql_query($query_visitedit, $swmisconn) or die(mysql_error());
$row_visitedit = mysql_fetch_assoc($visitedit);
$totalRows_visitedit = mysql_num_rows($visitedit);
?>
<?php 	$regpaid = 'N';
    if ($row_visitedit['rate'] == 0 or $row_visitedit['amtpaid'] > 0) {
	$regpaid = 'Y';
}
?>
<?php // Select the first surgery for the visit that is not the current visit that surgery is incomplete
mysql_select_db($database_swmisconn, $swmisconn);
$query_surgordervisit = "SELECT s.id, s.visitid, f.name FROM surgery s join fee f on s.feeid = f.id WHERE medrecnum = '".$_SESSION['mrn']."' and visitid != '".$_SESSION['vid']."' and s.id = (SELECT MIN(s.id) FROM surgery WHERE s.visitid != '".$_SESSION['vid']."') and status != 'Completed' and status != 'Cancelled'  ORDER BY s.id ASC";
$surgordervisit = mysql_query($query_surgordervisit, $swmisconn) or die(mysql_error());
$row_surgordervisit = mysql_fetch_assoc($surgordervisit);
$totalRows_surgordervisit = mysql_num_rows($surgordervisit);
?>

<?php // Select the surgery for the visit that was updated to a new visit. origvisitid is surgery field updated when visitid is updated in PatVisitAdd.php
mysql_select_db($database_swmisconn, $swmisconn);
$query_origsurgvisit = "SELECT s.id, s.origvisitid, f.name FROM surgery s join fee f on s.feeid = f.id WHERE medrecnum = '".$_SESSION['mrn']."' and visitid = '".$_SESSION['vid']."' and status != 'Completed' and status != 'Cancelled' ORDER BY s.id ASC";
$origsurgvisit = mysql_query($query_origsurgvisit, $swmisconn) or die(mysql_error());
$row_origsurgvisit = mysql_fetch_assoc($origsurgvisit);
$totalRows_origsurgvisit = mysql_num_rows($origsurgvisit);
?>

<?php //to display selections
mysql_select_db($database_swmisconn, $swmisconn);
$query_OutPatient = "SELECT id, dept, `section`, name, fee FROM fee WHERE Active = 'Y' and dept = 'Records' and Section = 'OutPatient' ORDER BY name ASC";
$OutPatient = mysql_query($query_OutPatient, $swmisconn) or die(mysql_error());
?>
<?php
mysql_select_db($database_swmisconn, $swmisconn);
$query_InPatient = "SELECT id, dept, `section`, name, fee FROM fee WHERE Active = 'Y' and dept = 'Records' and Section = 'InPatient' ORDER BY name ASC";
$InPatient = mysql_query($query_InPatient, $swmisconn) or die(mysql_error());
?>
<?php
mysql_select_db($database_swmisconn, $swmisconn);
$query_Antenatal = "SELECT id, dept, `section`, name, fee FROM fee WHERE Active = 'Y' and dept = 'Records' and Section = 'Antenatal' ORDER BY name ASC";
$Antenatal = mysql_query($query_Antenatal, $swmisconn) or die(mysql_error());
?>
<?php //for return location dd/
mysql_select_db($database_swmisconn, $swmisconn);
$query_locations = "SELECT name, fee FROM fee WHERE Active = 'Y' and dept = 'Records' and section != 'Registration' ORDER BY name ASC";
$locations = mysql_query($query_locations, $swmisconn) or die(mysql_error());
$row_locations = mysql_fetch_assoc($locations);
?>

<?php 
mysql_select_db($database_swmisconn, $swmisconn);
$query_booking = "SELECT o.id ordid FROM orders o join PATVISIT v ON o.visitid = v.id where o.visitid = '".$colid_visitedit."' and v.location = 'AnteBooking' and (o.feeid = 32 or o.feeid = 33)";
$booking = mysql_query($query_booking, $swmisconn) or die(mysql_error());
$row_booking = mysql_fetch_assoc($booking);
$totalRows_booking = mysql_num_rows($booking);
?>
<?php 
mysql_select_db($database_swmisconn, $swmisconn);
$query_followup = "SELECT o.id ordid FROM orders o join PATVISIT v ON o.visitid = v.id where o.visitid = '".$colid_visitedit."' and v.location = 'AnteFollowUp' and (o.feeid = 51 or o.feeid = 466)";
$followup = mysql_query($query_followup, $swmisconn) or die(mysql_error());
$row_followup = mysql_fetch_assoc($followup);
$totalRows_followup = mysql_num_rows($followup);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
<title>Visit Edit</title>
</head>

<body>
<table align="center">
  <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
	 	<tr>
			<td>
				<table align="center">
  				<tr>                                   <?php //echo $_SESSION['ordid'] . ' - ' . echo $_SESSION['getfeedid'] ?>
				  	<td height="24" align="right" class="subtitlebl">Status:&nbsp;<?php //echo $row_visitedit['ordid']; ?><?php //echo $row_visitedit['feeid']; ?></td>
				  	<td align="left" class="subtitlebl">
					  <select name="vstatus">
                        <option value="HERE">HERE</option>
                        <!--<option value="Scheduled">Scheduled</option>-->
                      </select>				  </td>
						<td align="center" class="subtitlebl"><input name="visitdate" type="text" id="visitdate" value="<?php echo $row_visitedit['visitdate']; ?>" size="20"  data-validation="date"   data-validation-error-msg="Visit Date in format of YYYY-MM-DD (10 characters long) is required ******"/></td>
						<td align="center" class="subtitlebl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="center" class="subtitlebl">Edit Patient Visit</td>
						<td align="center" class="subtitlebl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<?php  if ($_SESSION['vnum'] > 0) { ?>
						<td nowrap="nowrap">Visits: &nbsp; </td>
					<?php if(allow(20,1) == 1) { ?>
				    <td align="center"><a href="PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&visit=PatVisitList.php"&disp=on><?php echo $_SESSION['vnum']; ?></a></td>
					<?php  } ?>
				<?php  } else { ?>
					<td nowrap="nowrap">Visits: &nbsp; 0 &nbsp;</td>
				<?php  } ?>
						<td align="center" class="subtitlebl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<?php if(allow(20,4) == 1 and empty($row_visitedit['discharge']) and $regpaid == 'N') { ?>
						<td align="center"> <a href="PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&visit=PatVisitDelete.php&vid=<?php echo $row_visitedit['id'] ?>&disp=on">Delete</a></td>
				<?php  } ?>
				  	<td align="center" class="subtitlebl">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
				<?php if(allow(20,1) == 1) { ?>
						<td align="center"> <a href="PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&visit=PatVisitView.php&vid=<?php echo $row_visitedit['id'] ?>&disp=on">View</a></td>
				<?php  } ?>
					</tr>
				</table>
			</td>
	  </tr>
<!-- next section -->
				<?php if(!empty($row_visitedit['discharge']) ){    //dont allow Type/Location change if discharged ?>
		  <input name="radio" type="hidden" id="radio" value="<?php echo $row_visitedit['feeid']; ?>" />
    <tr>
	  	<td align="center">Visit is discharged.  Visit Type, Location, Entry Date, Discharge cannot be changed. <br />
	  	  Only Visit Reason, Height, Weight, and Diagnosis can be edited. </td>
	  </tr>
				<?php } else { ?>
	  <tr>	
			<td bgcolor="#F8FDCE">
				<table border="1" align="center" class="tablebc"> <!--for pat_type & location-->
					<tr>
<!-- ***************** Out Pat ******************************* -->		  
		        <td>
							<table>	 <!--The container table with $cols columns-->
						  	<tr>
									<td align="left" class="Black_1010">OutPatient Clinics:</td>
						  	</tr>
        <?php $cols=2; 		// Here we define the number of columns
	    			do{ ?>
					      <tr>
        <?php for($i=1;$i<=$cols;$i++){	// All the rows will have $cols columns even if
									// the records are less than $cols
					$row=mysql_fetch_array($OutPatient);
					if($row){
				?>      
									<td>
										<table>
											<tr valign="top">
										     <td><input name="radio" type="radio" class="BlackBold_11_11" value="<?php echo $row['id']; ?>" <?php echo ($row_visitedit['vfeeid'] == $row['id'])?'checked':'' ?>/></td>  <!-- ?'checked':'' (? means 'if true' then, : means else)-->
									 <!--Note:  single word 'name' must be followed by a space to appear on the location selection	-->
												<td class="BlackBold_11_11"><?php echo substr($row['name'], 0, strpos($row['name'], ' ')) ?></td>
												<td class="BlackBold_11_11"> (<?php echo $row['fee'] ?>)</td>									
												<!--<td class="BlackBold_11_11" width="20">&nbsp;</td>-->	<!-- Create gap between columns -->
											</tr>
									  </table>
									</td>
					<?php	}  // if($row)
							}  // row loop?> 
							  </tr>
				<?php			} while($row); ?>	
							</table>
					  </td>		  
<!-- ***************** In Pat ******************************* -->		  
		        <td>  <!--valign="top"  height="15"> <span class="BlackBold_11_11"> InPatient: </span> <br/-->
							<table>	 <!--The container table with $cols columns-->
						  	<tr>
									<td align="left" class="Black_1010">InPatient Wards:</td>
						  	</tr>
				<?php $cols=2; 		// Here we define the number of columns
					do{ ?>
							  <tr>
						<?php for($i=1;$i<=$cols;$i++){	// All the rows will have $cols columns even if the records are less than $cols
							$row=mysql_fetch_array($InPatient);
								if($row){ ?>      
									<td>
										<table>
											<tr valign="top">
  											<td><input name="radio" type="radio" class="BlackBold_11_11" value="<?php echo $row['id']; ?>" <?php echo ($row_visitedit['vfeeid'] == $row['id'])?'checked':'' ?>/></td>
												<td class="BlackBold_11_11"><?php echo substr($row['name'], 0, strpos($row['name'], ' ')) ?></td>
												<td class="BlackBold_11_11"> (<?php echo $row['fee'] ?>)</td>									
													<!--<td class="BlackBold_11_11" width="20">&nbsp;</td>-->	<!-- Create gap between columns -->
											</tr>
										</table>
									</td>
					<?php	}  // if($row)
							}  // row loop?> 
							  </tr>
				<?php } while($row); ?>	
							</table>
			  		</td>		  
<!-- ***************** Antenatal ******************************* -->		  
		        <td  valign="top">  <!--valign="top"  height="15"> <span class="BlackBold_11_11"> Antenatal: </span> <br/-->
							<table>	 <!--The container table with $cols columns-->
						  	<tr>
									<td align="left" class="Black_1010">Antenatal Clinics:</td>
        	<?php $cols=1; 		// Here we define the number of columns
						 do{ ?>
							  <tr>
        		<?php for($i=1;$i<=$cols;$i++){	// All the rows will have $cols columns even if
									// the records are less than $cols
							$row=mysql_fetch_array($Antenatal);
			
						if($row){	?>      
									<td>
										<table>
											<tr valign="top">
  											<td><input name="radio" type="radio" class="BlackBold_11_11" value="<?php echo $row['id']; ?>" <?php echo ($row_visitedit['vfeeid'] == $row['id'])?'checked':'' ?>/></td>
												<td class="BlackBold_11_11"><?php echo substr($row['name'], 0, strpos($row['name'], ' ')) ?></td>
												<td class="BlackBold_11_11"> (<?php echo $row['fee'] ?>)</td>								
												<!--<td class="BlackBold_11_11" width="20">&nbsp;</td>	<!-- Create gap between columns -->
											</tr>
										</table>
									</td>
					<?php	}  // if($row)
							}  // row loop?> 
							  </tr>
					<?php	} while($row); ?>
						 </table>
				  	</td>	
					<!--End of antenatal -->
				<?php } ?> <!-- end of if not paid-->
						<td>
            	<table>
                <tr>
 			  					<td align="right" valign="bottom"><input type="submit" name="Submit"  style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="Save Visit" /></td>
               </tr>
							</table>
            </td>

				  </tr>
				</table>
		 </td>
	 </tr>
 	 <tr>
    	<td>
      	<table width="100%" border="0" bgcolor="#F8FDCE">
<?php if(isset($row_surgordervisit['visitid'])) {  ?>
				<tr>
        	<td colspan="8" bgcolor="#ff99aa" >Is this visit for <strong><?php echo $row_surgordervisit['name'] ?> </strong> surgery?
            <input type="checkbox" name="surgery1" checked="checked" /> <strong>Uncheck this box if this visit is not for surgery.</strong></td>
						<input type="hidden"   name="surgid" id="surgid" value="<?php echo $row_surgordervisit['id'] ?>" />
						<input type="hidden"   name="surgvisitid" id="surgvisitid" value="<?php echo $row_surgordervisit['visitid'] ?>" />
        </tr>
<?php } ?>

<?php if(isset($row_origsurgvisit['origvisitid'])) {  ?>
				<tr>
        	<td colspan="8" bgcolor="#ff99aa" ><strong>If this is not visit for Surgery: <?php echo $row_origsurgvisit['name'] ?> </strong>
            <input type="checkbox" name="surgery2" /> <strong>Check this box to return surgery to original visit.</strong></td>
						<input type="hidden"   name="surgid" id="surgid" value="<?php echo $row_origsurgvisit['id'] ?>" />
						<input type="hidden"   name="surgvisitid" id="surgvisitid" value="<?php echo $row_origsurgvisit['origvisitid'] ?>" />
        </tr>
<?php } ?>
					<tr>
					  <td nowrap="nowrap"><span class="BlueBold_10">#<?php echo $row_visitedit['id']; ?> -Ord#- <?php echo $row_visitedit['ordid']; ?></span></td>
					  <td nowrap="nowrap" ><div align="right"  style="font-size:10px;">Entry Date:</div></td>
					  <td title="VID: <?php echo $row_visitedit['id']; ?>"><input name="entrydt" type="text" id="entrydt" style="font-size:10px;" readonly="readonly" value="<?php echo $row_visitedit['entrydt']; ?>" size="12" /></td>
	
						<td nowrap="nowrap" style="font-size:10px;"><div align="right">Type:</div></td>
						<td><input name="pat_type" type="text" readonly="readonly" id="pat_type" style="font-size:10px;" size="8" value="<?php echo $row_visitedit['pat_type']; ?>" /></td>
						<td><div align="right" style="font-size:10px;">Location:</div></td>
						<td><input name="location" type="text" readonly="readonly" id="location" style="font-size:10px;" size="8" value="<?php echo $row_visitedit['location']; ?>"/>
							<input name="urgency" type="hidden" value="Routine" /></td>
		 <!-- 	<td><div align="right">Urgency:</div></td>
			  		<td>
							<select name="urgency" id="urgency">
								<option value="Routine">Routine</option>
								<option value="Scheduled">Scheduled</option>
								<option value="ASAP">ASAP</option>
								<option value="STAT">STAT</option>
					 		</select>
          	</td>-->
				 		<td>&nbsp;</td>
	<?php if(!empty($row_visitedit['diagnosis'])) {
          if(!empty($row_visitedit['discharge']) &&  (allow(20,1) == 4)) { ?>
			  		<td><input name="discharge" type="text" id="discharge" class="Black_1011" value="<?php echo $row_visitedit['discharge']; ?>" size="14" /></td>
 	 <?php } else { ?>
            <td nowrap="nowrap"><input name="discharge" type="text" id="discharge" class="Black_1011" readonly="readonly" value="" /></td>
<!--			  		<td nowrap="nowrap">Discharged:<input name="discharge" id="discharge" style="font-size:10px;" readonly="readonly" value="<?php //echo $row_visitedit['discharge']; ?>" />
-->
	 <?php }
	     }
	 				else {?>
						<td style="font-size:9px;" >No Diagnosis</td>
            <input name="discharge" type="hidden" id="discharge" value="" />
  <?php }?>
<!--                  <input name="discharge" type="hidden" id="discharge" value="<?php //echo $row_visitedit['discharge']; ?>"
-->        
<!--			  		<td nowrap="nowrap"><div align="right"> Discharged: </div></td>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php // echo $row_visitedit['ordid'] ?></td>-->
				 		<td>&nbsp;</td>
					</tr>
         </table>
         <table width="100%" border="0" bgcolor="#F8FDCE"> 
					<tr>
			  	<td colspan="4"><span style="font-size:10px;">Visit Reason</span><br /><textarea name="visitreason" cols="30" rows="1" id="visitreason" data-validation="required" data-validation-length="min3" data-validation-error-msg="Visit Reason Required - Min = 3 Characters ********************************"><?php echo $row_visitedit['visitreason']; ?></textarea></td>
			  	<td colspan="2">
						<table width="100%">
							<tr>
					  		<td nowrap="nowrap"><span style="font-size:10px;">Height&nbsp;cm</span><br /><input name="height" type="text" size="5" value="<?php echo $row_visitedit['height']; ?>" data-validation="number"  data-validation-allowing="range[1;200]" data-validation-optional="true"  data-validation-error-msg="Height: Number between 1 and 200 Required ********************************"/></td>
					  		<td nowrap="nowrap"><span style="font-size:10px;">Weight&nbsp;kilo</span><br /><input name="weight" type="text" size="5" value="<?php echo $row_visitedit['weight']; ?>" data-validation="number" data-validation-allowing="range[1;300]" data-validation-optional="true"   data-validation-error-msg="Weight: Number between 1 and 300 Required ********************************" /></td>
							</tr>
			    	</table>
     		  </td>
 		  
            <td colspan="4"><span style="font-size:10px;">Diagnosis:</span><br /><textarea name="diagnosis" cols="30" rows="1" id="diagnosis" ><?php echo $row_visitedit['diagnosis']; ?></textarea></td>
            <td nowrap><span style="font-size:9px;">Return: Date</span> <!--<b>&#8657;<b>--><br>
            <input name="returndt" type="text" class="Black_1011" id="returndt" value="<?php echo $row_visitedit['returndate']; ?>" size="14" maxlength="12"/>
            </td>
            <td nowrap><span style="font-size:9px;">Location</span> <!--<b>&#8659;<b>--><br>
                <select name="returnloc" class="Black_1011" id="returnloc">  <!--value="<?php echo $row_visitedit['returnloc']; ?>"-->
               <option value="" <?php if (!(strcmp("", $row_visitedit['returnloc']))) {echo "selected=\"selected\"";} ?>>Select</option>
 
        				<?php do {  ?>
        			<option value="<?php echo $row_locations['name']?>" <?php if (!(strcmp($row_locations['name'], $row_visitedit['returnloc']))) {echo "selected=\"selected\"";} ?>><?php echo $row_locations['name']?></option>
					  <?php
                  } while ($row_locations = mysql_fetch_assoc($locations));
							  $rows = mysql_num_rows($locations);
							  if($rows > 0) {
									mysql_data_seek($locations, 0);
								  $row_locations = mysql_fetch_assoc($locations);
							} ?>
      			</select>

            </td>
              

     			  	<input name="billstatus" type="hidden" id="billstatus" value="Due" />
         		  <input name="booking" type="hidden" id="booking" value="<?php echo $totalRows_booking ?>" />
         		  <input name="followup" type="hidden" id="followup" value="<?php echo $totalRows_followup ?>" />
          		<input name="rate" type="hidden" id="rate" value="<?php echo $row_visitedit['rate']; ?>" />
		          <input name="ratereason" type="hidden" id="ratereason" value="<?php echo $row_visitedit['ratereason']; ?>" />
		          <input name="status" type="hidden" id="status" value="<?php echo $row_visitedit['status']; ?>" />
		          <input name="ordstatus" type="hidden" id="ordstatus" value="Ordered" />
		          <input name="ordid" type="hidden" id="ordid" value="<?php echo $row_visitedit['ordid']; ?>" />
		          <input name="feeid" type="hidden" id="feeid" value="<?php echo $row_visitedit['feeid']; ?>" />
		          <input name="amtpaid" type="hidden" id="amtpaid" value="<?php echo $row_visitedit['amtpaid']; ?>" />
		          <input name="vid" type="hidden" id="vid" value="<?php echo $row_visitedit['id']; ?>" />
		          <input name="dischgd" type="hidden" id="dischgd" value="<?php echo $row_visitedit['discharge']; ?>" />
		          <input name="entrydt" type="hidden" id="entrydt" value="<?php echo date("Y-m-d H:i:s"); ?>" />
        		  <input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
							<input name="medrecnum" type="hidden" id="medrecnum" value="<?php echo $_SESSION['mrn']; ?>" />
          		<input type="hidden" name="MM_update" value="form1" />
					 
          </tr>
      	</table>
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

<script type="text/javascript" src="../../nogray_js/1.2.2/ng_all.js"></script>
<script type="text/javascript" src="../../nogray_js/1.2.2/components/calendar.js"></script>
<!--<script type="text/javascript" src="../../nogray_js/1.2.2/components/timepicker.js"></script>-->
<script type="text/javascript">

//ng.Calendar.set_date_format ('Y-m-d');

ng.ready( function() {
	    var my_cal = new ng.Calendar({
        input:'returndt',
		//  start_date: '01-01-2014',
		  display_date: new Date()   // the display date (default is start_date)
      });
	    var my_cal = new ng.Calendar({
        input:'dissscharge'
		//  start_date: '01-01-2014',
		 // display_date: new Date()   // the display date (default is start_date)
      });
});

</script>

</body>
</html>