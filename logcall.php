<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
	<style>
		body{
			background-image:url('background_project.png');
			background-repeat: no-repeat;
			background-size: cover;
		}
		table{
			color:antiquewhite;
			background color:antiquewhite;
		}
		td{
			color:black;
			background-color: antiquewhite;
		}
		tr{
			color:black;
		}
		form{
			color:white;
		}
		
	</style>
</head>
<body>
<script>
	function aqil()
	{
		var caNa=document.forms["frmLogCall"]["callerName"].value;
		var coNo=document.forms["frmLogCall"]["contactNo"].value;
		var lo=document.forms["frmLogCall"]["location"].value;
		var inDe=document.forms["frmLogCall"]["incidentDesc"].value;
	// contact name
	if (caNa== null || caNa=="")
	{
		alert("Caller Name is required.");
		return false;
	}
	// Number
	if (coNo== null || coNo=="")
	{
		alert("Contact Number is required.");
		return false;
	}
	// location
	if (lo== null || lo=="")
	{
		alert("Location is required.");
		return false;
	}
	//Desc
	if (inDe== null || inDe=="")
	{
		alert("Description is required.");
		return false;
	}
	
	}
</script>
<?php require_once 'nav.php';
?>
<?php require_once 'db.php';
$mysqli = mysqli_connect(DB_SERVER, DB_USER,DB_PASSWORD,DB_DATABASE);
if ($mysqli->connect_errno){
	die("Unable to connect to Database: ".$mysqli->connect_errno);
}
$sql = "SELECT * FROM incidenttype";
if(!($stmt=$mysqli->prepare($sql)))
{
	die("Command error: ".$mysqli->errno);
}
if(!$stmt->execute())
{
	die("Cannot run SQL command: ".$stmt->errno);
}
if (!($resultset=$stmt->get_result()))
{
	die("No data in resultset: ".$stmt->errno);
}
$incidentType;
while($row=$resultset->fetch_assoc()){
	$incidentType[$row['incidentTypeId']]=$row['incidentTypeDesc'];
}
$stmt->close();
$resultset->close();
$mysqli->close();
?>
<fieldset>
<legend style="color:white;">Log Call</legend>
<form name="frmLogCall" method="post" action="dispatch.php" onSubmit="return aqil()">
	<table width="45%" border="2" align="center" cellpadding="5" cellspacing="5">
	<tr>
	<td width="20%" align="center">Name of Caller:</td>
	<td width="50%"><input type="text" name="callerName" id="callerName" pattern="[A-Za-z]+"></td>
	</tr>
	<tr>
	<td width="20%" align="center">Contact Number</td>
	<td width="50%"><input type="text" name="contactNo" id="contactNo" maxlength="8" minlength="8"  pattern="[6,8,9]{1}[0-9]{7}"></td>
	</tr>
	<tr>
		<td width="50%" align="center">Location:</td>
	<td widht="50%"><input type="text" name="location" id="location"></td>
	</tr>
	<tr>
	<td width="50%" align="center">Incident Type:</td>	
	<td width="50%"><select name="incidentType" id="incidentType">
		<?php foreach($incidentType as $key=> $value) {?>
		<option value="<?php echo $key ?> " ><?php echo $value ?></option>
		<?php } ?>
		</select>
		</td>
	</tr>
	<tr>
		<td width="50%" align="center">Description:</td>
	<td width="50%">
		<textarea name="incidentDesc" id="incidentDesc" cols="45" rows="5"></textarea></td>
	</tr>
	<tr>
		<table width="40%" border="0" align="center" cellpadding="5" cellspacing="5"><td align="center"><input type="reset" name="cancelProcess" id="cancelProcess" value="Reset"></td>
		<td align="center"><input type="submit" name="btnProcessCall" id="btnProcessCall" value="Process Call"</td>
		</table>
	</tr>
	</table>
	</form>
</fieldset>
</body>
</html>
