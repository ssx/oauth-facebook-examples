<?php
// OAuth Examples for Facebook
// by Scott Wilcox <scott@dor.ky> http://dor.ky @dordotky
// http://dor.ky/code/oauth-examples
// https://github.com/dordotky/oauth-facebook-examples
//
// Obtain an access token for use with these examples

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
$redirectUri = "http://oauth-facebook.local/single-account/get_access_token.php";

// Our page title
echo "<h1>Get Access token</h1>";

// Create our Facebook object
$facebookObject = new EpiFacebook($clientId, $clientSecret);

// If we've come back from Facebook with a value in $_GET["code"], then
// exchange that for a permanent token, otherwise prompt to click through for a
// new token.
if (empty($_GET["code"])) {
	// Create a new object to make our first call to get a AuthorizeUrl. This
	// also includes the array for 'scope' which are you permissions request. You
	// can find more examples of permissions at the Facebook documentation pages
	// at https://developers.facebook.com/docs/authentication/permissions/ If you want
	// to perform actions while a user is not logged in to your app/site then you need
	// to request the 'offline_access' token which will provide a non-expiring token
	$permissions = array("email", "publish_stream", "read_stream", "offline_access");
	
	$authorizeUrl = $facebookObject->getAuthorizeUrl($redirectUri, $permissions);
	echo "<p>To begin, request an access token by clicking the link below.</p>";
	echo "<a href=\"".$authorizeUrl."\">".$authorizeUrl."</a><hr />";
} else {
	$token = $facebookObject->getAccessToken($_GET['code'], $redirectUri);
	if ($token->responseText)
	{
		$token = str_replace("access_token=", "", $token->responseText);
		echo "I've got a token: $token";
		$_SESSION["token"] = $token;

		echo "<h1>A little information about you</h1>";
		$obj = new EpiFacebook($clientId, $clientSecret, $_SESSION["token"]);
		$user = $obj->get("/me");
		
		// Convert the provided XML into an object that we can use
		$data = json_decode($user->responseText);
		
		// Also add the UID to the session
		$_SESSION["uid"] = $user["id"];
		
		// Display a little basic information
		echo "<p>Hello ".$user["first_name"].", your Facebook profile link is: <a href=\"".$user["link"]."\">".$user["link"]."</a></p>";
		echo "<p>You last updated your profile on ".date("l jS F",strtotime($user["updated_time"])).".</p>";
		echo "<pre>";
		echo var_dump($data);
		echo "</pre>";
	}
}	

echo "<hr />";
echo "<h3>Other Examples</h3>";
echo "<ul>";
echo "<li><a href=\"post_update.php\">Post Update</a></li>";
echo "<li><a href=\"find_friends_using_app.php\">Find Friends Using App</a></li>";
echo "</ul>";
?>