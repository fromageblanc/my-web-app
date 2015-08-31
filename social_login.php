<?php
    require_once('const.php');
    
	require_once('facebook-sdk/facebook.php');

	$config = array('appId'=>FACEBOOK_APP_ID,'secret'=>FACEBOOK_SECRET);
	$facebook = new Facebook($config);
	$user_id = $facebook->getUser();
	//echo $user_id;
	$_SESSION['login_url_fb'] = $facebook->getLoginUrl();
	
	if ($user_id && !isset($_SESSION['login_flg'])) {
		$_SESSION['login_flg'] = true;
		// get profile
		$ret  = $facebook->api('/me','GET');
		$_SESSION['user_id'] = $user_id;
		$_SESSION['username'] = $ret['name'];
		$_SESSION['sns'] = 'facebook';
	} else {
	}
	//echo '<pre>';var_dump($_SESSION);echo '</pre>';
?>
