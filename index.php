<!DOCTYPE html>
<?php
session_start();
$_SESSION['token'] = substr(md5(rand()), 0, 10);
?>
<html>
<head>
	<title>NewsSite</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
	integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" 
	crossorigin="anonymous">
	<link rel="stylesheet" href="style.css">
</head>
<body>
<header class='newsHeader' id='header'>
	
	<div class='newsHeader' id='siteTitle'>
		Whats New??
	</div>
<?php
	if(isset($_SESSION['Username'])){
	echo "<div class='newsHeader' id='uploadButton'>
		<form method='POST' action='./upload.php'>
			<input type='submit' name='upload' value='Upload a Story' class='btn btn-success btn-lg'>
			<input type='hidden' name='token' value=".$_SESSION['token']."/>
		</form>
	</div>";
	}
	else{
		echo "<div class='newsHeader' id='uploadButton'> Login to upload files</div>";
	}
?>
<?php
	
	if(isset($_SESSION['Username'])){
		$Username = $_SESSION['Username'];
		echo "<span class='newsHeader' id='loggedAs'> Logged in as, $Username </span>";
		echo "<div class='newsHeader' id='logout'>
				<form method='POST' action='./logoutpage.php'>
					<input type='submit' name='logout' value='Logout' class='btn btn-danger btn-lg'>
					<input type='hidden' name='token' value=".$_SESSION['token']."/>
				</form>
			</div>";
	}
	else{
		echo "<span class='newsHeader' id='loggedAs'>Login to view and comment on articles </span>";
		echo "<div class='newsHeader' id='login'>
				<form method='POST' action='./loginpage.php'>
					<input type='submit' name='login' value='Login/Register' class='btn btn-primary btn-lg'>
					<input type='hidden' name='token' value=".$_SESSION['token']."/>
				</form>
			</div>";
	}
?>
</header>

<div id='containter'>



<table id='allArticles'>
<tr class='tableHeaders' id='onionNews'><td>News From The Onion:</td><td></td><td></td><td></td></tr>
<tr id='onionArticles'>
<?php
	$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newsSite');
	if($mysqli->connect_errno) {
		printf("Connection Failed: %s\n", $mysqli->connect_error);
		exit;
	}

	$onionHtml = file_get_contents('http://theonion.com/');
	//grab all titles and links from theonion.com
	preg_match_all('<a href="(.+)" title="(.+)">', $onionHtml, $output);
	//grab pic links
	preg_match_all('<img src="(.+)" \/>', $onionHtml, $imglinks);

	//delete previous articles in db
	$deleteQuery = "Delete from articles where img_source IS NOT NULL";
	if(mysqli_query($mysqli, $deleteQuery)){
		
	}
	else{
		echo "Error deleting record: ".mysqli_error($mysqli);
	}

	
	
	for($i=0; $i<4; $i++){
		$titles = $output[2][$i];
		$links = "http://theonion.com".$output[1][$i];
		$imgsources = $imglinks[1][$i];
		//grab article content

		$article_html = file_get_contents($links);
		$article_doc = new DOMDocument();
		libxml_use_internal_errors(TRUE);
		if(!empty($article_html)){
			$article_doc->loadHTML($article_html);
			libxml_clear_errors();
			$article_xpath = new DOMXPath($article_doc);
			$content = $article_xpath->query('//div[@class="content-text"]');
			if($content->length > 0){
				$d = $content->item(0)->nodeValue;
			}
		}

		//add to database
		$stmt = $mysqli->prepare("insert into articles (source, title, img_source, article) values (?, ?, ?, ?)");
			if(!$stmt){
				echo "Failed to upload";
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
		$stmt->bind_param('ssss', $links, $titles, $imgsources, $d);
		$stmt->execute();
		$stmt->close();
	}


	//insert into page
	$insertArticles = $mysqli->prepare("select img_source, title, source from articles");
	if(!$insertArticles){
		printf($mysqli->error);
		exit;
	}
	$insertArticles->execute();
	$insertArticles->bind_result($img, $retrievedTitle, $articleLink);
	while($insertArticles->fetch()){
		echo "<td>
				<a href=".htmlentities($articleLink).">
					<div class='box'>
						<div class='image'>
						<img src=".$img." style='width:300px; height:160px' alt='Photo Unavailable'>
						</div>
						<div class='title'><h3 class='titles'>
						".htmlentities($retrievedTitle)."</h3></div>

					</div>
				</a></td>";
	}
	echo "</tr>";
	$insertArticles->close();
?>
<tr id='userArticlesHeader'>
<td class='tableHeaders'>What Other Users Have To Say</td><td></td><td></td><td></td>
</tr>
</table>
<br><br><br><br><br><br><br><br>
<table id='userArticles'>
<tr class='userArticlesRow'>
<th>Title</th>
<th>Uploaded by</th>
<th>Link</th>
<td></td><td></td><td></td><td></td>
</tr>
<?php
	$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newsSite');
	if($mysqli->connect_errno) {
		printf("Connection Failed: %s\n", $mysqli->connect_error);
		exit;
	}

	//insert all user articles from db
	$insertUserArticles = $mysqli->prepare("select id, title, uploaded_by, source, description from userarticles");
	if(!$insertUserArticles){
		printf($mysqli->error);
		exit;
	}
	$insertUserArticles->execute();
	$insertUserArticles->bind_result($articleNum, $retrievedTitle, $uploaded_by, $source, $retrievedDesc);
	while($insertUserArticles->fetch()){
		
		echo "
				<tr class='userArticlesRow'>
				
				<td>
					<div class='userArticlesTitle'>
					".htmlentities($retrievedTitle)."
					</div>
				</td>
				<td>
					<span class='uploaded_by'>".htmlentities($uploaded_by)."</span>
				</td>
				<td>
					<a href=".$source."> <div class='link'>".htmlentities($source)."</div></a>
				
				</td>
				<td>
					<form method='GET' action='./stories.php'>
					<input type='hidden' name='viewStoryId' value=".$articleNum.">
					<div class='viewArticleSubmits'>
					<input type='submit' name='viewArticleSubmit' value='View Story'>
					</div>
					</form>
				</td>
				";
			

				echo "
				<td>
				<div class='userArticlesDescription'>
				".htmlentities($retrievedDesc)."
				</div>
				</td>";

				if(isset($_SESSION['Username']) && $_SESSION['Username'] == $uploaded_by){
					echo "
				<td>
					<form method='POST' action='./delete.php'>
					<input type='hidden' name='articleNum' value=".$articleNum.">
					<input type='submit' name='deleteArticle' value='Delete' class='btn btn-danger'>
					<input type='hidden' name='token' value=".$_SESSION['token']."/>
					</form>
				</td>
				<td>
					<form method='GET' action='./edit.php'>
					<input type='hidden' name='numArticle' value=".$articleNum.">
					<input type='submit' name='editArticle' value='Edit' class='btn btn-primary'>
					
					</form>
				</td>";
					
				}
				else{
					echo "<td></td><td></td>";
				}
				echo "
</tr>
				";
		
	}
	$insertUserArticles->close();

?>

</table>
</div>





</body>
</html>