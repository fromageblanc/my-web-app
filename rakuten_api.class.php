<?php
require_once("modifier.class.php");
// Rakuten
class RakutenApi implements Modifier
{
	// auth
	private $auth;
	private $access_url = "https://app.rakuten.co.jp/services/api/IchibaItem/Search/20120723";
	private $response;

	// constructer
	function __construct()
	{
		// 認証情報をセット
		$this->getAuth();
	}

	public function search($params)
	{
		$data = null;

		// 共通
		$p["applicationId"] = $this->auth["application_id"];
		$p["affiliateId"] = $this->auth["affiliate_id"];
		$p["format"] = "xml";
		//$p["callback"] = $this->auth["callback_domain"] ;

		//区分:サービス固有パラメーター
		$p["keyword"] = $params['Keywords'];
		$p["genreId"] = $params['Category'];
		$p["hits"] = "30";
		$p["page"] = "1";
		$p["sort"] = "-affiliateRate"; // アフィリエイト料率順（降順）
		$p["carrier"] = "0"; // PC:0  mobile:1,smartphon:2
		$p["imageFlag"] = "1"; // 画像のある商品のみ

		$url = $this->access_url. "?";
		foreach ($p as $k => $v) {
			$url .= $k ."=". mb_convert_encoding($v,"utf-8","auto"). "&";
		}

		$context = stream_context_create(array('http' => array('ignore_errors' => true)));
		$data = file_get_contents($url,false,$context);

		$this->response = $data;
	}

	public function response()
	{
		return $this->response;
	}

	public function searchById($id)
	{
		$data = null;

		// 共通
		$p["applicationId"] = $this->auth["application_id"];
		$p["affiliateId"] = $this->auth["affiliate_id"];
		$p["format"] = "xml";
		//$p["callback"] = $this->auth["callback_domain"] ;

		//区分:サービス固有パラメーター
		//$p["keyword"] = $params['Keywords'];
		$p["itemCode"] = $id;
		//$p["genreId"] = $params['Category'];
		//$p["hits"] = "30";
		//$p["page"] = "1";
		//$p["sort"] = "-affiliateRate"; // アフィリエイト料率順（降順）
		$p["carrier"] = "0"; // PC:0  mobile:1,smartphon:2
		$p["imageFlag"] = "1"; // 画像のある商品のみ

		$url = $this->access_url. "?";
		foreach ($p as $k => $v) {
			$url .= $k ."=". mb_convert_encoding($v,"utf-8","auto"). "&";
		}

		$context = stream_context_create(array('http' => array('ignore_errors' => true)));
		$data = file_get_contents($url,false,$context);

		$this->response = $data;
	}

	private function getAuth()
	{
		// api_authから認証情報を取得
		$this->auth["application_id"] = RAKUTEN_APP_ID;
		$this->auth["application_secret"] =RAKUTE_APP_SECRET;
		$this->auth["affiliate_id"] = RAKUTEN_AFF_ID;
		$this->auth["callback_domain"] = "*********.com";
		$this->auth["policy_url"] = "";
	}
}
?>
