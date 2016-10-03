<!DOCTYPE html>
<html>
<head>
	<title>Upload a Story</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
	integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" 
	crossorigin="anonymous">
	<link rel="stylesheet" href="uploadStyle.css">
</head>
</head>
<body>
<?php
session_start();
?>
<div id='container'>
<form method='POST' id="uploadStory">
	<div class="inputFields" id="title">
		<span class='pretext'>Title</span>
	<input type="text" name="storyTitle" placeholder="Title">
	</div>

	<br><br>
	
	<div class="inputFields" id="description">
	<span class='pretext'>Description</span>
	<span id='blankspace'></span>
	<textarea name="description" form="uploadStory"></textarea>
	</div>
	<br><br><br>
	<div id='uploadFile'>
	<span class='pretext'>Insert a link here</span>
	<input type="text" name="storyLink" placeholder="Link here">
	</div>

	<input type="submit" name="submit" value="submit">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />

</form>
<div id='pageTitle'>
<h1>
Upload Your Story Here
</h1>
</div>



<?php
	$username = $_SESSION['Username'];
	if(isset($username)){
		
		$mysqli = new mysqli('localhost', 'root', '', 'newssite');
		if($mysqli->connect_errno) {
			printf("Connection Failed: %s\n", $mysqli->connect_error);
			exit;
		}

	if(isset($_POST['submit']) && !empty($_POST['storyTitle'])){
		if(empty($_POST['storyLink'])){
			$_POST['storyLink'] = "";
		}
		if(empty($_POST['description'])){
			$_POST['storyDescription'] = "No description";
		}
		else{
			$_POST['storyDescription'] = $mysqli->real_escape_string($_POST['description']);
		}

		$_POST['storyTitle'] = $mysqli->real_escape_string($_POST['storyTitle']);
		$stmt = $mysqli->prepare("insert into userarticles (uploaded_by, title, description, source) values (?, ?, ?, ?)");
			if(!$stmt){
				echo "Failed to upload";
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
		$stmt->bind_param('ssss', $username, $_POST['storyTitle'] , $_POST['storyDescription'] , $_POST['storyLink']);
		$stmt->execute();
		$stmt->close();
		header('location: ./index.php');
	}

	
		
	}
	

?>

</div>
</body>
</html>