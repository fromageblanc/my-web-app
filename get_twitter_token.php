<?php
  session_start();
  require_once('const.php');
  require_once('twitter-sdk/twitteroauth.php');
  
  // request token
  $tw = new TwitterOAuth(TWITTER_CONSUMER_KEY,TWITTER_CONSUMER_SECRET);
  $token = $tw->getRequestToken(TWITTER_CALLBACK);
  if (!isset($token['oauth_token'])) {
  	echo "Error: fail to token";
  	exit();
  }
  
  // save token 
  $_SESSION['oauth_token'] = $token['oauth_token'];
  $_SESSION['oauth_token_secret'] = $token['oauth_token_secret'];
  
  // current uri
  if (!strlen($_SERVER['HTTP_REFERER'])) {
  	$_SESSION['return_url'] = "http://" .$_SERVER['HTTP_HOST']. "/" ."index.html";
  } else {
  	$_SESSION['return_url'] = $_SERVER['HTTP_REFERER'];
  }
  $auth_url = $tw->getAuthorizeURL($_SESSION['oauth_token']);
  //$auth_url = 'https://api.twitter.com/oauth/authorize?oauth_token=' .$_SESSION['oauth_token'];
  header("Location: " .$auth_url);
  
?>
