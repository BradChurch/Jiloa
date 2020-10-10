
<!-- POCT link sends mrn and vid-->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>POC Select</title>
</head>

<body>

<h1 title="Point Of Care Testing">POCT - Select    <!-- Feeid: Hemoglobin 51, FBS fasting 66, RBS Random 48, RVST 183, Urine Dipstick 11  466,--> <a href="../../InstructionDocs/DemonstratingPointOfCare(POC)Labs.htm" target="_blank">+++</a> </a></h1>
<table>
	<tr>
    <td><div align="center">
      <input type="button" name="button" class="btngradblu150" value="Hemoglobin" onclick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&feeid=51&name=Hemoglobin&visit=PatVisitView.php&act=poc&pge=POCREadd.php'" />
    </div></td>
	</tr>

	<tr>
    <td><div align="center">
      <input type="button" name="button" class="btngradblu150" value=" FBS (RDT) " onclick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&feeid=66&name=FBS (RDT)&visit=PatVisitView.php&act=poc&pge=POCREadd.php'" />
    </div></td>
	</tr>
	<tr>
    <td><div align="center">
      <input type="button" name="button" class="btngradblu150" value=" RBS (RDT) " onclick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&feeid=48&name=RBS (RDT)&visit=PatVisitView.php&act=poc&pge=POCREadd.php'" />
    </div></td>
	</tr>
	<tr>
    <td><div align="center">
      <input type="button" name="button" class="btngradblu150" value=" RVST Screening " onclick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&feeid=183&name=RVST Screening&visit=PatVisitView.php&act=poc&pge=POCREadd.php'" />
    </div></td>
	</tr>
	<tr>
    <td><div align="center">
      <input type="button" name="button" class="btngradblu150" value=" Urine Dip stick 11 " onclick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&feeid=466&name=FBS&visit=PatVisitView.php&act=poc&pge=POCREadd.php'" />
    </div></td>
	</tr>
</table>
</body>
</html>