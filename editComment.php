<!DOCTYPE html>
<?php
	session_start();
?>
<html>
<head>
	<title>Edit</title>
</head>
<body>

<form method='POST' id='editComment'>
<textarea name="newDescription" form="editComment"></textarea>
<input type='submit' name='submit' value='Edit'>
<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
</form>
<?php
	if(isset($_SESSION['Username'])){

	$number = $_GET['edit_comment_id'];

	$username = $_SESSION['Username'];

	$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newsSite');
	$stmt = $mysqli->prepare("select registered_user from comments where id=".$number);
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
			$descrip = $mysqli->real_escape_string($_POST['newDescription']);
			$sql = "UPDATE comments SET comment_content='$descrip' WHERE id=$number";

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

