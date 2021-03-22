<?php

	session_start();

	$error = "";

	if (array_key_exists("logout", $_GET)) {
		
		unset($_SESSION);
		setcookie("id", "", time() - 60*60);
		$_COOKIE['id'] = "";

	} elseif ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR 
		(array_key_exists("id", $_COOKIE) AND $_COOKIE['id']) ) {
		
		header("Location: loggedinpage.php");

	}

	if (array_key_exists("submit", $_POST)) {

		include("connection.php");		
		
		if (!$_POST['email']) {
			
			$error .= "An email address is required</br>";

		} 

		if (!$_POST['password']) {
			
			$error .= "A password is required</br>";

		} 

		if ($error != "") {
			
			$error = "<p>There were error(s) in your form:</p>".$error;

		} else {

			if ($_POST['signUp'] == '1') {

				$query = "SELECT id FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";

				$result = mysqli_query($link, $query);

				if (mysqli_num_rows($result) > 0) {
					
					$error = "That email address is taken.";

				} else {

					$query = "INSERT INTO `users` (`email`, `password`) VALUES (
						'".mysqli_real_escape_string($link, $_POST['email'])."', 
						'".mysqli_real_escape_string($link, $_POST['password'])."')";

					if (!mysqli_query($link, $query)) {
						
						$error = "<p>Could not sign you up. Please try again later!</p>";

					} else {

						$query = "UPDATE `users` SET password = 
							'".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = 
							".mysqli_insert_id($link)." LIMIT 1";

						$id = mysqli_insert_id($link);

						mysqli_query($link, $query);

						$SESSION['id'] = $id;

						if ($_POST['stayLoggedIn'] == '1') {
							
							setcookie("id", $id, time() + 60*60*24*365);

						}

						header("Location: loggedinpage.php");

					}

				} 

			} else {

					$query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, 
						$_POST['email'])."'";

					$result = mysqli_query($link, $query);

					$row = mysqli_fetch_array($result);

					if (isset($row)) {
						
						$hashedPassword = md5(md5($row['id']).$_POST['password']);

						if ($hashedPassword == $row['password']) {
							
							$_SESSION['id'] = $row['id'];

							if (isset($_POST['stayLoggedIn']) AND $_POST['stayLoggedIn'] == '1') {
							
								setcookie("id", $row['id'], time() + 60*60*24*365);

							}

							header("Location: loggedinpage.php");

						} else {

							$error = "That email/password combination could not be found";

						}

					} else {

						$error = "That email/password combination could not be found";

					}

				}

		}

	}

?>


<?php include("header.php") ?>

  	<div class="container" id="homePageContainer">
	
		<h1><strong>Secret Diary</strong></h1>  

		<p><strong>You can store your idea and thoughts here securely.</strong></p>

		<div id="error"><?php if ($error != "") {
			echo '<div class = "alert alert-danger" role = "alert">'.$error.'</div>';
		} ?></div>

		<form method="post" id="signUpForm">

			<p class="bg-secondary text-white">Sign up? don't worry it's free.</p>
  		
	  		<div class="form-group">
	    
	    		<input type="email" class="form-control" name="email" placeholder="Your email">
	    
	  		</div>
	  		
	  		<div class="form-group">
	    
	    		<input type="password" class="form-control" name="password" placeholder="Password">
	  
	  		</div>
	  
	  		<div class="form-group form-check">
	    	
	    		<input type="checkbox" class="form-check-input" name="stayLoggedIn" value="1">

	    		<label class="form-check-label" for="exampleCheck1">Stay logged in</label>
	    
	  		</div>
	  
	  		<div class="form-group">

	  			<input type="hidden" name="signUp" value="1">
	  
	  			<input type="submit" class="btn btn-success" name="submit" value="Sign Up!">

	  		</div>

	  		<p><a href="#" class="text-info toggleForm">Log in</a></p>

		</form>

		<form method="post" id="logInForm">

			<p class="bg-secondary text-white">Enter your email and password to access.</p>
  		
	  		<div class="form-group">
	    
	    		<input type="email" class="form-control" name="email" placeholder="Your email">
	    
	  		</div>
	  		
	  		<div class="form-group">
	    
	    		<input type="password" class="form-control" name="password" placeholder="Password">
	  
	  		</div>

			<div class="form-group form-check">
	    	
	    		<input type="checkbox" class="form-check-input" name="stayLoggedIn" value="1">

	    		<label class="form-check-label" for="exampleCheck1">Stay logged in</label>
	    
	  		</div>
			
			<div class="from-group">

				<input type="hidden" name="signUp" value="0">

				<input type="submit" class="btn btn-success" name="submit" value="Log in!">
				
			</div>

			<p><a href="#" class="text-info toggleForm">Sign Up</a></p>

		</form>		

	</div>

<?php include("footer.php") ?>






