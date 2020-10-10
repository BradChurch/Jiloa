<?php  $pt = "Reports Menu"; ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/Len/Jiloa/Master/Header.php'); ?> 
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>
<?php $_SESSION['CurrDateTime'] = date("Y-m-d  H:i"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p>&nbsp;</p>
<p align="center" class="BlueBold_24">Patient Lists </p>
<table width="50%" align="center">
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php if(allow(109,1)==1){?>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="PatientList.php">All Patients List  </a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php }?>
<?php if(allow(110,1)==1){?>  
<tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="Dischargable.php">Dischargable Now</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php }?>
<?php if(allow(111,1)==1){?> 
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="AutoDischarged.php">Autodischarged Visits </a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php }?>
<?php if(allow(112,1)==1){?> 
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><!--<a href="CashierNotPaid.php"></a>-->
    <a href="SystemDataProblemsPatient.php">System Data Problems - Patients </a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php }?>
<?php if(allow(113,1)==1){?> 
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="MergeView.php">Patient MRN compare </a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php }?>
<?php if(allow(114,1)==1){?> 
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="VisitData.php">Visit Data</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php }?>
<?php if(allow(115,1)==1){?> 
    <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><!--<a href="cashier.php"></a>-->
    <a href="PatOrders.php">Orders</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php }?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php if(allow(116,1)==1){?> 
 <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="../Validate/ValidationMenu.php">Validation Reports  </a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php }?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><!--<a href="SurgeryReports.php">Surgery Reports</a>--></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="CommunityStats.php">CommunityStats</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
