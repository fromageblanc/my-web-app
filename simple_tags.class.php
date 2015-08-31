<?php
require_once("api_factory.class.php");

class SimpleTags 
{
	function getSimpleTags($kw,$target_ec_arr,$page=null)
	{

		$response = array();

		$fac = new ApiFactory();

		if ( in_array(EC_ID_AMAZON,$target_ec_arr) ) {
			// amazon
			$amazon_obj = $fac->create(EC_ID_AMAZON);
			$amazon_obj->search(array('Keywords'=>$kw,'Category'=>CATEGORY_ALL_AMAZON,'ItemPage'=>$page));
			$amazon_res = $amazon_obj->response();
			$response['AMAZON'] = $amazon_res;
		}

		if ( in_array(EC_ID_RAKUTEN,$target_ec_arr) ) {
			// rakuten
			$rakuten_obj = $fac->create(EC_ID_RAKUTEN);
			$rakuten_obj->search(array('Keywords'=>$kw,'Category'=>CATEGORY_ALL_RAKUTEN));
			$rakuten_res = $rakuten_obj->response();
			$response['RAKUTEN'] = $rakuten_res;
		}

		if ( in_array(EC_ID_YAHOO_SHOPPING,$target_ec_arr) ) {
			// YahooShopping
			$yahoo_obj = $fac->create(EC_ID_YAHOO_SHOPPING);
			$yahoo_obj->search(array('Keywords'=>$kw,'Category'=>CATEGORY_ALL_YAHOO_SHOPPING));
			$yahoo_res = $yahoo_obj->response();
			$response['YAHOO_SHOPPING'] = $yahoo_res;
		}

		return $response;
	}
	
	function getSimpleTagById($id,$target_ec_arr,$page=null)
	{

		$response = array();

		$fac = new ApiFactory();

		if ( in_array(EC_ID_AMAZON,$target_ec_arr) ) {
			// amazon
			$amazon_obj = $fac->create(EC_ID_AMAZON);
			$amazon_obj->searchById($id);
			$amazon_res = $amazon_obj->response();
			$response['AMAZON'] = $amazon_res;
		}		

		if ( in_array(EC_ID_RAKUTEN,$target_ec_arr) ) {
			// rakuten
			$rakuten_obj = $fac->create(EC_ID_RAKUTEN);
			$rakuten_obj->searchById($id);
			$rakuten_res = $rakuten_obj->response();
			$response['RAKUTEN'] = $rakuten_res;
		}

		if ( in_array(EC_ID_YAHOO_SHOPPING,$target_ec_arr) ) {
			// YahooShopping
			$yahoo_obj = $fac->create(EC_ID_YAHOO_SHOPPING);
			$yahoo_obj->searchById($id);
			$yahoo_res = $yahoo_obj->response();
			$response['YAHOO_SHOPPING'] = $yahoo_res;
		}

		return $response;

	}
}
?>
