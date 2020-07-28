
<?php require_once('../../Connections/swmisconn.php'); ?>

<?php $visitid = 99999;?>

<?php // query to display the selected surgery
mysql_select_db($database_swmisconn, $swmisconn);
$query_surg = "SELECT s.id sid, s.medrecnum, s.visitid, s.feeid, s.surgdate, (Select userid from users where id = s.surgeon) surgeon, (Select userid from users where id = s.surgeonassist) surgassist, (Select userid from users where id = s.anesthetist) anesthetist, s.anesttechnique, s.preopdiag, s.postopdiag, s.incision, s.findings, s.procedures, s.difficulties, s.closure, s.postoporders, s.status, s.entrydt, s.entryby, f.name, f.descr, p.lastname, p.firstname, p.othername, p.gender FROM surgery s join fee f on s.feeid = f.id JOIN patperm p on p.medrecnum = s.medrecnum WHERE s.visitid = '".$visitid."' order by s.id";  //'".$visitid."'
$surg = mysql_query($query_surg, $swmisconn) or die(mysql_error());
$row_surg = mysql_fetch_assoc($surg);
$totalRows_surg = mysql_num_rows($surg);
?>

<?php
//preop query
 mysql_select_db($database_swmisconn, $swmisconn);
$query_anestpreop = "SELECT a.id aid, a.surgid, a.medrecnum, a.visitid, a.surgid, a.surgfeeid, a.preopexamdt, a.preopdiag, a.anesthetist, a.pathisthtn, a.pathistdm, a.pathistchestdx, a.pathisthrtdx, a.pathistscd, a.pathisttb, a.pathistseiz, a.pathistkidneydx, a.pathistprevop, a.pathistothers, a.famhisthtn, a.famhisthrtdx, a.famhistdm, a.famhistkidneydx, a.famhistchestdx, a.famhistseiz, a.famhistscd, a.famhisttb, a.famhistothers, a.sociahistedu, a.sociahistfinstatus, a.sociahistsupports, a.sociahisthome, a.sociahistsexual, a.sociahisthabit, a.sociahistwork, a.socishisttravels, a.sociahistsickcontact, a.sociahistothers, a.allergies, a.dentalstatus, a.labstatus, a.nutritionstatus, a.physicalstatus, a.asagrading, a.lastoralintake, a.preanestorders, a.anesttechnique, a.status, a.entrydt, a.entryby, f.name, f.descr FROM anesthesia a JOIN fee f on f.id = a.surgfeeid WHERE a.surgid = ".$row_surg['sid']."";
$anestpreop = mysql_query($query_anestpreop, $swmisconn) or die(mysql_error());
$row_anestpreop = mysql_fetch_assoc($anestpreop);
$totalRows_anestpreop = mysql_num_rows($anestpreop);
?>

<?php
// order query
 mysql_select_db($database_swmisconn, $swmisconn);
$query_ordered = "SELECT o.id, o.medrecnum, o.visitid, o.feeid, o.rate, o.doctor, substr(o.status,1,7) status, substr(o.urgency,1,1) urg, DATE_FORMAT(o.entrydt,'%d%b%y %H:%i') entrydt, o.entryby, o.amtdue, o.amtpaid, f.section, f.name, f.descr FROM orders o, fee f WHERE o.feeid = f.id and f.dept = 'Surgery' and Section = 'Anesthesia' and o.visitid ='".$visitid."' ORDER BY entrydt ASC";
$ordered = mysql_query($query_ordered, $swmisconn) or die(mysql_error());
$row_ordered = mysql_fetch_assoc($ordered);
$totalRows_ordered = mysql_num_rows($ordered);
?>
<?php

//intraop query 
mysql_select_db($database_swmisconn, $swmisconn);
$query_anestintraop = "SELECT id aid, medrecnum, visitid, surgid, intraopdt, aneststarttime, surgstarttime, surgeon, surgeonassist, anesthetist, hblevel, ebv, mabl, bldgp, PtCondition, cannularsite, intubation, circuit, resp, localanest, localanestposition, drugchart, vitalchart, fluidchart, totalbldloss, totalfluidgiven, anestcomplications, surgendtime, anestendtime, status, entrydt, entryby FROM anesthesia WHERE surgid='".$row_surg['sid']."'";
$anestintraop = mysql_query($query_anestintraop, $swmisconn) or die(mysql_error());
$row_anestintraop = mysql_fetch_assoc($anestintraop);
$totalRows_anestintraop = mysql_num_rows($anestintraop);

//find drugs for this anesthesia procedure
$colname_anestdrugs = "-1";
if (isset($row_anestintraop['aid'])) {
  $colname_anestdrugs = $row_anestintraop['aid'];
}
mysql_select_db($database_swmisconn, $swmisconn);
$query_anestdrugs = "SELECT ad.id, ad.druglistid, ad.anestid, ad.begindrug, ad.enddrug, al.drug  FROM anestdrug ad join anestdruglist al on ad.druglistid = al.id WHERE anestid = '".$row_anestintraop['aid']."' ORDER BY ad.begindrug ASC";
$anestdrugs = mysql_query($query_anestdrugs, $swmisconn) or die(mysql_error());
$row_anestdrugs = mysql_fetch_assoc($anestdrugs);
$totalRows_anestdrugs = mysql_num_rows($anestdrugs);

//postop query
mysql_select_db($database_swmisconn, $swmisconn);
$query_anestpostop = "SELECT id aid, medrecnum, visitid, surgid, postopexamdt, postopcomplications, otherfindings, status, entrydt, entryby FROM anesthesia WHERE surgid = '".$row_surg['sid']."'";
$anestpostop = mysql_query($query_anestpostop, $swmisconn) or die(mysql_error());
$row_anestpostop = mysql_fetch_assoc($anestpostop);
$totalRows_anestpostop = mysql_num_rows($anestpostop);


?>



<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css">
</head>
<body>
<!--*****************SURGERY **********************************************-->

  <?php if($totalRows_surg > 0) { ?>
  <?php do { ?>
<p> </p>
<p class="RedBold_30"> Patient: <?php echo $row_surg['lastname']?>, <?php echo $row_surg['firstname']?>, <?php echo $row_surg['othername']?>&nbsp;&nbsp;&nbsp;  Gender   <?php echo $row_surg['gender']?></p>
<p  class="BlueBold_30">&#9988;&#9988;&#9988; Surgery - <?php echo $row_surg['name'].': '.$row_surg['descr']?> &#9988;&#9988;&#9988; </p>

<table width="1000px" border="1" cellpadding="1" cellspacing="1" bgcolor="#f9d2f9">
		<?php   $bgc = "#CCCCCC";
				if($row_surg['status'] == 'Ordered'){ $bgc = "#7ac701"; }
				if($row_surg['status'] == 'Scheduled'){ $bgc = "#FFFDDA"; }
				if($row_surg['status'] == 'In-Progress'){ $bgc = "#d3e4f4"; }
				if($row_surg['status'] == 'Recovery'){ $bgc = "#ff9800"; }
				if($row_surg['status'] == 'Complete'){ $bgc = "#e6442e"; }
		?>
      
	<tr>
    <td align="right" bgcolor="#FFFDDA">Surgery:</td>
   <!-- title="Surg Id: <?php echo $row_surg['sid'] ?>&#10;MRN: <?php echo $row_surg['medrecnum'] ?>&#10;Visit Id: <?php echo $row_surg['visitid'] ?>  "-->
    <td bgcolor=<?php echo $bgc ?>>Status: <input type="text" name="statusdisp" id="statusdisp" size="7" readonly value= "<?php echo $row_surg['status'] ?>"></td>
<!--Display Surgery name-->
    <td align="right" scope="row">Surgery Name:</td>
    <td colspan="2" title ="Feeid : <?php echo $row_surg['feeid'] ?>">
      <input type="text" name="surgname" id="surgname" size="50" readonly value="<?php echo $row_surg['name'].': '.$row_surg['descr']?>">
    </td>
  </tr>
  <tr>
 <!-- Display/Update Surg date/Time-->
     <td height="18" align="right" scope="row">Surgery Date/Time:</td>
    <td align="left" nowrap="nowrap">
             <input id="scheddt" name="scheddt" type="text" size="12" maxlength="16" autocomplete="off" value="<?php echo  date('D M d, Y', $row_surg['surgdate']);?>" />
             <input id="scheddt_alt" name="scheddt_alt" type="text" size="6" maxlength="15" autocomplete="off" value="<?php echo  date('h:i a', $row_surg['surgdate']) ;?>" />
		</td>
<!-- Surgeon-->          
    <td nowrap="nowrap">Surgeon: <?php echo $row_surg['surgeon'] ?>
<!-- Surgeon Assistant-->          
    <td nowrap>Surgeon Assistant: <?php echo $row_surg['surgassist'] ?>
<!-- Anesthetist-->          
    <td nowrap>Anesthetist: <?php echo $row_surg['anesthetist'] ?>
  </tr>
<!--enter/display preop diagnosis-->
  <tr>
    <td align="right" scope="row">Pre-Op Diagnosis</td>
    <td colspan="4"><?php echo $row_surg['preopdiag']?></td>
  </tr>
  <tr>
    <td align="right">Incision:</td>
    <td colspan="4"><?php echo $row_surg['incision']?></td>
  </tr>
  <tr>
    <td align="right">Findings:</td>
    <td colspan="4"><?php echo $row_surg['findings']?></td>
  </tr>
  <tr>
    <td align="right">Procedure:</td>
    <td colspan="4"><?php echo $row_surg['procedures']?></td>
  </tr>
  <tr>
    <td align="right">Difficulties:</td>
    <td colspan="4"><?php echo $row_surg['difficulties']?></td>
  </tr>
  <tr>
    <td align="right">Closure:</td>
    <td colspan="4"><?php echo $row_surg['closure']?></td>
  </tr>
  <tr>
    <td align="right" scope="row">Post-Op Diagnosis:</td>
    <td colspan="4"><?php echo $row_surg['postopdiag']?></td>
  </tr>
  <tr>
    <td align="right" scope="row">Post-Op Orders:</td>
    <td colspan="4"><?php echo $row_surg['postoporders']?></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  <!--Status -->         
    <td>Status: <?php echo $row_surg['status'] ?>
    <td> Entrydt: <?php echo $row_surg['entrydt'] ?>
    <td> Entryby: <?php echo $row_surg['entryby'] ?>
  </tr>
</table>
<!--*******  PRE OP **********************************************************************************************************************--> 
<p> <span style="color:white; font-size:24px;"> <?php echo str_repeat("&#9729;", 5);?></span><span class="BlueBold_24"> Anesthesia PREOP Exam</span><span style="color:white; font-size:24px;" ><?php echo str_repeat("&#9729;", 5);?></span></p>

<table width="50%" border="1" style="border-collapse:collapse;"  bgcolor="#BCFACC">
  <tr>
    <td>
      <table>
        <tr>
          <td>
            <table border="1" align="left" cellpadding="1" cellspacing="1"  style="border-collapse:collapse;">
              <tr>
           <!--display Status AnesthesiaID, Pat Medrecnum, Pat visitID, surgeryID-->       
                <td nowrap>Status:<input type="text" name="status" id="status" size="7" readonly value= "<?php echo $row_anestpreop['status'] ?>"></td>
                <td>Anesthesia ID: <input type="text" name="id" id="id" size="7" readonly value= "<?php echo $row_anestpreop['aid'] ?>"></td>
                <td>MRN:<input type="text" name="medrecnum" id="medrecnum" size="7" readonly value= "<?php echo $row_anestpreop['medrecnum'] ?>"></td>
                <td>Visit ID:<input type="text" name="visitid" id="visitid" size="7" readonly value= "<?php echo $row_anestpreop['visitid'] ?>"></td>          
                <td>Surgery ID:<input type="text" name="surgid" id="surgid" size="7" readonly value= "<?php echo $row_anestpreop['surgid'] ?>"></td>
              </tr>
              <tr>
                <td height="18" align="right">Preopexamdt:</td>
								<td  colspan="3" align="left" nowrap="nowrap">
                  <input id="preopddt" name="preopddt" type="text" size="10" maxlength="30" value="<?php echo date('Y-m-d', $row_anestpreop['preopexamdt']) ;?>" />                  <input id="preopddt_alt" name="preopddt_alt" type="text" size="6" maxlength="15" value="<?php echo  date('h:i a', $row_anestpreop['preopexamdt']) ;?>" /></td>
      <!--Select/display anesthesia Technique-->
             <td>&nbsp;</td>
              <tr>
      <!--Display Surgery name-->
                <td align="right">Surgery Name:</td>
                <td colspan="3" title ="SurgFeeid  <?php echo $row_anestpreop['surgfeeid'] ?>"><input type="text" name="surgname" id="surgname" size="50" readonly value="<?php echo $row_anestpreop['name'].': '.$row_anestpreop['descr']?>"></td>
              </tr>
              <tr>
      <!-- Select/Display anesthetist -->
                <td align="right" >Anesthetist:</td>
                <td><input type="text" name="anesthetist" id="anesthetist" size="7" readonly value= "<?php echo $row_anestpreop['visitid'] ?>"></td>          
              </tr>
      <!--enter/display preop diagnosis-->
              <tr>
                <td align="right" scope="row">Pre-Op Diagnosis</td>
                <td colspan="4"><textarea name="preopdiag" id="preopdiag" class="surgdata" ><?php echo $row_anestpreop['preopdiag']?></textarea></td>
              </tr>
            </table>																																
    </td>
    <td>  
		  <table border="1" align="left" cellpadding="1" cellspacing="1" style="border-collapse:collapse;">
				<tr>
        	<td colspan="9" class="flagWhiteonGreen">Anesthesia Orders</td>
				</tr>
        <tr>
                  <td nowrap="nowrap" class="BlackBold_11">&nbsp;</td>
                  <td nowrap="nowrap" class="BlackBold_11">Date/Time</td>
                  <td nowrap="nowrap" class="BlackBold_11">Ord#*</td>
                  <td nowrap="NOWRAP" class="BlackBold_11" title="<?php echo $row_ordered['descr']; ?>">Test*</td>
                  <td nowrap="nowrap" class="BlackBold_11">Urg</td>
                  <td nowrap="nowrap" class="BlackBold_11">Status</td>
                  <td nowrap="nowrap" class="BlackBold_11">Due</td>
                  <td nowrap="nowrap" class="BlackBold_11">Paid</td>
                </tr>
                <?php do { ?>
                <tr>
                  <?php if (!empty($row_ordered['id']) and empty($row_ordered['amtpaid']) ) {  // and allow(51,4) == 1 ?>
                  <td class="BlackBold_11" nowrap="nowrap"><a href="PatShow1.php?mrn=<?php echo $row_ordered['medrecnum']; ?>&vid=<?php echo $row_ordered['visitid']; ?>&visit=PatVisitView.php&act=hist&pge=PatOrdersView.php&ordchg=PatOrdersDelete.php&id=<?php echo $row_ordered['id'] ?>">Del</a></td>
                  <?php } else {?>
                  <td nowrap="nowrap" class="BlackBold_11">&nbsp;</td>
                  <?php } ?>
                  <td nowrap="nowrap" class="BlackBold_11" title="VID: <?php echo $row_ordered['visitid']; ?> &#10;EntryBy: <?php echo $row_ordered['entryby']; ?>"><?php echo $row_ordered['entrydt']; ?></td>
                  <td nowrap="nowrap" class="BlackBold_11" title="Doctor: <?php echo $row_ordered['doctor']; ?>"><div align="center"><?php echo $row_ordered['id']; ?></div></td>
                  <td nowrap="NOWRAP" class="BlackBold_11" title="<?php echo $row_ordered['descr']; ?>"><?php echo $row_ordered['name']; ?></td>
                  <td nowrap="nowrap" class="BlackBold_11"><?php echo $row_ordered['urg']; ?></td>
                  <td nowrap="nowrap" class="BlackBold_11"><?php echo $row_ordered['status']; ?></td>
                  <td nowrap="nowrap" class="BlackBold_11"><div align="right"><?php echo $row_ordered['amtdue']; ?></div></td>
                  <td nowrap="nowrap" class="BlackBold_11"><div align="right"><?php echo $row_ordered['amtpaid']; ?></div></td>
                </tr>
                <?php } while ($row_ordered = mysql_fetch_assoc($ordered)); ?>
							</table>
            </td>
          </tr>
		    </table>
   		</td>
 		</tr>
  	<tr>  
    	<td colspan="1"> 
			<table>  <!--enter/display patient history-->      
				<tr>
					<td nowrap="nowrap"><div align="center" class="BlackBold_14">Patient History:</div></td>
          
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" Title="Hypertension" ><div align="left">
          <input type="hidden" name="pathisthtn" id="pathisthtn" value="N" />
          <input type="checkbox" name="pathisthtn" id="pathisthtn" <?php if ($row_anestpreop['pathisthtn'] == "Y") {echo "checked=\"checked\"";} ?> />HTN</div></td>

        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Diabetes"><div align="left">
          <input type="hidden" id="pathistdm" name="pathistdm" value="N" />
          <input type="checkbox" id="pathistdm" name="pathistdm" <?php if ($row_anestpreop['pathistdm'] == "Y") {echo "checked=\"checked\"";} ?> />DM</div></td>

        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Chest Disease"><div align="left">
          <input type="hidden" id="pathistchestdx" name="pathistchestdx" value="N" />
          <input type="checkbox" id="pathistchestdx" name="pathistchestdx" <?php if ($row_anestpreop['pathistchestdx'] == "Y") {echo "checked=\"checked\"";} ?> />CD</div>  </td>
        
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" Title="Heart Disease" ><div align="left">
          <input type="hidden" name="pathisthrtdx" value="N" />
          <input type="checkbox" name="pathisthrtdx" id="pathisthrtdx" <?php if ($row_anestpreop['pathisthrtdx'] == "Y") {echo "checked=\"checked\"";} ?> />HD</div></td>

        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Sickle Cell Disease"><div align="left">
          <input type="hidden" id="pathistscd" name="pathistscd" value="N" />
          <input type="checkbox" id="pathistscd" name="pathistscd" <?php if ($row_anestpreop['pathistscd'] == "Y") {echo "checked=\"checked\"";} ?> />SCD</div></td>        
	
        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" title="Tuberculosis"><div align="left">
          <input type="hidden" name="pathisttb" id="pathisttb" value="N" />
          <input type="checkbox" name="pathisttb" id="pathisttb"<?php if ($row_anestpreop['pathisttb'] == "Y") {echo "checked=\"checked\"";} ?> />TB</div></td>

        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Seizures"><div align="left">
          <input type="hidden" id="pathistseiz" name="pathistseiz" value="N" />
          <input type="checkbox" id="pathistseiz" name="pathistseiz" <?php if ($row_anestpreop['pathistseiz'] == "Y") {echo "checked=\"checked\"";} ?> />Seiz</div></td>

        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Kidney Disease"><div align="left">
          <input type="hidden" id="pathistkidneydx" name="pathistkidneydx" value="N">
          <input type="checkbox" id="pathistkidneydx" name="pathistkidneydx" <?php if ($row_anestpreop['pathistkidneydx'] == "Y") {echo "checked=\"checked\"";} ?> />KD</div></td>
          	
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Operations"><div align="left">
          <input type="hidden" id="pathistprevop" name="pathistprevop" value="N">
          <input type="checkbox" id="pathistprevop" name="pathistprevop" <?php if ($row_anestpreop['pathistprevop'] == "Y") {echo "checked=\"checked\"";} ?> />Ops</div></td>
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="">&nbsp;</td>

        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Notes/Comments">Others:</td>
        	<td class="borderbottomthinblack" nowrap="nowrap" ><textarea name="pathistothers" cols="20" rows="1" id="pathistothers"><?php echo $row_anestpreop['pathistothers']; ?></textarea></td>
  
				</tr>
<!--enter/display family history-->

      	<tr>
        	<td class="borderbottomthinblackBold14" nowrap="nowrap" ><div align="center">Family History:</div></td>

        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Hypertension" ><div align="left">
          <input type="hidden" name="famhisthtn" id="famhisthtn" value="N" />
           <input type="checkbox" name="famhisthtn" id="famhisthtn" <?php if ($row_anestpreop['famhisthtn'] == "Y") {echo "checked=\"checked\"";} ?> />
HTN</div></td>
	
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Diabetes"><div align="left">
          <input type="hidden" id="famhistdm" name="famhistdm" value="N" />
          <input type="checkbox" id="famhistdm" name="famhistdm" <?php if ($row_anestpreop['famhistdm'] == "Y") {echo "checked=\"checked\"";} ?> />
DM</div></td>
          
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Chest Disease"><div align="left">
          <input type="hidden" id="famhistchestdx" name="famhistchestdx" value="N">
          <input type="checkbox" id="famhistchestdx" name="famhistchestdx" <?php if ($row_anestpreop['famhistchestdx'] == "Y") {echo "checked=\"checked\"";} ?> />
CD</div>  </td>
        
        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Heart Disease"><div align="left">
          <input type="hidden" name="famhisthrtdx" id="famhisthrtdx" value="N" />
          <input type="checkbox" name="famhisthrtdx" id="famhisthrtdx" <?php if ($row_anestpreop['famhisthrtdx'] == "Y") {echo "checked=\"checked\"";} ?> />
HD</div></td>

        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Sickle Cell Disease"><div align="left">
          <input type="hidden" id="famhistscd" name="famhistscd" value="N" />
          <input type="checkbox" id="famhistscd" name="famhistscd" <?php if ($row_anestpreop['famhistscd'] == "Y") {echo "checked=\"checked\"";} ?> />
SCD</div></td>

        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" title="Tuberculosis"><div align="left">
          <input type="hidden" name="famhisttb" id="famhisttb" value="N" />
          <input type="checkbox" name="famhisttb" id="famhisttb" <?php if ($row_anestpreop['famhisttb'] == "Y") {echo "checked=\"checked\"";} ?> />
TB</div></td>
       
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Seizures"><div align="left">
          <input type="hidden" id="famhistseiz" name="famhistseiz" value="N" />
          <input type="checkbox" id="famhistseiz" name="famhistseiz" <?php if ($row_anestpreop['famhistseiz'] == "Y") {echo "checked=\"checked\"";} ?> />
Seiz</div></td>
          
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Kidney Disease"><div align="left">
          <input type="hidden" id="famhistkidneydx" name="famhistkidneydx" value="N">
          <input type="checkbox" id="famhistkidneydx" name="famhistkidneydx" <?php if ($row_anestpreop['famhistkidneydx'] == "Y") {echo "checked=\"checked\"";} ?> />
KD</div>  </td>

        	<td colspan="2" nowrap="nowrap" bgcolor="#C0E8D5" >&nbsp;</td>
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Notes/Comments">Others:</td>
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" ><textarea name="famhistothers" cols="20" rows="1" id="famhistothers"><?php echo $row_anestpreop['famhistothers']; ?></textarea></td>
    		</tr>
 <!--enter/display Social history-->
      	<tr>
        	<td class="borderbottomthinblackBold14" nowrap="nowrap" ><div align="center">Social History:</div></td>
 
        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Education" ><div align="left">
          <input type="hidden" name="sociahistedu" id="sociahistedu" value="N" />
           <input type="checkbox" name="sociahistedu" id="sociahistedu" <?php if ($row_anestpreop['sociahistedu'] == "Y") {echo "checked=\"checked\"";} ?> />
EDU</div></td>
        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Financial Status" ><div align="left">
          <input type="hidden" name="sociahistfinstatus" id="sociahistfinstatus" value="N" />
           <input type="checkbox" name="sociahistfinstatus" id="sociahistfinstatus" <?php if ($row_anestpreop['sociahistfinstatus'] == "Y") {echo "checked=\"checked\"";} ?> />
FS</div></td>

        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Social Supports" ><div align="left">
          <input type="hidden" name="sociahistsupports" id="sociahistsupports" value="N" />
           <input type="checkbox" name="sociahistsupports" id="sociahistsupports" <?php if ($row_anestpreop['sociahistsupports'] == "Y") {echo "checked=\"checked\"";} ?> />
SS</div></td>

        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Social Home" ><div align="left">
          <input type="hidden" name="sociahisthome" id="sociahisthome" value="N" />
           <input type="checkbox" name="sociahisthome" id="sociahisthome" <?php if ($row_anestpreop['sociahisthome'] == "Y") {echo "checked=\"checked\"";} ?> />
SH</div></td>

        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Sexually Active" ><div align="left">
          <input type="hidden" name="sociahistsexual" id="sociahistsexual" value="N" />
           <input type="checkbox" name="sociahistsexual" id="sociahistsexual" <?php if ($row_anestpreop['sociahistsexual'] == "Y") {echo "checked=\"checked\"";} ?> />
SA</div></td>

        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Social Habit" ><div align="left">
          <input type="hidden" name="sociahisthabit" id="sociahisthabit" value="N" />
           <input type="checkbox" name="sociahisthabit" id="sociahisthabit" <?php if ($row_anestpreop['sociahisthabit'] == "Y") {echo "checked=\"checked\"";} ?> />
SHB</div></td>

        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Social Work" ><div align="left">
          <input type="hidden" name="sociahistwork" id="sociahistwork" value="N" />
           <input type="checkbox" name="sociahistwork" id="sociahistwork" <?php if ($row_anestpreop['sociahistwork'] == "Y") {echo "checked=\"checked\"";} ?> />
SW</div></td>

        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Social Travels" ><div align="left">
          <input type="hidden" name="socishisttravels" id="socishisttravels" value="N" />
           <input type="checkbox" name="socishisttravels" id="socishisttravels" <?php if ($row_anestpreop['socishisttravels'] == "Y") {echo "checked=\"checked\"";} ?> />
ST</div></td>

        	<td nowrap="nowrap" bgcolor="#C0E8D5" class="borderbottomthinblack" Title="Contact with Sick Persons" ><div align="left">
          <input type="hidden" name="sociahistsickcontact" id="sociahistsickcontact" value="N" />
           <input type="checkbox" name="sociahistsickcontact" id="sociahistsickcontact" <?php if ($row_anestpreop['sociahistsickcontact'] == "Y") {echo "checked=\"checked\"";} ?> />
SC</div></td>

        	<td colspan="1" nowrap="nowrap" bgcolor="#C0E8D5" >&nbsp;</td>
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" title="Notes/Comments">Others:</td>
        	<td bgcolor="#C0E8D5" class="borderbottomthinblack" nowrap="nowrap" ><textarea name="sociahistothers" cols="20" rows="1" id="sociahistothers"><?php echo $row_anestpreop['famhistothers']; ?></textarea></td>

   <?php // do { ?>
       <?php //} while ($row_anestpreop = mysql_fetch_assoc($anestpreop)); ?>
        </tr>
			</table>
    </td>
	</tr>
  <tr> <!-- ********************** Begin Dental ********************************************-->
  	<td>Dental Status: N = Normal, O = Mobile,  X = Missing</td>
  </tr>
  <tr>
    <td>
      <table width="800px" border="0" cellspacing="1" cellpadding="1" style="border-collapse:collapse">
        <tr>
          <td width="50px" align="center">8</td>
          <td width="50px" align="center">7</td>
          <td width="50px" align="center">6</td>
          <td width="50px" align="center">5</td>
          <td width="50px" align="center">4</td>
          <td width="50px" align="center">3</td>
          <td width="50px" align="center">2</td>
          <td width="50px" align="center">1</td>
          <td width="50px">&nbsp; </td>
          <td width="50px" align="center">1</td>
          <td width="50px" align="center">2</td>
          <td width="50px" align="center">3</td>
          <td width="50px" align="center">4</td>
          <td width="50px" align="center">5</td>
          <td width="50px" align="center">6</td>
          <td width="50px" align="center">7</td>
          <td width="50px" align="center">8</td>
        </tr>
        <tr>
          <td><input type="text" name="0" id="0" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],0,1) ?>"></td>          
          <td><input type="text" name="1" id="1" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],1,1) ?>"></td>          
          <td><input type="text" name="2" id="2" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],2,1) ?>"></td>          
          <td><input type="text" name="3" id="3" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],3,1) ?>"></td>          
          <td><input type="text" name="4" id="4" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],4,1) ?>"></td>          
          <td><input type="text" name="5" id="5" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],5,1) ?>"></td>          
          <td><input type="text" name="6" id="6" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],6,1) ?>"></td>          
          <td><input type="text" name="7" id="7" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],7,1) ?>"></td>          
          <td width="50px">&nbsp;</td>
          <td><input type="text" name="8" id="8" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],8,1) ?>"></td>          
          <td><input type="text" name="9" id="9" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],9,1) ?>"></td>          
          <td><input type="text" name="10" id="10" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],10,1) ?>"></td>          
          <td><input type="text" name="11" id="11" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],11,1) ?>"></td>          
          <td><input type="text" name="12" id="12" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],12,1) ?>"></td>          
          <td><input type="text" name="13" id="13" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],13,1) ?>"></td>          
          <td><input type="text" name="14" id="14" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],14,1) ?>"></td>          
          <td><input type="text" name="15" id="15" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],15,1) ?>"></td>          
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
          <td width="50px">&nbsp;</td>
        </tr>
      
        <tr>
          <td><input type="text" name="16" id="16" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],16,1) ?>"></td>          
          <td><input type="text" name="17" id="17" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],17,1) ?>"></td>          
          <td><input type="text" name="18" id="18" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],18,1) ?>"></td>          
          <td><input type="text" name="19" id="19" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],19,1) ?>"></td>          
          <td><input type="text" name="20" id="20" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],20,1) ?>"></td>          
          <td><input type="text" name="21" id="21" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],21,1) ?>"></td>          
          <td><input type="text" name="22" id="22" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],22,1) ?>"></td>          
          <td><input type="text" name="23" id="23" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],23,1) ?>"></td>          
          <td>&nbsp;</td>
          <td><input type="text" name="24" id="24" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],24,1) ?>"></td>          
          <td><input type="text" name="25" id="25" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],25,1) ?>"></td>          
          <td><input type="text" name="26" id="26" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],26,1) ?>"></td>          
          <td><input type="text" name="27" id="27" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],27,1) ?>"></td>          
          <td><input type="text" name="28" id="28" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],28,1) ?>"></td>          
          <td><input type="text" name="29" id="29" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],29,1) ?>"></td>          
          <td><input type="text" name="30" id="30" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],30,1) ?>"></td>          
          <td><input type="text" name="31" id="31" size ="1" readonly value= "<?php echo substr($row_anestpreop['dentalstatus'],31,1) ?>"></td>          
        </tr>     
        <tr>
          <td width="50px" align="center">8</td>
          <td width="50px" align="center">7</td>
          <td width="50px" align="center">6</td>
          <td width="50px" align="center">5</td>
          <td width="50px" align="center">4</td>
          <td width="50px" align="center">3</td>
          <td width="50px" align="center">2</td>
          <td width="50px" align="center">1</td>
          <td width="50px" >&nbsp;</td>
          <td width="50px" align="center">1</td>
          <td width="50px" align="center">2</td>
          <td width="50px" align="center">3</td>
          <td width="50px" align="center">4</td>
          <td width="50px" align="center">5</td>
          <td width="50px" align="center">6</td>
          <td width="50px" align="center">7</td>
          <td width="50px" align="center">8</td>
        </tr>
      </table>
    </td>
   </tr>  <!-- end of dental-->
   <tr>
    <td>
      <table border="1 style="border-collapse:collapse;"">
        <tr>
   <!--Allergy selector -->         
	        <td>Allergies: <input type="text" name="allergies" id="allergies" readonly value= "<?php echo substr($row_anestpreop['allergies'],31,1) ?>"></td>          
<!--Lab Status -->         
					<td>Lab Status: <input type="text" name="labstatus" id="labstatus" readonly value= "<?php echo substr($row_anestpreop['labstatus'],31,1) ?>"></td>          
<!--Nutrition Status -->         
					<td>Nutrition Status:<input type="text" name="nutritionstatus" id="nutritionstatus" readonly value= "<?php echo substr($row_anestpreop['nutritionstatus'],31,1) ?>"></td>          
<!--Physical Status -->         
					<td>Physical Status:<input type="text" name="physicalstatus" id="physicalstatus" readonly value= "<?php echo substr($row_anestpreop['physicalstatus'],31,1) ?>"></td>
<!--ASA Grading -->         
					<td>ASA Grading:<input type="text" name="asagrading" id="asagrading" readonly value= "<?php echo substr($row_anestpreop['asagrading'],31,1) ?>"></td>          
				</tr>
     		<tr>
        	<td height="18" align="right" scope="row">Last Oral Intake:</td>
                <td align="left" nowrap="nowrap">
              <input id="loiddt" name="loiddt" type="text" size="10" maxlength="30" readonly value="<?php echo  date('m-d-Y', $row_anestpreop['lastoralintake']) ;?>" />              <input id="loiddt_alt" name="loiddt_alt" type="text" size="6" maxlength="15" readonly value="<?php echo  date('h:i a', $row_anestpreop['lastoralintake']) ;?>" /></td>       
<!-- PreOp Anesthetic Orders --->
        	<td nowrap align="right" scope="row">Pre-Anesthetic Order</td>
        	<td colspan="5"><textarea name="preanestorders" id="preanestorders" class="surgdata" ><?php echo $row_anestpreop['preanestorders']?></textarea></td>
				</tr>
				<tr>
          <td>Entry By:<input type="text" name="entryby" Value = "<?php echo $row_anestpreop['entryby']; ?>"/></td>
          <td>Entry Date<input type="text" name="entrydt" Value = "<?php echo $row_anestpreop['entrydt']; ?>" /></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<!--******** INTRA OP *******************************************-->

<table width="50%">
	<tr>
		<td nowrap="nowrap">
			<p> <span style="color:white; font-size:24px;"> <?php echo str_repeat("&#9729;", 5);?></span><span class="BlueBold_24"> Anesthesia INTRAOP Update</span><span style="color:white; font-size:24px;"> <?php echo str_repeat("&#9729;", 5);?></span></p>
		</td>
	</tr>
</table>
<table width="50%"  border="0" cellpadding="0" cellspacing="0" >
	<tr>
		<td>
			<table border="0" align="left" cellpadding="1" cellspacing="1" bgcolor="#F8FDCE">
				<tr>
					<td align="left" nowrap="nowrap">
						<table border="1" align="left" cellpadding="1" cellspacing="1" bgcolor="#F8FDCE">
							<tr>
								<td nowrap>Presenting Patient condition:
                  <input type="text" name="PtCondition" id="PtCondition" value="<?php echo $row_anestintraop['PtCondition'] ?>" />
             		<td nowrap="nowrap">Local Anest:
								<td title="Estimated Blood Volume">EBV:
              		<input type="text" name="ebv" id="ebv" size='2' max='15' value="<?php echo $row_anestintraop['ebv'] ?>"/>ml </td>
            		<td height="18" align="right" scope="row">Intraopdt:</td>
            		<td align="left" nowrap="nowrap">
              		<input id="exscheddt" name="exscheddt" type="text" size="12" maxlength="12" value="<?php echo  date('D M d, Y', $row_anestintraop['intraopdt']) ;?>" />
              		<input id="exscheddt_alt" name="exscheddt_alt" type="text" size="8" maxlength="10" value="<?php echo  date('h:i a', $row_anestintraop['intraopdt']) ;?>" /></td>
          		</tr>
          		<tr>
            		<td title="Hemoglobin Level">HB Level:
              		<input type="text" name="hblevel" id="hblevel"  size='2' max='15' value="<?php echo $row_anestintraop['hblevel'] ?>"/></td>
            		<td nowrap="nowrap">Local Anest Position:
                  <input type="text" name="localanestposition" id="localanestposition" value="<?php echo $row_anestintraop['localanestposition'] ?>"/>
            		<td title="Maximum Allowable Blood Loss">MABL
 		              <input type="text" name="mabl" id="mabl"  size='2' max='15' value="<?php echo $row_anestintraop['mabl'] ?>"/>ml </td>
            		<td align="right" scope="row">aneststarttime:</td>
            		<td align="left" nowrap="nowrap">
									<input id="bascheddt" name="bascheddt" type="text" size="12" maxlength="15"  value="<?php echo  date('D M d, Y', $row_anestintraop['aneststarttime']) ;?>" />
              		<input id="bascheddt_alt" name="bascheddt_alt" type="text" size="8" maxlength="10" value="<?php echo  date('h:i a', $row_anestintraop['aneststarttime']) ;?>" />                </td>
      		
            	</tr>
          		<tr>
            		<td title="Blood Group">Bld Gp:
                  <input type="text" name="bldgp" id="bldgp" value="<?php echo $row_anestintraop['bldgp'] ?>"/></td>
            		<td nowrap="nowrap">Intubation:
                  <input type="text" name="intubation" id="intubation" value="<?php echo $row_anestintraop['intubation'] ?>"/></td>
           			<td title="Total Blood Loss">TBL:
              <input type="text" name="totalbldloss" id="totalbldloss" size='2' max='5' value="<?php echo $row_anestintraop['totalbldloss'] ?>"/>
              ml </td>

            <td align="right" scope="row">surgstarttime:</td>
            <td align="left" nowrap="nowrap">
            	<input id="bsscheddt" name="bsscheddt" type="text" size="12" maxlength="15"  value="<?php echo  date('D M d, Y', $row_anestintraop['surgstarttime']) ;?>" />
              <input id="bsscheddt_alt" name="bsscheddt_alt" type="text" size="8" maxlength="10" value="<?php echo  date('h:i a', $row_anestintraop['surgstarttime']) ;?>" />
              </td>
          </tr>
          <tr>
            <td nowrap="nowrap">Respiration:
              <input type="text" name="resp" id="resp" value="<?php echo $row_anestintraop['resp'] ?>"/></td>
            <td nowrap="nowrap">circuit:
              <input type="text" name="circuit" id="circuit" value="<?php echo $row_anestintraop['circuit'] ?>"/></td>
            <td title="Total Fluid Given">TFG:
              <input type="text" name="totalfluidgiven" id="totalfluidgiven"  size='2' max='5' value="<?php echo $row_anestintraop['totalfluidgiven'] ?>"/> ml </td>
            <td height="18" align="right">surgendtime:</td>
            <td align="left" nowrap="nowrap">
              <input id="esscheddt" name="esscheddt" type="text" size="12" maxlength="15" value="<?php echo  date('D M d, Y', $row_anestintraop['surgendtime']) ;?>" />
              <input id="esscheddt_alt" name="esscheddt_alt" type="text" size="8" maxlength="10" value="<?php echo  date('h:i a', $row_anestintraop['surgendtime']) ;?>" />
            </td>
          </tr>
          <tr>
            <td align="right" alig>Cannular Site: </td>
            <td nowrap><input type="text" name="cannularsite" id="cannularsite"  max='5' value="<?php echo $row_anestintraop['cannularsite'] ?>"/></td>
            <td nowrap>&nbsp;</td>
            <td height="18" align="right" scope="row">anestendtime:</td>
            <td align="left" nowrap="nowrap">
              <input id="eascheddt" name="eascheddt" type="text" size="12" maxlength="15" value="<?php echo  date('D M d, Y', $row_anestintraop['anestendtime']) ;?>" />
              <input id="eascheddt_alt" name="eascheddt_alt" type="text" size="8" maxlength="10" value="<?php echo  date('h:i a', $row_anestintraop['anestendtime']) ;?>" />
            </td>
          <tr>
            <td align="right">Anest Complications:</td>
            <td colspan='4'><textarea name="anestcomplications" id="anestcomplications" class="surgdata" ><?php echo $row_anestintraop['anestcomplications']?></textarea></td>
          </tr>
				  <tr>
  					<td align="right" nowrap title="Anest Id: <?php echo $row_anestintraop['aid'] ?>&#10;Surg Id: <?php echo $row_anestintraop['surgid'] ?>&#10;MRN: <?php echo $row_anestintraop['medrecnum'] ?>&#10;Visit Id: <?php echo $row_anestintraop['visitid'] ?>"> Status:
            <input type="text" name="status" id="status" value="<?php echo $row_anestintraop['status'] ?>"/></td>
            <td colspan="4" align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;              <input name="entryby" type="text" id="entryby" value="<?php echo $row_anestintraop['entryby'] ?>" />              <input name="entrydt" type="text" id="entrydt" value="<?php echo $row_anestintraop['entrydt'] ?>" /></td>
            </table>
      </td>
       
<!--  section to add, edit, delete drugs for surgery/anesthesia   section to add, edit, delete drugs for surgery/anesthesia   -->       
      <td valign="top">
        <table  cellpadding="0" cellspacing="0" bordercollapse="collapse">
          <tr>
            <td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td align="center" class="BlackBold_12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;drug&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td align="center" class="BlackBold_12">&nbsp;begindrug&nbsp;</td>
            <td align="center" class="BlackBold_12">&nbsp;&nbsp;&nbsp;enddrug&nbsp;&nbsp;&nbsp;</td>
            <td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          </tr>
    <?php if($totalRows_anestdrugs > 0) { ?>
          <?php do { ?>
            <tr>
              <td title="ID: <?php echo $row_anestdrugs['id']; ?>&#10;anest ID: <?php echo $row_anestdrugs['anestid']; ?>">&nbsp;</td>
              <td nowrap="nowrap"><input name="drug" size = "16" readonly value="<?php echo $row_anestdrugs['drug']; ?>" /></td>
              <td nowrap="nowrap" title="Date: <?php echo date('D, M d, Y',$row_anestdrugs['begindrug']); ?>"><input name="begindrug" size = "4" readonly value="<?php echo date('h:i A',$row_anestdrugs['begindrug']); ?>"  /></td>
          
              <td nowrap="nowrap"><input name="enddrug" size = "4" readonly value="<?php echo date('h:i A',$row_anestdrugs['enddrug']); ?>"/></td>
            </tr>
            <?php } while ($row_anestdrugs = mysql_fetch_assoc($anestdrugs)); ?>
        <?php }  ?>  <!--if > 0 drugs-->
          </table>
        </td>
      </tr>
    </table>
	</tr>
</table> 

<!-- Surgery/Anesthesia/Drug Chart   Surgery/Anesthesia/Drug Chart   Surgery/Anesthesia/Drug Chart   Surgery/Anesthesia/Drug Chart  -->
<?php if($row_anestintraop['aneststarttime'] == null) {
		    $aneststarttime = time(); 
     } else {
		    $aneststarttime = $row_anestintraop['aneststarttime'];	 
		 }?>
<?php if($row_anestintraop['surgstarttime'] == null) {
		    $surgstarttime = time(); 
     } else {
		    $surgstarttime = $row_anestintraop['surgstarttime'];	 
		 }?>

<?php if($row_anestintraop['anestendtime'] == null) {
		    $anestendtime = time(); 
     } else {
		    $anestendtime = $row_anestintraop['anestendtime'];	 
		 }?>
<?php if($row_anestintraop['surgendtime'] == null) {
		    $surgendtime = time(); 
     } else {
		    $surgendtime = $row_anestintraop['surgendtime'];	 
		 }?>

<table>
  <tr>
  	<td><div>
			<img src="../../Gantt.php? aid=<?php echo $row_anestintraop['aid'] ?>&ba=<?php echo $aneststarttime ?>&ea=<?php echo $anestendtime ?>&bs=<?php echo $surgstarttime ?>&es=<?php echo $surgendtime ?>" alt="ChartAnest">
		</div></td>
  </tr>
</table>

<!--POST OP *******************************************-->
<p><span style="color:white; font-size:24px;"> <?php echo str_repeat("&#9729;", 5);?></span> <span class="BlueBold_24"> Anesthesia POSTOP Update</span><span style="color:white; font-size:24px;"> <?php echo str_repeat("&#9729;", 5);?></span></p>
<table width="50%" border="1" bgcolor="#ffedcc" cellpadding="1" cellspacing="1">
  <tr>
      <td height="18" align="right" scope="row">postopexamdt:</td>
      <td  colspan="2" align="left" nowrap="nowrap">
        <input id="postopexamdt" name="postopexamdt" type="text" size="12" maxlength="12" value="<?php echo  date('D,d-M-Y', $row_anestpostop['postopexamdt']) ;?>" />
        <input id="postopexamdt_alt" name="postopexamdt_alt" type="text" size="8" maxlength="10" value="<?php echo  date('h:i a', $row_anestpostop['postopexamdt']) ;?>" /></td>
  </tr>
  <tr>
      <td align="right">Postop Complications:</td>
      <td colspan='4'><textarea name="postopcomplications" cols="80" rows="2" class="surgdata" id="postopcomplications" ><?php echo $row_anestpostop['postopcomplications']?></textarea></td>
  </tr>
  <tr>
    <td align="right">Other Findings:</td>
    <td colspan='4'><textarea name="otherfindings" cols="80" rows="2" class="surgdata" id="otherfindings" ><?php echo $row_anestpostop['otherfindings']?></textarea></td>
  </tr>
  <tr>
    <td nowrap="nowrap">status:
          <input type="text" name="status2" id="status2" value="$row_anestpostop['status']"/></td>
    <td align="right" title="Anest Id: <?php echo $row_anestpostop['aid'] ?>&#10;Surg Id: <?php echo $row_anestpostop['surgid'] ?>&#10;MRN: <?php echo $row_anestpostop['medrecnum'] ?>&#10;Visit Id: <?php echo $row_anestpostop['visitid'] ?>">*</td>
  
    
		<td colspan="2" align="right">EntryBy:
		  <input name="entryby" type="text" id="entryby" value="<?php echo $row_anestpostop['entryby'] ?>" />
		  EntryDate:
		  <input name="entrydt" type="text" id="entrydt" value="<?php echo $row_anestpostop['entrydt'] ?>" /></td>
	</tr>
</table>
<p class="BlueBold_24"> <?php echo str_repeat("&#9856;",80);?></p> 
<p class="BlueBold_24"> <?php echo str_repeat("&#9856;",80);?></p> 
 		<?php } while ($row_surg = mysql_fetch_assoc($surg)); ?>
		<?php } ?>

</body>
</html>
<?php
mysql_free_result($anestintraop);


mysql_free_result($anestdrugs);

?>


</body>
</html>
