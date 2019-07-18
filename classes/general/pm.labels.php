<?
IncludeModuleLangFile(__FILE__);

class PromediaAnalyticsHeaders {
	
	function headerCheckValue() {
		if($arHeaderValues = self::getUtmValues()) {
			return $arHeaderValues;
		} else {
			return false;
		}
	}

	static function getUtmValues() {
		$arHeaderValues = array();
		if(strlen($_REQUEST["utm_source"]) && $_REQUEST["utm_source"] != 'null') {
			$arHeaderValues["ADV_SERVICE"] = $_REQUEST["utm_source"];
		}
		if(strlen($_REQUEST["utm_medium"]) && $_REQUEST["utm_medium"] != 'null') {
			$arHeaderValues["ADV_SOURCE"] = $_REQUEST["utm_medium"];
		}
		if(strlen($_REQUEST["utm_term"]) && $_REQUEST["utm_term"] != 'null') {
			$arHeaderValues["ADV_KW"] = $_REQUEST["utm_term"];
		}
		if(strlen($_REQUEST["utm_content"]) && $_REQUEST["utm_content"] != 'null') {
			$arHeaderValues["ADV_AD"] = iconv("UTF-8", "windows-1251", $_REQUEST["utm_content"]);
		}
		if(strlen($_REQUEST["utm_campaign"]) && $_REQUEST["utm_campaign"] != 'null') {
			$arHeaderValues["ADV_CAMPAIGN"] = $_REQUEST["utm_campaign"];
		}
		if(strlen($_REQUEST["keyword"]) && $_REQUEST["keyword"] != 'null') {
			$arHeaderValues["ADV_KEYWORDS"] = $_REQUEST["keyword"];
		}
		if(strlen($_REQUEST["position"]) && $_REQUEST["position"] != 'null') {
			$arHeaderValues["ADV_POSITION"] = $_REQUEST["position"];
		}
		
		if(count($arHeaderValues))
			return $arHeaderValues;
		
		return false;
	}
}
?>