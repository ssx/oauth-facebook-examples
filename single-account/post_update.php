<?php
// Initialise a session to store tokens in throughout the example process
session_start();

// Require EpiCurl.php and EpiFacebook.php libraries
require_once 'EpiCurl.php';
require_once 'EpiFacebook.php';

// The tokens specific to your application, make sure that you've added the
// requesting IP to the whitelist in the 'Advanced' section of your
// application settings. The $redirectUri value is the location of this file
$clientId = '149212441816006';
$clientSecret = 'c8160ada82cde8599862af51f7208c7a';
$redirectUri = "http://facebook-async.local/simpleTest.php";

// Create a new object to make our first call to get a AuthorizeUrl. This
// also includes the array for 'scope' which are you permissions request. You
// can find more examples of permissions at the Facebook documentation pages
// at https://developers.facebook.com/docs/authentication/permissions/
$fsObjUnAuth = new EpiFacebook($clientId, $clientSecret);
$authorizeUrl = $fsObjUnAuth->getAuthorizeUrl($redirectUri, array("email", "publish_stream", "read_stream", "offline_access"));
echo "<a href=\"".$authorizeUrl."\">".$authorizeUrl."</a><hr />";

// If we've come back from Facebook with a value in $_GET["code"], then
// exchange that for a permanent token.
if (!empty($_GET["code"]))
{
	$token = $fsObjUnAuth->getAccessToken($_GET['code'], $redirectUri);
	if ($token->responseText)
	{
		$token = str_replace("access_token=", "", $token->responseText);
		echo "I've got a token: $token";
		$_SESSION["token"] = $token;
	}
}

// If we have a user access token in $_SESSION["token"] then make a few
// example calls to the Facebook Graph API
if (!empty($_SESSION["token"]))
{
	echo "<hr /><h1>Get Your Data</h1>";
	$obj = new EpiFacebook($clientId, $clientSecret, $_SESSION["token"]);
	$user = $obj->get("/me");
	$data = json_decode($user->responseText);
	echo "<pre>".var_dump($data)."</pre>";

	echo "<hr /><h1>Post a Stream Update</h1>";
	$obj = new EpiFacebook($clientId, $clientSecret, $_SESSION["token"]);
	$user = $obj->post("/".$data->id."/feed", array("message" => "Hello World", "link" => "http://google.com/"));
	$data = json_decode($user->responseText);
	echo "<pre>".var_dump($data)."</pre>";
}
?>