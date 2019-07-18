<?
IncludeModuleLangFile(__FILE__);

class PromediaAnalyticsSearch {	
	function getSearchRequest($referer) {
		if(!strlen($referer))
			return false;
		
		$arSearchSystem = self::getSearchSystem();
		
		$arUrl = parse_url($referer);
		if(strlen($arUrl["host"])) {
			$arSearchData = array();
			$searchSystemCode = "";
			foreach($arSearchSystem as $arSS) {
				foreach($arSS["HOSTS"] as $host) {
					if(preg_match($host, $arUrl["host"])) {
						$searchSystemCode = $arSS["CODE"];
						break;
					}
				}
				if(strlen($searchSystemCode)) {
					$arSearchData["SEARCH_SYSTEM"] = $arSS["NAME"];
					break;
				}
			}
			if(strlen($arUrl["query"]) && strlen($searchSystemCode)) {
				parse_str($arUrl["query"], $arSearchParams);
				foreach($arSearchSystem as $arSS) {
					if($arSS["CODE"] == $searchSystemCode) {
						foreach($arSS["PARAMS"] as $key=>$param) {
							if(array_key_exists($param, $arSearchParams)) {
								if($key == "SEARCH_QUERY") {
									if(ToUpper(LANG_CHARSET) !== "UTF-8") {
										$arSearchParams[$param] = iconv("UTF-8", "windows-1251", $arSearchParams[$param]);
									}
								}
								if($key == "SEARCH_FLAG" && strlen($arSearchParams[$param])) {
									$arSearchParams[$param] = GetMessage("yes_word");
								}
								$arSearchData[$key] = $arSearchParams[$param];
							}
						}
						break;
					}
				}
			}
			return $arSearchData;
		} else {
			return false;
		}
	}
	
	static function getSearchSystem() {
		$arSearchSystem = array(
			array(
				"NAME" => GetMessage("yandex_name"),
				"CODE" => "yandex",
				"HOSTS" => array("yandex.ru"),
				"PARAMS" => array(
					"SEARCH_QUERY" => "text",
					"SEARCH_REGION" => "lr",
					"SEARCH_FLAG" => "rstr"
				),
			),
			array(
				"NAME" => "Google",
				"CODE" => "google",
				"HOSTS" => array("google.com"),
				"PARAMS" => array(
					"SEARCH_QUERY" => "q",
					"SEARCH_REGION" => "",
					"SEARCH_FLAG" => ""
				),
			),
			array(
				"NAME" => "Rambler",
				"CODE" => "rambler",
				"HOSTS" => array("rambler.ru"),
				"PARAMS" => array(
					"SEARCH_QUERY" => "query",
					"SEARCH_REGION" => "",
					"SEARCH_FLAG" => ""
				),
			),
			array(
				"NAME" => "Mail.ru",
				"CODE" => "mail",
				"HOSTS" => array("mail.ru"),
				"PARAMS" => array(
					"SEARCH_QUERY" => "q",
					"SEARCH_REGION" => "",
					"SEARCH_FLAG" => ""
				),
			),
			array(
				"NAME" => "Bing",
				"CODE" => "bing",
				"HOSTS" => array("bing.com"),
				"PARAMS" => array(
					"SEARCH_QUERY" => "q",
					"SEARCH_REGION" => "",
					"SEARCH_FLAG" => ""
				),
			),
			array(
				"NAME" => GetMessage("yandex_market_name"),
				"CODE" => "yandex_market",
				"HOSTS" => array("market.yandex.ru"),
				"PARAMS" => array(
					"SEARCH_QUERY" => "text",
					"SEARCH_REGION" => "",
					"SEARCH_FLAG" => ""
				),
			),
		);
		return $arSearchSystem;
	}
}
?>