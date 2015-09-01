<?php  
  //認証系は一部マスク
  
  // no depend this const valuable
  require_once("common_methods.class.php");

  // software-instrument.com database auth 
  define('DSN','pgsql:dbname=softwareinstrument_db host=localhost port=5432');
  define('USER','kangaroonote');
  define('PASSWORD','xv3080');

// Ec Id
 define('EC_ID_AMAZON',1);			// アマゾン
 define('EC_ID_RAKUTEN',2);			// 楽天
 define('EC_ID_YAHOO_SHOPPING',3);	// ヤフーショッピング
 define('EC_ID_DMM',4);				//DMM.com

 // カテゴリ指定なし（全ジャンル）
  define('CATEGORY_ALL_AMAZON','All');	// アマゾン
  define('CATEGORY_ALL_RAKUTEN','0');	// 楽天
  define('CATEGORY_ALL_YAHOO_SHOPPING','');	// ヤフーショッピング
  
  // sns application auth

  // facebook
  define('FACEBOOK_APP_ID','31092***5677293');
  define('FACEBOOK_SECRET','c26657***6a17ae3a74***be***da3da');
  
  // twitter
  define('TWITTER_CONSUMER_KEY','Yi7VFYg7s44***VBdFA');
  define('TWITTER_CONSUMER_SECRET','F***IVHLqPw***NPTkVxnPXoBoTO9SfK***WugA7Q');
  define('TWITTER_CALLBACK','http://******-instrument.com/callback.php');
  define('TWITTER_ACCESS_TOKEN','1177***122-YYO00t***6ZT8aNzpBDiJK4s9LUSyI***v2Og3f');
  define('TWITTER_ACCESS_TOKEN_SECRET','dsy6FwpctX***m85J1A***a9u5rqPCs***zrbKYArA');
  
  // yahoo
  define('YAHOO_APP_KEY','dj0zaiZpPVJFcndHTWQ2djdHVSZ***lXazli***YzJwMk5tc21jR285TUEtLSZzPWNvbnN1bWVyc2VjcmV***g9Y2U-');
  define('YAHOO_APP_SECRET','9e5da97f87adb118***305dc***959c0b7c102ac');
  define('YAHOO_CALLBACK','http://*********-********.com/ycallback.php');

  // google
  define('GOOGLE_CLIENT_ID', '86987***0521.apps.googleusercontent.com');
  define('GOOGLE_CLIENT_SECRET', '8***P5GnXi2f***_SZQ5X3Ze');
  define('GOOGLE_CALLBACK', 'http://******-************.com/oauth2callback.php');
  
?>
