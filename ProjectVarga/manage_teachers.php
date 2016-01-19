<?php
	include "mysql_connect.php";
	include "html_formatting.php";
	
	print_header("Manage Teachers");
	
	$periods = str_split("ABCDEFG");

	if (array_key_exists("EditButton", $_POST))
	{
		$fname = $_POST['first'];
		$lname = $_POST['last'];
		$email = $_POST['email'];
		$idnum = $_POST['idnum'];
		$params = array($fname, $lname, $email, $idnum);
		
		$statement = $db->prepare("UPDATE teachers SET first_name=?, last_name=?, email=? where id_num = ?");
		$statement->execute($params);	
	}
	elseif (array_key_exists("AddButton", $_POST))
	{
		$fname = $_POST['first'];
		$lname = $_POST['last'];
		$email = $_POST['email'];
		$params = array($fname, $lname, $email);
		
		$statement = $db->prepare("INSERT into teachers (first_name, last_name, email) VALUES (?, ?, ?)");
		$statement->execute($params);	
	}
	elseif (array_key_exists("DeleteButton", $_POST))
	{
		$idnum = $_POST['idnum'];
		$params = array($idnum);
		
		$statement = $db->prepare("DELETE from teachers where id_num = ?");
		$statement->execute($params);	
	}
	elseif (array_key_exists("ClassButton", $_POST))
	{
		$idnum = $_POST['idnum'];
		$params = array($idnum);
		$statement = $db->prepare("DELETE from classes where teacher_id = ?");
		$statement->execute($params);
		foreach ($periods as $period)
		{
			if (strlen($_POST["class_$period"])>1 && strlen($_POST["room_$period"])>1)
			{
				$name = $_POST["class_$period"];
				$room = $_POST["room_$period"];
				$params = array($period, $name, $room, $idnum);
				$statement = $db->prepare("INSERT into classes (period, name, room, teacher_id) VALUES(?, ?, ?, ?)");
				$statement->execute($params);
			}
		}	
	}
	
	// Show form for adding a new teacher
	print "<P><FORM METHOD='post' ACTION='manage_teachers.php'>";
	print "First: <INPUT TYPE='text' NAME='first'  />";
	print "Last: <INPUT TYPE='text' NAME='last'  />";
	print "Email: <INPUT TYPE='text' NAME='email'  />";
	print "<INPUT TYPE='submit' VALUE='Add' NAME='AddButton'/>";
	print "</FORM>";
	
	print "<HR>";
	
	// show forms for all teachers
	$statement = $db->prepare("SELECT first_name, last_name, id_num, email from teachers order by last_name, first_name");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_NUM);
	foreach ($result as $row)
	{
		$fname = $row[0];
		$lname = $row[1];
		$idnum = $row[2];
		$email = $row[3];
		print "<P>";
		print "<FORM METHOD='post' ACTION='manage_teachers.php'>";
		print "First: <INPUT TYPE='text' NAME='first' VALUE='$fname' />";
		print "Last: <INPUT TYPE='text' NAME='last' VALUE='$lname' />";
		print "Email: <INPUT TYPE='text' NAME='email' VALUE='$email' />";
		print "<INPUT TYPE='hidden' NAME='idnum' VALUE='$idnum' />";
		print "<INPUT TYPE='submit' VALUE='Edit' NAME='EditButton'/>";		
		print "<INPUT TYPE='submit' VALUE='Delete' NAME='DeleteButton'/>";
		print "</FORM>";
		
		$params = array($idnum);
		$names = array();
		$rooms = array();
		$statement = $db->prepare("SELECT period, name, room from classes where teacher_id = ?");
		$statement->execute($params);
		$result_classes = $statement->fetchAll(PDO::FETCH_NUM);
		foreach($result_classes as $row_classes)
		{
		      	$period = $row_classes[0];
				$name = $row_classes[1];
				$room = $row_classes[2];
				$names[$period] = $name;
				$rooms[$period] = $room;
		}
		print "<FORM METHOD='post' ACTION='manage_teachers.php'>";
		print "<INPUT TYPE='hidden' NAME='idnum' VALUE='$idnum' />";
		print "Class/Room  ";
		foreach ($periods as $period)
		{
			print " $period:";
			print "<INPUT TYPE='text' NAME='class_$period' SIZE='8' VALUE='$names[$period]' />";
			print "/";
			print "<INPUT TYPE='text' NAME='room_$period' SIZE='6' VALUE='$rooms[$period]' />";
			if ($period == "D") {print "<BR>";}
		}
			
		print "<INPUT TYPE='submit' VALUE='Update Classes for $fname $lname' NAME='ClassButton'/>";
		print "</FORM>";
		print "</P>";
		print "<HR>";
	}
?>