<?php
session_start();

if(isset($_SESSION['Username'])){
	$number = $_POST['articleNum'];
	$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newssite');
	if($mysqli->connect_errno) {
		printf("Connection Failed: %s\n", $mysqli->connect_error);
		exit;
	}



	//check username of the file
	$stmt = $mysqli->prepare("select id, uploaded_by from userarticles where id=$number");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		 
		$stmt->execute();
		$stmt->bind_result($num, $name);
		$stmt->fetch();
		$stmt->close();
			
		if(trim($name) == $_SESSION['Username']){
				$sql = ("Delete from userarticles where id= $number");
				if(mysqli_query($mysqli, $sql)){
					
				}
				else{
					echo "Error deleting record: ".mysqli_error($mysqli);
				}

				$dc = ("Delete from comments where article_id= $number");
				if(mysqli_query($mysqli, $dc)){
					
				}
				else{
					echo "Error deleting record: ".mysqli_error($mysqli);
				}
				
		}
		else{
			header('location: ./index.php');
		}
		


	}

	
	header('location: ./index.php');
?>