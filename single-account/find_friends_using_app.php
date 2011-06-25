<?php
// OAuth Examples for Facebook
// by Scott Wilcox <scott@dor.ky> http://dor.ky @dordotky
// http://dor.ky/code/oauth-examples
// https://github.com/dordotky/oauth-facebook-examples
//
// Find your friends that are also using this application

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
if (empty($_SESSION["token"])) {
	die("<p>Head over to <a href=\"get_access_token.php\">get_access_token.php</a> to obtain an access token.");
} else {
	// This will display all friends of a user that are using the
	// application that $clientId and $clientSeceret are from.
		try {
			$facebookObject = new EpiFacebook($clientId, $clientSecret, $_SESSION["token"]);
			$user = $facebookObject->get(
					"https://api.facebook.com/method/fql.query",
					array(
							"format" => "xml",
							"query" => "SELECT uid, name, pic_square FROM user WHERE has_added_app=1 and uid IN (SELECT uid2 FROM friend WHERE uid1 = ".$_SESSION["uid"].")"
					)
			);
			if ($users = simplexml_load_string($user->responseText)) {
				if (count($users) > 0) {
					echo "<p>A total of ".count($reply)."friends are using this application.</p>";
	          		echo "<br /><table><tr>";
	            		foreach ($users->user as $user) {
	              		echo "<td><img src=\"".$user->pic_square."\" /><br />".$user->name."</td>";
	              		$i++; if ($i > 3) { $i = 0; echo "</tr><tr>"; }
	                  }
	                  echo "</tr></table>";					
				} else {
					echo "<p>No friends of ".$_SESSION["uid"]." are using this application.</p>";
				}
			}
		} Catch (Exception $error) {
			echo "<p>Exception: $error</p>";
		}
}

echo "<hr />";
echo "<h3>Other Examples</h3>";
echo "<ul>";
echo "<li><a href=\"get_access_token.php\">Get Access Token</a></li>";
echo "<li><a href=\"post_update.php\">Post Update</a></li>";
echo "</ul>";
?>