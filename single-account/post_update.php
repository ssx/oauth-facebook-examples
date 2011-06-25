<?php
// OAuth Examples for Facebook
// by Scott Wilcox <scott@dor.ky> http://dor.ky @dordotky
// http://dor.ky/code/oauth-examples
// https://github.com/dordotky/oauth-facebook-examples
//
// Using a previously provided access token to post a new update

// Initialise a session to store tokens in throughout the example process
session_start();

// Require EpiCurl.php and EpiFacebook.php libraries
require_once '../facebook-async/EpiCurl.php';
require_once '../facebook-async/EpiFacebook.php';

// The tokens specific to your application, make sure that you've added the
// requesting IP to the whitelist in the 'Advanced' section of your
// application settings. The $redirectUri value is the location of this file
$clientId = '124877107597710';
$clientSecret = '96fea8133cabb860af99ee5168169015';

// If we don't have a token, send the user to go get one
if (empty($_SESSION["token"]) {
	die("<p>Head over to <a href=\"get_access_token.php\">get_access_token.php</a> to obtain an access token.")
} else {
	// If we have POST'd some text then push that out to the API, otherwise
	// show a form for the user to enter text
	if ($_POST["message"]) {
		try {
			$facebookObject = new EpiFacebook($clientId, $clientSecret, $_SESSION["token"]);
			$response = $facebookObject->post("/me/feed", array("message" => $_POST["message"]));
			if ($response->responseText) {
				echo var_dump($response->responseText);
			}
		} Catch (Exception $error) {
			echo "Exception Occured: $error";
		}
	} else {
		// Show our input form 
		?>
		<form method="post" action="?">
			<textarea name="message"></textarea><br />
			<input type="submit" value="Post to Stream" />
		</form>
		<?
	}
}
?>