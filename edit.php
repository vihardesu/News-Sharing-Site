<!DOCTYPE html>
<?php
	session_start();
?>
<html>
<head>
	<title>Edit</title>
</head>
<body>

<form method='POST' id='editArticle'>
<input type='text' name='editTitle' placeholder='edit title'>
<input type='text' name='editLink' placeholder='edit link'>
<textarea name="newDescription" form="editArticle"></textarea>
<input type='submit' name='submit' value='Edit'>
<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />

</form>
<?php
	if(isset($_SESSION['Username'])){

	$number = $_GET['numArticle'];

	$username = $_SESSION['Username'];

	$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newssite');
	$stmt = $mysqli->prepare("select uploaded_by from userarticles where id=".$number);
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$stmt->bind_result($user);
	$stmt->fetch();
	$stmt->close();
	if($mysqli->connect_errno) {
		printf("Connection Failed: %s\n", $mysqli->connect_error);
		exit;
	}
	if(trim($user) == $username){
		if(isset($_POST['submit'])){
			$editTitle = $mysqli->real_escape_string($_POST['editTitle']);
			$editLink = $mysqli->real_escape_string($_POST['editLink']);
			$descrip = $mysqli->real_escape_string($_POST['newDescription']);
			$sql = "UPDATE userarticles SET title='$editTitle', source='$editLink', description='$descrip' WHERE id=$number";

			if(mysqli_query($mysqli, $sql)){
				header('location: ./index.php');
			}
			else{
				echo "Error deleting record: ".mysqli_error($mysqli);
			}
		}
	}
}
?>

</body>
</html>

