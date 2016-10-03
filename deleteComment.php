<?php
session_start();

if(isset($_SESSION['Username'])){
	$number = $_POST['comment_id'];
	echo $number;
	$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newsSite');
	if($mysqli->connect_errno) {
		printf("Connection Failed: %s\n", $mysqli->connect_error);
		exit;
	}
	//check username of the file
	$stmt = $mysqli->prepare("select id, registered_user, article_id from comments where id=$number");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		 
		$stmt->execute();
		$stmt->bind_result($num, $name, $article_id);
		$stmt->fetch();
		$stmt->close();
			
		if(trim($name) == $_SESSION['Username']){
				$sql = ("Delete from comments where id= $number");
				if(mysqli_query($mysqli, $sql)){
					
				}
				else{
					echo "Error deleting record: ".mysqli_error($mysqli);
				}
			}
		else{
			header('location: stories.php?viewStoryId=$article_id');
		}
		

	}

	
	header("location: ./index.php");