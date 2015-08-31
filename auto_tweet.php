<?php
// 今は動かない・・・
	require_once('const.php');
	require_once('twitter-sdk/twitteroauth.php');
  
	class AutoTweet 
	{  
		public static function auto_tweet($tweet)
		{
			//$tweet_api_path = 'https://api.twitter.com/1/statuses/update.xml';
			$tweet_api_path = 'http://api.twitter.com/1.1/statuses/update.json';
		
			// OAuthオブジェクト生成
			$to = new TwitterOAuth(TWITTER_CONSUMER_KEY,TWITTER_CONSUMER_SECRET,TWITTER_ACCESS_TOKEN,TWITTER_ACCESS_TOKEN_SECRET);

			//投稿
			$req = $to->OAuthRequest($tweet_api_path,"POST",array("status"=>$tweet));

			// レスポンスを表示する場合は下記コメントアウトを外す
			//header("Content-Type: application/xml");
			//echo $req;
		}
	}

?>
