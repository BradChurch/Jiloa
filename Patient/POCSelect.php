

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>POC Select</title>
</head>

<body>

<h1>POC Select </h1>
<table>
	<tr>
    <td><div align="center">
      <input type="button" name="button" class="btngradblu50" value=" FBS (RDT) " onclick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&feeid=66&name=FBS&visit=PatVisitView.php&act=poc&pge=POCREadd.php'" />
    </div></td>
	</tr>
	<tr>
    <td><div align="center">
      <input type="button" name="button" class="btngradblu50" value=" Urine Dip stick 11 " onclick="parent.location='PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&feeid=466&name=FBS&visit=PatVisitView.php&act=poc&pge=POCREadd.php'" />
    </div></td>
	</tr>
</table>
</body>
</html>