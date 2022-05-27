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

<body	>
	<?php require_once 'nav.php'?>
	
<?php 
	if (isset($_POST["btnDispatch"]))
	{
	require_once 'db.php';
	$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	
	if ($mysqli->connect_errno)
	{
		die("Unable to connect to MySql:" .$mysqli->connect_errno);
	}
	$patrolcarDispatched = $_POST["chkPatrolcar"];
	$numOfPatrolcarDispatched = count($patrolcarDispatched);
	$incidentSatus;
	if ($numOfPatrolcarDispatched > 0)
	{
		$incidentStatus="2";
	} else {
		$incidentStatus="1";
	}
	$sql = "INSERT INTO incident (callerName, phoneNumber, incidentTypeId, incidentlocation, incidentDesc, incidentStatusId) VALUES (?, ?, ?, ?, ?, ?)";
	
	if (!($stmt = $mysqli->prepare($sql)))
	{
		die("Prepare failed: " .$mysqli->errno);
	}
	if (!$stmt->bind_param('ssssss', $_POST['callerName'],
						 			$_POST['contactNo'],
						 			$_POST['incidentType'],
						 			$_POST['location'],
						 			$_POST['incidentDesc'],
						 			$incidentStatus))
	{
		die("Binding parameters failed: ".$stmt->errno);
	}
	if (!$stmt->execute())
	{
		die("Insert incident table failed: ".$stmt->errno);
	}
	
	$incidentId=mysqli_insert_id($mysqli);
	for($i=0; $i < $numOfPatrolcarDispatched; $i++)
	{
		$sql="UPDATE patrolcar SET patrolcarStatusId ='1' WHERE patrolcarId = ?";
	
	if(!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
	if (!$stmt->bind_param('s', $patrolcarDispatched[$i]))
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
	if (!$stmt->execute())
	{
		die("Update patrolcar_status table failed: ".$stmt->errno);
	}
	$sql ="INSERT INTO dispatch (incidentId, patrolcarId, timeDispatched) VALUES (?, ?, NOW())";
	if (!($stmt = $mysqli->prepare($sql)))
	{
		die ("Prepare failed: " .$mysqli->errno);
	}
	if (!$stmt->bind_param('ss',$incidentId,$patrolcarDispatched[$i]))
	{
		die("Binding parameters failed: ".$stmt->errno);
	}
	if(!$stmt->execute())
	{
		die("Insert disatch table failed: ".$stmt->errno);
	}
	}
	}
	?>
	<fieldset>
	<legend>Dispatch Patrol Cars</legend>
	<form name="formdispatch" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
	<div>
		<table width="40%" border="1" align="center" cellpadding="4" cellspacing="4">
		<tr>
			<td colspan="2"><strong><center>Incident Detail</center></strong></td>
		</tr>
		<tr>
		<td>Caller's Name:</td>	
		<td><?php echo $_POST['callerName']?>
			<input type="hidden" name="callerName" id="callerName" value="<?php echo $_POST['callerName']?>"</td>
		</tr>
		<tr>
		<td>Contact Number:</td>
		<td><?php echo $_POST['contactNo']?><input type="hidden" name="contactNo" id="contactNo" value="<?php echo $_POST['contactNo']?>">
		</td>
		</tr>
		<tr>
		<td>Location:</td>
		<td><?php echo $_POST['location']?><input type="hidden" name="location" id="lcoation" value="<?php echo $_POST['location']?>"</td>
		</tr>
		<tr>
		<td>Incident Type:</td>
		<td><?php echo $_POST['incidentType']?><input type="hidden" name="incidentType" id="incidentType" value="<?php echo $_POST['incidentType']?>"></td>
		</tr>
		<tr>
		<td>Description:</td>
		<td><textarea name="incidentDesc" cols="45" rows="5" randomly id="incidentDesc"><?php echo $_POST['incidentDesc']?></textarea><input name="incidentDesc" type="hidden" id="incidentDesc" value="<?php echo $_POST['incidentDesc']?>"</td>
		</tr>
		</table>
		<?php
		require_once'db.php';
		$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
		if($mysqli->connect_errno){
			die("Failed to connect to MySQL: " .$mysqli->connect_errno);
		}
		$sql = "SELECT patrolcarId, statusDesc FROM patrolcar JOIN patrolcar_status ON patrolcar.patrolcarStatusId=patrolcar_status.StatusId WHERE patrolcar.patrolcarStatusId='2' OR patrolcar.patrolcarStatusId ='3'";
		if (!($stmt=$mysqli->prepare($sql)))
		{
			die("Prepare faied: " .$mysqli->errno);
		}
		if (!$stmt->execute())
		{
			die("Cannot run SQL comand: " .$stmt->errno);
		}
		if(!($resultset = $stmt->get_result()))
		{
			die("No data in resultset: ".$stmt->errno);
		}
		$patrolcarArray;
		while ($row = $resultset->fetch_assoc())
		{
			$patrolcarArray[$row['patrolcarId']] = $row['statusDesc'];
		}
		$stmt-> close();
		$resultset->close();
		$mysqli->close();
		?>
		
		<br><br>
		<table border="1" align="center" width="100%">
		<tr>
		<td colspan="3"><center><strong>DIspatch Patrolcar Panel</strong></center></td>
		</tr>
		<?php
			foreach($patrolcarArray as $key=>$value){
				?>
		<tr>
		<td align="center"><input type="checkbox" name="chkPatrolcar[]" value="<?php echo $key?>">
		</td>
		<td align="center"><?php echo $key ?></td>
		<td align="center"><?php echo $value?></td>
	
		</tr> <?php } ?>
		<tr>
		<td align="center"><input type="reset" name="btnCancel" id="btnCancel" value="Reset" class="boyubutton"></td>
		<td colspan="2" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="btnDispatch" value="Dispatch" class="boyubutton"></td>
		</tr>
		</table>
	</div>
	</form>
	</fieldset>
</body>
</html>