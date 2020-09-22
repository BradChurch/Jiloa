<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>

<?php
mysql_select_db($database_swmisconn, $swmisconn);
$query_orders = "Select o.id ordid, o.medrecnum,  DATE_FORMAT(o.entrydt,'%d-%b-%Y %H:%i') entrydt, o.status, o.entryby, o.doctor, o.revby, o.revdt, o.comments, f.name,s.name spec, sc.collby, DATE_FORMAT(sc.colldt,'%d-%b-%Y %H:%i') colldt, p.gender, DATE_FORMAT(FROM_DAYS(DATEDIFF(CURRENT_DATE,p.dob)),'%y') AS age from orders o join fee f on o.feeid = f.id join patperm p on o.medrecnum = p.medrecnum join specimens s on f.specid= s.id left outer join speccollected sc on sc.ordnum = o.id where f.dept = 'Laboratory' and visitid = '".$_GET['vid']."'";
$orders = mysql_query($query_orders, $swmisconn) or die(mysql_error());
$row_orders = mysql_fetch_assoc($orders);
$totalRows_orders = mysql_num_rows($orders);

$query_fbs = "SELECT r.ordid, r.feeid, r.testid, r.result, r.normflag, DATE_FORMAT(r.entrydt,'%d-%m-%y') entrydt, DATE_FORMAT(r.entrydt,'%H:%i') entrytime, o.medrecnum, o.visitid FROM results r join orders o on o.id = r.ordid WHERE o.medrecnum = 13882 AND r.testid = 50 ORDER BY o.medrecnum DESC, r.entrydt ASC";
$fbs = mysql_query($query_fbs, $swmisconn) or die(mysql_error());
$row_fbs = mysql_fetch_assoc($fbs);
$totalRows_fbs = mysql_num_rows($fbs);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="1000px" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td colspan="2"> <!--1-->
      <table width="1000px" >
        <tr>
          <td width ="110px" colspan="2" nowrap="nowrap"> <a href="PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&visit=PatVisitView.php">Close</a>&nbsp;&nbsp;&nbsp;<a href="PatLabResPrt.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&visit=PatVisitView.php">Print</a></td>
          <td width ="340px" colspan="3" nowrap="nowrap"><h1 align="center">Laboratory Results page </h1></td>
          <td width ="550px">&nbsp;</td>
        </tr>
      </table>
      <table bgcolor="#FFFFFF">
        <tr>
        	<td title="FBS, date and time resulted&#10;(Day-Month-Year)&#10;For All Visits">
          	For<br />ALL<br />Visits
          </td>
        	<td title="FBS, date and time resulted&#10;(Day-Month-Year)&#10;For All Visits">
          	<span class="BlueBold_18">FBS</span>*<br />Date<br />Time
          </td>
		  <?php do { ?>
      	<td align="center">
        	<?php echo $row_fbs['result'] ?> <br />	<?php echo $row_fbs['entrydt']?> <br />	<?php echo $row_fbs['entrytime']?> 
        </td>
  
      <?php } while ($row_fbs = mysql_fetch_assoc($fbs)); ?>
  
        </tr>
      </table>
    </td><!--1-->
  </tr>
  <tr>
  	 <td colspan="2" >
	 	<table width="1000px">
	      <tr>
		    <td width ="30px">ord#</td>
		    <td width ="80px">Ordered*</td>
			<td width ="110px">Specimen*</td>
			<td width ="80px">Collected*</td>
			<td width ="150px">Order</td>
			<td width ="125px">Test</td>
			<td width ="125px">Result*</td>
			<td width ="30px">Flag</td>
			<td width ="70px">Normal</td>
			<td width ="50px">Units</td>
			<td width ="150px">Interpretation</td>
        </tr>
      </table>
    </td><!--1-->
  </tr>
  
  <tr>
  	<td><!--2-->
	  <table>
	     <tr>
		  <?php do { ?>
			<td valign="top"><!--2a-->
			  <table width="450">
					<tr>
						<td width="30px" ></td>
						<td width="80px" ></td>
						<td width="110px" ></td>
						<td width="80px" ></td>
						<td width="150px" ></td>
					</tr>
				<tr>
				  <td class="BlackBold_11"><?php echo $row_orders['ordid']; ?></td>
				  <td class="BlackBold_11" title=" Doctor: <?php echo $row_orders['doctor']; ?>&#10; EntryBy:<?php echo $row_orders['entryby']; ?>"><?php echo $row_orders['entrydt']; ?></td>
				  <td class="BlackBold_11" title="Collected: <?php echo $row_orders['colldt']; ?>&#10; Collected By: <?php echo $row_orders['collby']; ?>"><?php echo $row_orders['spec']; ?></td>
				  <td class="BlackBold_11" title="Collected By: <?php echo $row_orders['collby']; ?>"><?php echo $row_orders['colldt']; ?></td>
				  <td class="BlackBold_14" title="Reviewed By: <?php echo $row_orders['revby']; ?>&#10;Reviewed Date: &#10;<?php echo $row_orders['revdt']; ?>"><?php echo $row_orders['name']; ?><br/><span class="BlackBold_11"><?php echo $row_orders['status']; ?></span></td>
				</tr>
				<tr>
				  <td colspan="5" class="BlackBold_14">Comments: <?php echo $row_orders['comments']; ?></td>
				</tr>
			 </table>
		  </td><!--2a-->
	  <td><!--2b-->
		<table width="550">
			<tr>
				<td width="125px" ></td>
				<td width="125px" ></td>
				<td width="30px" ></td>
				<td width="50px" ></td>
				<td width="70px" ></td>
				<td width="150px" ></td>
			</tr>
		   <?php
				mysql_select_db($database_swmisconn, $swmisconn);
				$query_tests = "SELECT t.id, t.test, t.description, t.units, r.id rid, r.result, r.normflag, r.entryby, r.entrydt, n.normlow, n.normhigh ,n.paniclow, n.panichigh, n.interpretation FROM tests t join orders o on o.feeid in (t.feeid1, t.feeid2, t.feeid3, t.feeid4, t.feeid5, t.feeid6, t.feeid7, t.feeid8, t.feeid9, t.feeid0, t.feeidA, t.feeidB, t.feeidC, t.feeidD, t.feeidE, t.feeidF, t.feeidG, t.feeidH, t.feeidI, t.feeidJ, t.feeidK, t.feeidL, t.feeidM, t.feeidN) join results r on (o.id = r.ordid and o.feeid = r.feeid and t.id = r.testid) join testnormalvalues n on (n.testid = t.id and instr(gender,'".$row_orders['gender']."') > 0 AND '".$row_orders['age']."' >= agemin AND '".$row_orders['age']."' < agemax) WHERE t.active = 'Y' and o.id = '".$row_orders['ordid']."' and r.result != 'Not Tested' ORDER BY reportseq";
				$tests = mysql_query($query_tests, $swmisconn) or die(mysql_error());
				$row_tests = mysql_fetch_assoc($tests);
				$totalRows_tests = mysql_num_rows($tests);
		   
			do { ?>
			  <tr>
				<td nowrap="nowrap" class="BlackBold_11" title="Test ID: <?php echo $row_tests['id']; ?>&#10;Description: <?php echo $row_tests['description']; ?>"><?php echo $row_tests['test']; ?></td>
				<td nowrap="nowrap" class="BlackBold_14" title="Result ID: <?php echo $row_tests['rid']; ?>&#10;Entry By: <?php echo $row_tests['entryby']; ?>&#10;Entry Date: <?php echo $row_tests['entrydt']; ?>"><?php echo $row_tests['result']; ?></td>
				<td nowrap="nowrap" class="BlackBold_11" ><?php echo $row_tests['normflag']; ?></td>
				<td nowrap="nowrap" class="BlackBold_11" ><?php echo $row_tests['normlow']; ?> - <?php echo $row_tests['normhigh']; ?></td>
				<td nowrap="nowrap" class="BlackBold_11"><?php echo $row_tests['units']; ?></td>
				<td class="BlackBold_11"><?php echo $row_tests['interpretation']; ?></td>
			  </tr>
			<?php } while ($row_tests = mysql_fetch_assoc($tests)); ?>
			</table>
		  </td><!--2b-->
		</tr>
         <?php } while ($row_orders = mysql_fetch_assoc($orders)); ?>
	  </table>
	</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($orders);

mysql_free_result($tests);
?>
