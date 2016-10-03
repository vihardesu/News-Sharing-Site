<!DOCTYPE html>
<?php 
session_start();
?>
<html>
<head>
	<title>Login Page</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
	integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" 
	crossorigin="anonymous">
</head>
<body>
<h1>
	Login or Register Below
</h1> <br><br>

<form id='login-password' method="POST">
<h1>Login:</h1>
<input type="text" name="username" placeholder="Username">
<input type="password" name="password" placeholder="Password">
<input type="submit" name="login" value="Login">
<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
</form>
<br> <br>
<h1>Register:</h1>
<form id='register' method="POST">
<input type="text" name="first_name" placeholder="First Name">
<input type="text" name="last_name" placeholder="Last Name">
<input type="text" name="newUser" placeholder="Username">
<input type="password" name="newPass" placeholder="New Password">
<input type="email_address" name="email" placeholder="Email Address">
<input type="submit" name="submit" value="Register">
<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
</form>


<?php
//connect to database, reference this when making calls
$mysqli = new mysqli('localhost', 'newsweb', 'ilovenews', 'newsSite');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}

//Check Login Info
if(isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['login']) && !empty($_POST['login'])){
	
	$stmt = $mysqli->prepare("SELECT COUNT(*), Username, Password FROM users WHERE username=?");
	 
	// Bind the parameter
	$user = $mysqli->real_escape_string($_POST['username']);
	$stmt->bind_param('s', $user);
	
	$stmt->execute();
	 
	// Bind the results
	$stmt->bind_result($cnt, $user, $pwd_hash);
	$stmt->fetch();
	
	$pwd_guess = trim(htmlentities($_POST['password']));
	// Compare the submitted password to the actual password hash
	if( $cnt==1 && crypt($pwd_guess, $pwd_hash)==$pwd_hash){
		// Login succeeded!
		$_SESSION['Username'] = $user;
		$_SESSION['token'] = substr(md5(rand()), 0, 10);
		// Redirect to your target page
		header('location: ./index.php');
	}else{
		echo "Invalid username and/or password";
	}
}

//For Registration
if(isset($_POST['first_name']) && !empty($_POST['first_name']) && isset($_POST['last_name']) && !empty($_POST['last_name']) && isset($_POST['newUser']) && !empty($_POST['newUser'])
	&& isset($_POST['newPass']) && !empty($_POST['newPass']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['submit']) && !empty($_POST['submit'])){
		$password = crypt($_POST['newPass']);
	 	$first = $mysqli->real_escape_string($_POST['first_name']);
		$last= $mysqli->real_escape_string($_POST['last_name']);
		$email = $mysqli->real_escape_string($_POST['email']);
		$newUser = $mysqli->real_escape_string($_POST['newUser']);
		
		//check if user already exists
		$userCheck = $mysqli->prepare("SELECT Username FROM users");
		$userCheck->execute();
		$userCheck->bind_result($usernameCheck);
		while($userCheck->fetch()){
			if($usernameCheck == $newUser){
				echo "Username already taken";
				exit;
			}
		}

		//check if email is in use
		$emailCheck = $mysqli->prepare("SELECT email_address FROM users");
		$emailCheck->execute();
		$emailCheck->bind_result($checking_email);
		while($emailCheck->fetch()){
			if($checking_email == $email){
				echo "Email address already in use";
				exit;
			}
		}

	//insert into database
	$stmt = $mysqli->prepare("insert into users (Username, Password, first_name, last_name, email_address) values (?, ?, ?, ?, ?)");
	if(!$stmt){
		echo "Failed to register, you suck";
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('sssss', $newUser, $password, $first, $last, $email);
	$stmt->execute();
	$stmt->close();
	echo "Successful Registration! Sign in above.";


}



?>

</body>
</html>