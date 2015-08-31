<?php
	session_start();
	require_once('const.php');
	require_once('yahoo-sdk/lib/YConnect.inc');
 
	//$state ="44Oq44Ki5YWF44Gr5L+644Gv44Gq44KL77yB";// リプレイアタック対策のランダムな文字列を指定してください...(5)
	//$nonce ="5YOV44Go5aWR57SE44GX44GmSUTljqjjgavjgarjgaPjgabjgog=";// レスポンスタイプ...(6)
	$state = Common::getRandomPhrase();
	$nonce = Common::getRandomPhrase();
	$response_type =OAuth2ResponseType::CODE_IDTOKEN;// Scope...(7)
	$scope = array(OIDConnectScope::OPENID,OIDConnectScope::PROFILE,OIDConnectScope::EMAIL,OIDConnectScope::ADDRESS);// display...(8)
	$display =OIDConnectDisplay::DEFAULT_DISPLAY;// prompt...(9)
	$prompt = array(OIDConnectPrompt::DEFAULT_PROMPT);// クレデンシャルインスタンス生成
	$cred =new ClientCredential( YAHOO_APP_KEY, YAHOO_APP_SECRET );// YConnectクライアントのインスタンス生成
	$client =new YConnectClient( $cred );// デバッグ用ログ出力...(10)
	$client->enableDebugMode();// Authorizationエンドポイントにリクエスト...(11)

	$_SESSION['cred'] = $cred;
	$_SESSION['state'] = $state;
	$_SESSION['nonce'] = $nonce;
	$_SESSION['client'] = $client;
  
	// current uri
	if (!strlen($_SERVER['HTTP_REFERER'])) {
		$_SESSION['return_url'] = "http://" .$_SERVER['HTTP_HOST']. "/" ."index.html";
	} else {
		$_SESSION['return_url'] = $_SERVER['HTTP_REFERER'];
	}

	$client->requestAuth(
		YAHOO_CALLBACK,
		$state,
		$nonce,
		$response_type,
		$scope,
		$display,
		$prompt
	);
?>
