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
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Community Stats</title>
</head>

<body>
<table>
  <tr>
    <td><a href="ReportsMenu.php" title='ReportsMenu.php'>Menu</a></td>
  </tr>
</table>


<table>
	<tr>
		<td align="center">From BMC MIS <br>
	  Database</td>  
		<td align="center">From BMC MIS <br>
	  Database</td>  
		<td align="center">LG = local government<br>
	  % is of all patients</td>  
		<td align="center">LG = local government<br>
% is of all visits</td>  
		<td align="center">3 cities are Luga,<br>
	  Mbataiv, and Mbataan<br>
	  % is of Gboko LG patients</td>  
		<td align="center">3 cities are Luga,<br>
Mbataiv, and Mbataan<br>
% is of Gboko LG visits</td>  
  </tr>
  <tr>
    <td valign="top">
<!--*********************************************** All PATIENTS *****************************************-->
      <table border="1">
        <tr>
          <td nowrap="nowrap">All Patients</td>
        </tr>	
    <?php $allpats = array();
					$totpatall=0; 
			//loop 2015 to 2020
 			 		for ($x = 2015; $x <= 2020; $x++)  {
			// query to count number of patients per year          
          mysql_select_db($database_swmisconn, $swmisconn);
          $query_patients = "SELECT count(distinct pv.medrecnum) patients FROM patvisit pv join patinfo pi on pv.medrecnum = pi.medrecnum WHERE visitdate like '".$x."%'";
          $patients = mysql_query($query_patients, $swmisconn) or die(mysql_error());
          $row_patients = mysql_fetch_assoc($patients);
          $totalRows_patients = mysql_num_rows($patients);
          ?>
        <tr>
          <td>	
      <?php 
			// display year and # of patients/year
          echo $x.' - ';
          echo $row_patients['patients'].'<br>';
			// accumulate number of patients into a total
					$totpatall = $totpatall + $row_patients['patients'];
					$allpats[$x] = $row_patients['patients'];
          ?>
          </td>
        </tr>
      <?php } ?>
			<!--// Display total-->
      	<tr>
        	<td>Total:<?php echo $totpatall ?></td>
        </tr>

<?php  // query to find last medrecnum
		      mysql_select_db($database_swmisconn, $swmisconn);
          $query_lastmrn = "SELECT MAX(medrecnum) mrns FROM patperm";
          $lastmrn = mysql_query($query_lastmrn, $swmisconn) or die(mysql_error());
          $row_lastmrn = mysql_fetch_assoc($lastmrn);
          $totalRows_lastmrn = mysql_num_rows($lastmrn);      
?>        
			<!--// Display last medrecnum and tooltip-->
				<tr>
        	<td nowrap="nowrap" title="Last MRN may be lower than MRNs for visits because&#10;a patient may be included in more than one year. ">Last MRN - <?php echo $row_lastmrn['mrns'] ?>*</td>
        </tr>

      </table>
    </td>
    
<!--*********************************************** All VISITS *****************************************-->
    
    <td valign="top">
      <table border="1">
        <tr>
          <td>All visits</td>
        </tr>	
   <?php  $allvisits = array();
					$totvisitall=0;
			//loop 2015 to 2020
 			 		for ($x = 2015; $x <= 2020; $x++)  {
			// query to count number of visits per year                   
          mysql_select_db($database_swmisconn, $swmisconn);
          $query_visits = "SELECT count(pv.id) visits FROM patvisit pv join patinfo pi on pv.medrecnum = pi.medrecnum WHERE visitdate like '".$x."%'";
          $visits = mysql_query($query_visits, $swmisconn) or die(mysql_error());
          $row_visits = mysql_fetch_assoc($visits);
          $totalRows_visits = mysql_num_rows($visits);
          ?>
        <tr>
          <td>	
      <?php 
			// display year and # of visits/year
          echo $x.' - ';
          echo $row_visits['visits'].'<br>';
			// accumulate number of visits into a total
					$totvisitall = $totvisitall + $row_visits['visits'];
					$allvisits[$x] = $row_visits['visits'];
          ?>
          </td>
        </tr>
      <?php } ?>
			<!--// Display total-->
      	<tr>
        	<td>Total:<?php echo $totvisitall ?></td>
        </tr>
<?php   // query to find last visit id
			    mysql_select_db($database_swmisconn, $swmisconn);
          $query_lastvisitid = "SELECT MAX(id) visits FROM patvisit";
          $lastvisitid = mysql_query($query_lastvisitid, $swmisconn) or die(mysql_error());
          $row_lastvisitid = mysql_fetch_assoc($lastvisitid);
          $totalRows_lastvisitid = mysql_num_rows($lastvisitid);      
?>        
			<!--// Display last medrecnum and tooltip-->
				<tr>
        	<td nowrap="nowrap" title="Last Visit id may be higher than total visits&#10;because not all patients have info record or &#10;id may be from before 2015. &#10;System started on Sept 20, 2014 ">Last Visit ID - <?php echo $row_lastvisitid['visits'] ?>*</td>
        </tr>
      </table>
    </td>

<!--*********************************************** All Gboko LG PATIENTS *****************************************-->

    <td valign="top">
      <table border="1">
        <tr>
          <td nowrap="nowrap">Gboko LG Patients</td>
        </tr>	
      <?php
					$gbokopats = array();
			 		for ($x = 2015; $x <= 2020; $x++)  {
          
          mysql_select_db($database_swmisconn, $swmisconn);
          $query_patients = "SELECT count(distinct pv.medrecnum) patients FROM patvisit pv join patinfo pi on pv.medrecnum = pi.medrecnum WHERE pi.locgovt = 1 and visitdate like '".$x."%'";
          $patients = mysql_query($query_patients, $swmisconn) or die(mysql_error());
          $row_patients = mysql_fetch_assoc($patients);
          $totalRows_patients = mysql_num_rows($patients);
          ?>
        <tr>
          <td>	
      <?php 
          echo $x.' - ';
          echo $row_patients['patients'];
					$gbokopats[$x] = $row_patients['patients'];
					echo ' = ';
					echo ROUND(($gbokopats[$x]/$allpats[$x])*100,2);
					echo ' %';
          ?>
          </td>
        </tr>
      <?php } ?>
			<?php //foreach($gbokopats as $x => $x_value) {?>
<!--      	<tr>
        	<td><?php //echo "Key=" . $x . ", Value=" . $x_value;?></td>
        </tr>
-->      <?php // } ?>        
      </table>
    </td>

<!--*********************************************** All Gboko LG VISITS *****************************************-->

    <td valign="top">
      <table border="1">
        <tr>
          <td>Gboko LG visits</td>
        </tr>	
      <?php $gbokovisits = array();
			 		for ($x = 2015; $x <= 2020; $x++)  {
          
          mysql_select_db($database_swmisconn, $swmisconn);
          $query_visits = "SELECT count(pv.id) visits FROM patvisit pv join patinfo pi on pv.medrecnum = pi.medrecnum WHERE pi.locgovt = 1 and visitdate like '".$x."%'";
          $visits = mysql_query($query_visits, $swmisconn) or die(mysql_error());
          $row_visits = mysql_fetch_assoc($visits);
          $totalRows_visits = mysql_num_rows($visits);
          ?>
        <tr>
          <td>	
      <?php 
          echo $x.' - ';
          echo $row_visits['visits'];
					$gbokovisits[$x] = $row_visits['visits'];
					echo ' = ';
					echo ROUND(($gbokovisits[$x]/$allvisits[$x])*100,2);
					echo ' %';
          ?>
          </td>
        </tr>
      <?php } ?>
			<?php //foreach($gbokovisits as $x => $x_value) {?>
<!--      	<tr>
        	<td><?php //echo "Key=" . $x . ", Value=" . $x_value;?></td>
        </tr>
 -->     <?php //} ?>        
      </table>
    </td>
    
    
<!--*********************************************** 3 Gboko cities PATIENTS *****************************************-->

    <td valign="top">
      <table border="1">
        <tr>
          <td nowrap="nowrap">Gboko 3 Cities Patients</td>
        </tr>	
      <?php
					$gboko3cpats = array();
			 		for ($x = 2015; $x <= 2020; $x++)  {
          
          mysql_select_db($database_swmisconn, $swmisconn);
          $query_patients = "SELECT count(distinct pv.medrecnum) patients FROM patvisit pv join patinfo pi on pv.medrecnum = pi.medrecnum WHERE pi.city IN(294,620,1829) and visitdate like '".$x."%'";
          $patients = mysql_query($query_patients, $swmisconn) or die(mysql_error());
          $row_patients = mysql_fetch_assoc($patients);
          $totalRows_patients = mysql_num_rows($patients);
          ?>
        <tr>
          <td nowrap="nowrap">	
      <?php 
          echo $x.' - ';
          echo $row_patients['patients'];
					$gboko3cpats[$x] = $row_patients['patients'];
					echo ' = ';
					echo ROUND(($gboko3cpats[$x]/$gbokovisits[$x])*100,2);
					echo ' %';
          ?>
          </td>
        </tr>
      <?php } ?>
			<?php //foreach($gboko3cpats as $x => $x_value) {?>
<!--      	<tr>
        	<td><?php //echo "Key=" . $x . ", Value=" . $x_value;?></td>
        </tr>
 -->     <?php //} ?>        
      </table>
    </td>

<!--***********************************************  3 Gboko cities VISITS *****************************************-->

    <td valign="top">
      <table border="1">
        <tr>
          <td>Gboko 3 Cities Visits</td>
        </tr>	
      <?php $gboko3cvisits = array();
			 		for ($x = 2015; $x <= 2020; $x++)  {
          
          mysql_select_db($database_swmisconn, $swmisconn);
          $query_visits = "SELECT count(pv.id) visits FROM patvisit pv join patinfo pi on pv.medrecnum = pi.medrecnum WHERE pi.city IN(294,620,1829) and visitdate like '".$x."%'";
          $visits = mysql_query($query_visits, $swmisconn) or die(mysql_error());
          $row_visits = mysql_fetch_assoc($visits);
          $totalRows_visits = mysql_num_rows($visits);
          ?>
        <tr>
          <td nowrap="nowrap">	
      <?php 
          echo $x.' - ';
          echo $row_visits['visits'];
					$gboko3cvisits[$x] = $row_visits['visits'];
					echo ' = ';
					echo ROUND(($gboko3cvisits[$x]/$gbokovisits[$x])*100,2);
					echo ' %';
          ?>
          </td>
        </tr>
      <?php } ?>
			<?php //foreach($gboko3cvisits as $x => $x_value) {?>
<!--      	<tr>
        	<td><?php //echo "Key=" . $x . ", Value=" . $x_value;?></td>
        </tr>
-->      <?php //} ?>        
      </table>
    </td>
    
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($visits);
?>
