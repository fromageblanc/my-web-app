<?php
require_once("modifier.class.php");
require_once("amazon_api.class.php");
require_once("rakuten_api.class.php");
require_once("yahoo_shopping_api.class.php");

// Factory
class ApiFactory
{
	public function create($ecid)
	{
		return $this->createApi($ecid);
	}

	private function createApi($ecid)
	{
		if ($ecid == EC_ID_AMAZON){

			return new AmazonApi();

		} else if ($ecid == EC_ID_RAKUTEN) {

			return new RakutenApi();

		} else if ($ecid == EC_ID_YAHOO_SHOPPING) {

			return new YahooShoppingApi();

		} else { // undefined

			return null;

		}
	}
}
?>
