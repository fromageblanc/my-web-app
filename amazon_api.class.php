<?php
require_once("modifier.class.php");
// Amazon
class AmazonApi implements Modifier
{
	// property
	private $auth;
	private $access_url = "https://ecs.amazonaws.jp/onca/xml";
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

		// common params -----------------------------------
		$p["Service"] = "AWSECommerceService";
		$p["Version"] = "2011-08-02";
		$p["AssociateTag"] = $this->auth["associate_id"];
		$p["SignatureMethod"] = "HmacSHA256";
		$p["SignatureVersion"] = 2;

		// time zone (ISO8601 format)
		$p["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");

		$p["Operation"] = "ItemSearch";
		$p["SearchIndex"] = $params['Category'];
		if ( $p["SearchIndex"] != "All" ) {
			$p["Sort"] = "salesrank";
		}
		$p["Keywords"] = mb_convert_encoding($params['Keywords'],"utf-8","auto");
		$p["ResponseGroup"] = "ItemAttributes,Offers,Images";
		$p["ItemPage"] = (!empty($params['ItemPage'])) ? $params['ItemPage']:"1";

		// sort by asc
		ksort($p);

		$qstr = "AWSAccessKeyId=" .$this->auth['access_key'];
		foreach ( $p as $k=>$v ) {
			$qstr .= "&" .Common::urlencode_RFC3986($k). "=" .Common::urlencode_RFC3986($v);
		}

		$parsed_url = parse_url($this->access_url);
		$string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$qstr}";
		$signature = base64_encode(
									hash_hmac('sha256', 
												$string_to_sign, 
												$this->auth['secret_access_key'], 
												true)
									);

		$url = $this->access_url.'?'.$qstr.'&Signature='.Common::urlencode_RFC3986($signature);

		$context = stream_context_create(array('http' => array('ignore_errors' => true)));
		$data = file_get_contents($url,false,$context);

		$this->response = $data;
	}

	public function searchById($id)
	{
		$data = null;

		// common params -----------------------------------
		$p["Service"] = "AWSECommerceService";
		$p["Version"] = "2011-08-02";
		$p["ItemId"] = $id;
		$p["AssociateTag"] = $this->auth["associate_id"];
		$p["SignatureMethod"] = "HmacSHA256";
		$p["SignatureVersion"] = 2;

		// time zone (ISO8601 format)
		$p["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");

		$p["Operation"] = "ItemLookup";
		/*
		$p["SearchIndex"] = $params['Category'];
		if ( $p["SearchIndex"] != "All" ) {
			$p["Sort"] = "salesrank";
		}
		*/
		//$p["Keywords"] = mb_convert_encoding($params['Keywords'],"utf-8","auto");
		//$p["ItemPage"] = (!empty($params['ItemPage'])) ? $params['ItemPage']:"1";
		$p["ResponseGroup"] = "ItemAttributes,Offers,Images";

		// sort by asc
		ksort($p);

		$qstr = "AWSAccessKeyId=" .$this->auth['access_key'];
		foreach ( $p as $k=>$v ) {
			$qstr .= "&" .Common::urlencode_RFC3986($k). "=" .Common::urlencode_RFC3986($v);
		}

		$parsed_url = parse_url($this->access_url);
		$string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$qstr}";
		$signature = base64_encode(
									hash_hmac('sha256', 
												$string_to_sign, 
												$this->auth['secret_access_key'], 
												true)
									);

		$url = $this->access_url.'?'.$qstr.'&Signature='.Common::urlencode_RFC3986($signature);
		$context = stream_context_create(array('http' => array('ignore_errors' => true)));
		$data = file_get_contents($url,false,$context);

		// for test return url
		$this->response = $data;
	}

	public function response()
	{
		// xmlをパース
		$xml = simplexml_load_string($this->response);
		$res = array();

		// TotalResults、TotalPagesはItemLookupでは戻らない
		$operation = (String)$xml->OperationRequest->Arguments->Argument[0]->attributes()->Value;
		if (strcmp($operation,"ItemLookup")) {
			$res['TotalResults'] = $xml->Items->TotalResults;
			$res['TotalPages'] = $xml->Items->TotalPages;
		}
		
		$i = 0;
		foreach($xml->Items->Item as $current){
			$res[$i]['asin'] = $current->ASIN;
			$res[$i]['image'] = $current->MediumImage->URL;
			$res[$i]['brand'] = $current->ItemAttributes->Brand;
			$res[$i]['manufacturer'] = $current->ItemAttributes->Manufacturer;
			$res[$i]['title'] = $current->ItemAttributes->Title;
			$res[$i]['detail_url'] = $current->DetailPageURL;
			$i++;
		}
		return $res;
	}

	private function getAuth()
	{
		// api_authから認証情報を取得 
		$this->auth["associate_id"] = EC_ID_AMAZON;
		$this->auth["access_key"] = ACCESS_KEY;
		$this->auth["secret_access_key"] = SECRET_ACCESS_KEY;
	}
}
?>
