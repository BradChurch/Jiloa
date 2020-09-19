<?php session_start(); // 'Start new or resume existing session'  $_SESSION['sysconn'] seems to unavailable default?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>

<?php
//$editFormAction = $_SERVER['PHP_SELF'];
//if (isset($_SERVER['QUERY_STRING'])) {
//  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
//}
//
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
//	if (isset($_POST['laborder'])) {
//	    echo var_dump($_POST['laborder']);  // to see $_POST['laborder'] content
//	exit;
//	}
//	if (isset($_POST['surgorder'])) {
//    echo 'surgorder'.var_dump($_POST['surgorder']);
//    echo 'surgorder'.var_dump($_POST["MM_insert"]);
//  exit;
//	}  
//	if (isset($_POST['anestorder'])) {
//    echo 'anestorder'.var_dump($_POST['anestorder']);
//    echo 'anestorder'.var_dump($_POST["MM_insert"]);
//	}
//  exit;

// used for lab orders and immunizatio orders
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formplv1")) {
	if (isset($_POST['laborder'])) {
	   $order = $_POST['laborder'];
       $N = count($order);
//    echo("You selected $N order(s): ");
		for($i=0; $i < $N; $i++) {
		
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_Fee = sprintf("SELECT fee from fee where id = '".$order[$i]."'");
	$Fee = mysql_query($query_Fee, $swmisconn) or die(mysql_error());
	$row_Fee = mysql_fetch_assoc($Fee);
	$totalRows_Fee = mysql_num_rows($Fee);
	
	$billstatus = 'Due';
	if(isset($_POST['billstatus']) &&  $_POST['billstatus'] == 'paylater'){
		$billstatus = 'paylater';
		}
	$urgency = $_POST['urgency'];
   $rate = $_POST['rate']; 
		if(isset($_POST['urgency']) &&  $_POST['urgency'] == 'STAT'){
		$billstatus = 'paylater';
	   $rate = '125%';
		}
	$amtdue = $row_Fee['fee']*($rate/100);
		
  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, doctor, comments, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($_POST['visitid'], "int"),
                       $order[$i],
                       GetSQLValueString($rate, "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($billstatus, "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($urgency, "text"),
                       GetSQLValueString($_POST['doctor'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));


  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
//echo 'arrived';
			}
		  
		} // end of if laborder
	} // end of if form
//*************************************************************************************************************
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formSurgAdd")) {
	if (isset($_POST['surgorder'])) {
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_SurgFee = sprintf("SELECT fee from fee where id = '".$_POST['surgorder']."'");
	$SurgFee = mysql_query($query_SurgFee, $swmisconn) or die(mysql_error());
	$row_SurgFee = mysql_fetch_assoc($SurgFee);
	$totalRows_SurgFee = mysql_num_rows($SurgFee);
	
	$amtdue = $row_SurgFee['fee']*($_POST['rate']/100);
	
	$billstatus = 'Due';
	if(isset($_POST['billstatus']) &&  $_POST['billstatus'] == 'paylater'){
		$billstatus = 'paylater';
		}
		
  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, doctor, comments, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($_POST['visitid'], "int"),
                       GetSQLValueString($_POST['surgorder'],"int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($billstatus, "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['doctor'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));


  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
//*************************************************************************************************************
//************************************************************************************************************* and feeid = '".$_POST['surgorder']."'
	mysql_select_db($database_swmisconn, $swmisconn);   // find the receipt number
	$query_maxid = "SELECT MAX(id) mxoid from orders WHERE medrecnum = '".$_POST['medrecnum']."' and visitid = '".$_POST['visitid']."'";  
	$maxid = mysql_query($query_maxid, $swmisconn) or die(mysql_error());
	$row_maxid = mysql_fetch_assoc($maxid);
	$totalRows_maxid = mysql_num_rows($maxid);


  $insertSQL = sprintf("INSERT INTO surgery (medrecnum, visitid, feeid, ordid, status, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($_POST['visitid'], "int"),
                       GetSQLValueString($_POST['surgorder'], "int"),
                       GetSQLValueString($row_maxid['mxoid'], "int"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));


  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
	// end of if surgorder
	// find last surgid 
	mysql_select_db($database_swmisconn, $swmisconn);   // find the receipt number
	$query_maxid = "SELECT MAX(id) mxid from surgery";  
	$maxid = mysql_query($query_maxid, $swmisconn) or die(mysql_error());
	$row_maxid = mysql_fetch_assoc($maxid);
	$totalRows_maxid = mysql_num_rows($maxid);
	// Create initial record for anesthesia
  $insertSQL = sprintf("INSERT INTO anesthesia (medrecnum, visitid, surgid, surgfeeid, status, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($_POST['visitid'], "int"),
                       GetSQLValueString($row_maxid['mxid'], "int"),
                       GetSQLValueString($_POST['surgorder'], "int"),
                       GetSQLValueString('ordered', "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));
  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

  $insertSQL = sprintf("INSERT INTO surgcountbegin (surgid, entryby, entrydt) VALUES (%s, %s, %s)",
                       GetSQLValueString($row_maxid['mxid'], "int"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));
  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

  $insertSQL = sprintf("INSERT INTO surgcountend (surgid, entryby, entrydt) VALUES (%s, %s, %s)",
                       GetSQLValueString($row_maxid['mxid'], "int"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));
  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
	
	
	
	} // end of add inital anesthesia 
}// end of surgery
//*************************************************************************************************************
//echo 'surgorder'.$_POST['surgorder'];
//exit;
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formAnestPreop")) {
	if (isset($_POST['anestorder'])) {
		
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_AnestFee = sprintf("SELECT fee from fee where id = '".$_POST['anestorder']."'");
	$AnestFee = mysql_query($query_AnestFee, $swmisconn) or die(mysql_error());
	$row_AnestFee = mysql_fetch_assoc($AnestFee);
	$totalRows_AnestFee = mysql_num_rows($AnestFee);
	
	$amtdue = $row_AnestFee['fee']*($_POST['rate']/100);
	
	$billstatus = 'Due';
	if(isset($_POST['billstatus']) &&  $_POST['billstatus'] == 'paylater'){
		$billstatus = 'paylater';
		}
		
  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, doctor, comments, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($_POST['visitid'], "int"),
                       GetSQLValueString($_POST['anestorder'],"int"),
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($billstatus, "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['doctor'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));


  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());
	}
} // end of AnestAdd
//*************************************************************************************************************

//used in auto ordering antenatal booking
	if (isset($_POST['anteorder'])) { 
	//array(2) { [0]=> string(2) "49" [1]=> string(2) "28" }
	//array(4) { [0]=> string(2) "49" [1]=> string(2) "28" [2]=> string(2) "24" [3]=> string(2) "51" }
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

	$billstatus = 'Due';
	if(isset($_POST['billstatus'])){
	$billstatus = $_POST['billstatus'];	
		}
	
	  $insertSQL = sprintf("INSERT INTO orders (medrecnum, visitid, feeid, rate, ratereason, amtdue, amtpaid, billstatus, status, urgency, doctor, comments, entryby, entrydt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['medrecnum'], "text"),
                       GetSQLValueString($_POST['visitid'], "int"),
                       $ante[$j],
                       GetSQLValueString($_POST['rate'], "int"),
                       GetSQLValueString($_POST['ratereason'], "text"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($billstatus, "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['doctor'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($insertSQL, $swmisconn) or die(mysql_error());

		 // echo($N.' - '.$ante[$j] . " ");
    } //  FOR loop
  } // end of if anteorder
//*****************************************************************************************************

  $insertGoTo = "PatShow1.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_POST['qrystr'];
  }
  header(sprintf("Location: %s", $insertGoTo));

// PatShow1.php?mrn=3&vid=10&visit=PatVisitView.php&act=lab&pge=PatLabView.php

?>

