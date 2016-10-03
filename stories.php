<!DOCTYPE html>
<?php
	session_start();
	if(isset($_SESSION['Username'])){
	$username = $_SESSION['Username'];
	}	
	

?>
<html>
<head>
	<title>User Story</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
	integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" 
	crossorigin="anonymous">
	<link rel="stylesheet" href="storiesStyle.css">
</head>
<body>
<?php
	$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newssite');
		if($mysqli->connect_errno) {
			printf("Connection Failed: %s\n", $mysqli->connect_error);
			exit;
		}

	$articlenum = $_GET['viewStoryId'];
	$stmt = $mysqli->prepare("select id, title, uploaded_by, source, description from userarticles where id=$articlenum");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	 
	$stmt->execute();
	 
	$stmt->bind_result($id, $title, $uploaded_by, $source, $description);
	$stmt->fetch();
	//echo "$id <br> $title <br> $uploaded_by <br> $source <br> $description";

?>
<header id='artTitle'>

<?php
	echo "<span id='headerTitle'>$title</span>";
	if(isset($_SESSION['Username'])){
		$Username = $_SESSION['Username'];
		echo "<span id='loggedAs'> Logged in as, $Username </span>";
		echo "<div class='login' id='logout'>
				<form method='POST' action='./logoutpage.php'>
					<input type='submit' name='logout' value='Logout' class='btn btn-danger btn-lg'>
					<input type='hidden' name='token' value=".$_SESSION['token']."/>
				</form>
			</div>";
	}
	else{
		echo "<div class='login' id='login'>
				<form method='POST' action='./loginpage.php'>
					<input type='submit' name='login' value='Login/Register' class='btn btn-primary btn-lg'>
					<input type='hidden' name='token' value=".$_SESSION['token']."/>
				</form>
			</div>";
	}
?>
</header>
<div id='container'>
	<div id='details'>
		<div id='author'>
		<?php
			echo "Posted by: ".$uploaded_by;
		?>
		</div>

		<div id='link'>
			<a href="<?php echo $source?>">
				<?php
					echo "$source";
				?>
			</a>
		</div>
		<div id='description'>
		<?php
			echo $description;
		?>
		</div>

	</div>
	<br><br><br><br><br><br>
	<div id='comments'>
		<span id='commentshead'>Comments</span>
		<?php
		if(isset($_SESSION['Username'])){
		echo "
			<form method='POST' id='writeComment'>
			<textarea name='comment' form='writeComment'></textarea>
			<input type='submit' name='submitComment' value='Comment'>
			<input type='hidden' name='token' value=".$_SESSION['token']."/>
			</form>";
		}
		if(isset($_POST['submitComment'])){
			$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newssite');
			if($mysqli->connect_errno) {
				printf("Connection Failed: %s\n", $mysqli->connect_error);
				exit;
			}
			$stmt = $mysqli->prepare("insert into comments (article_id, registered_user, comment_content) values (?, ?, ?)");
			if(!$stmt){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			 
			$stmt->bind_param('sss',$articlenum, $username, $_POST['comment']);
			$stmt->execute();
			$stmt->close();
			echo "Successfully posted comment";
		}
		?>
	</div>
	<table id='allComments'>
	<tr>
		<th>User</th>
		<th>Comment</th>
	</tr>
	<?php
		$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newssite');
		if($mysqli->connect_errno) {
			printf("Connection Failed: %s\n", $mysqli->connect_error);
			exit;
		}
		$stmt = $mysqli->prepare("select id, registered_user, comment_content from comments where article_id=$articlenum");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->execute();
		$stmt->bind_result($comment_id, $registered_user, $comment_content);
		while($stmt->fetch()){
			echo "<tr>
				<div class='userofcomment'>
					<td class='tds'>$registered_user</td>
				</div>
				<div class='commenttext'>
					<td class'tds'>$comment_content</td>
				</div>";
				if(isset($_SESSION['Username']) && $_SESSION['Username'] == $registered_user){
					echo "
				<td>
					<form method='POST' action='./deleteComment.php'>
					<input type='hidden' name='comment_id' value=".$comment_id.">
					<input type='submit' name='deleteArticle' value='Delete' class='btn btn-danger'>
					<input type='hidden' name='token' value=".$_SESSION['token']."/>
					</form>
				</td>
				<td>
					<form method='GET' action='./editComment.php'>
					<input type='hidden' name='edit_comment_id' value=".$comment_id.">
					<input type='submit' name='editComment' value='Edit' class='btn btn-primary'>
					
					</form>
				</td>
				</tr>";
				}
		}


	?>
	</table>
	<div id='return'>
	<form action='index.php' method='POST'>
	<input type='submit' name='return' value='Return To Homescreen' class='btn btn-success'>
	<input type='hidden' name='token' value=".$_SESSION['token']."/>
	</form>
	</div>
</div>
</body>
</html>