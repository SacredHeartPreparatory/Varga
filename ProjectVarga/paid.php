<?php
	include "mysql_connect.php";
	include "html_formatting.php";
	
	if (isset($_POST['update']))
	{
		$statement = $db->prepare("UPDATE status SET paid = ?");
		$statement->execute(array($_POST['paid']));
		header("Location: admin.html"); 
		exit();
	}
	else
	{
		print_header("Toggle Paid Status");

		$result = $db->query("SELECT paid FROM status");
		$row = $result->fetch(PDO::FETCH_NUM);
		$yes = "";
		$no = "";
		if ($row[0]==1)
		{
			$yes = "SELECTED";
		}
		else
		{
			$no = "SELECTED";
		}
		print "<FORM ACTION='paid.php' METHOD='post'>";
		print "<INPUT TYPE='hidden' NAME='update' VALUE='y' />";
		print "Have all teachers completed the required number of subs, which means teachers now will get paid for additional subbing?";
		print "<SELECT NAME='paid'>";
		print "<OPTION VALUE='1' $yes>Yes</OPTION>";
		print "<OPTION VALUE='0' $no>No</OPTION>";
		print "</SELECT>";
		print "<INPUT TYPE='submit' VALUE='Save Changes' />";
		print "</FORM>";
	
	}


?>