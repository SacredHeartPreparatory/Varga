<?php
	try {
		$db = new PDO('mysql:host=localhost;dbname=shp_subs','root', 'password');
    }
    catch (PDOException $e)
    {
    	print $e->getMessage();
    }
?>