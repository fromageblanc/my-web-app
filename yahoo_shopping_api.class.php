<?php
require_once("modifier.class.php");
// Yahoo Shopping
class YahooShoppingApi implements Modifier
{
	// auth
	private $auth;
	private $access_url = "http://shopping.yahooapis.jp/ShoppingWebService/V1/itemSearch";
	private $access_url2 = "http://shopping.yahooapis.jp/ShoppingWebService/V1/itemLookup";	
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

		$p["appid"] = $this->auth["appid"];
		$p["affiliate_type"] = $this->auth["affiliate_type"];
		$p["affiliate_id"] = $this->auth["affiliate_id"];
		//$p["callback"] = $this->auth["callback"];
		$p["query"] = $params['Keywords'];
		$p["type"] = "all";
		$p["image_size"] = "132"; //76,106,132,146,300,600
		if ( strlen(trim($params['Category'])) ) {
			$p["category_id"] = $params['Category'];
		}
		$p["hits"] = "30";
		//$p["sort"] = "score";
		$p["availability"] = "1"; // 在庫あり
		//$p["payment"] = ""; // 支払方式

		$url = $this->access_url. "?";

		foreach ($p as $k => $v) {
			$url .= $k. "=" .mb_convert_encoding($v,"utf-8","auto"). "&";
		}
		
		$context = stream_context_create(array('http' => array('ignore_errors' => true)));
		$data = file_get_contents($url);

		$this->response = $data;
	}

	public function searchById($id)
	{
		$data = null;

		$p["appid"] = $this->auth["appid"];
		$p["affiliate_type"] = $this->auth["affiliate_type"];
		$p["affiliate_id"] = $this->auth["affiliate_id"];
		//$p["callback"] = $this->auth["callback"];
		//$p["query"] = $params['Keywords'];

		// 商品コード
		// 商品検索APIおよびカテゴリランキングAPIの結果リストのCodeタグに含まれる、商品固有のコード。
		// ストアID_ストア商品コードの組み合わせ
		$p["itemcode"] = $id;
		$p["type"] = "all";
		$p["image_size"] = "132"; //76,106,132,146,300,600
		if ( strlen(trim($params['Category'])) ) {
			$p["category_id"] = $params['Category'];
		}
		//$p["hits"] = "30";
		//$p["sort"] = "%2Bprice";
		$p["availability"] = "1"; // 在庫あり
		//$p["payment"] = ""; // 支払方式

		$url = $this->access_url2. "?";

		foreach ($p as $k => $v) {
			$url .= $k. "=" .mb_convert_encoding($v,"utf-8","auto"). "&";
		}

		$context = stream_context_create(array('http' => array('ignore_errors' => true)));
		$data = file_get_contents($url);

		$this->response = $data;

	}
	
	public function response()
	{
		return $this->response;
	}

	private function getAuth()
	{
		// api_authから認証情報を取得
		$this->auth["appid"] = "dj0zaiZpPVJFcndHTWQ2djdHVSZkPVlXazliV0ZNYzJwMk5tc21jR285TUEtLSZzPWNvbnN1bWVyc2VjcmV0Jng9Y2U-";
		$this->auth["affiliate_type"] = "yid";
		$this->auth["affiliate_id"] = "AmJXTDjRmK73iuQriDYrpiU-";
		$this->auth["callback"] = "kangaroonote.com";

	}
}
?>
