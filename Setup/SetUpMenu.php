<?php  $pt = "Setup Menu"; ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/Len/Jiloa/Master/Header.php'); ?> 
<?php //require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>
<?php require_once('../../Connections/swmisconn.php'); ?>
<?php $_SESSION['CurrDateTime'] = date("Y-m-d  H:i"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Bed Setup</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p>&nbsp;</p>
<p align="center" class="BlueBold_24">SETUP</p>
<table width="50%" align="center">
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="MiscBilling.php">MISC BILLING</a> </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="FeeSchedule.php">Fee Schedule </a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="Specimens.php">Specimens</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="DrpDwnList.php">DropDownLists</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="LabTests.php">Lab Tests</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="LabTestNorms.php">LabTest Normal Values</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="NoticesList.php">Patient Notices</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="Exam_ExamView.php">Exam Setup </a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="BedAdd.php">Bed Setup</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>

  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="AnestDrugView.php">Anesthetic Drug Setup</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php if($_SESSION['user'] == 'GRACE ADMIN' || 'L.GABRIELSE'){?>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="../../Development/DevProjectView.php">Development Tracking</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php }?>
<?php if($_SESSION['user'] == 'GRACE ADMIN' || 'L.GABRIELSE'){?>

  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="token_out_report">Token Out Report</a></td>
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
    <td bgcolor="#FFFFFF" class="BlueBold_14">Address: <a href="AddrCountryView.php">Country</a> - <a href="AddrStateView.php">State</a> - <a href="AddrLocGovtView.php">LocGovt</a> - <a href="AddrCityView.php">City</a></td>
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
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="../Backup/AutoBackup.php">Backup - Automated </a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="../Backup/AdminBackup.php">Backup - Export (Manual) </a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="BlueBold_14"><a href="../Backup/AdminRestore.php">Restore - Manual </a></td>
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
