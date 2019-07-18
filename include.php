<?
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/promedia.orderanalytics/classes/general/pm.search.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/promedia.orderanalytics/classes/general/pm.user.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/promedia.orderanalytics/classes/general/pm.labels.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/promedia.orderanalytics/classes/general/pm.tools.php");

IncludeModuleLangFile(__FILE__);

class PromediaAnalytics {
	static $USER_COOKIE_NAME = "pm_user_cookie_data";
	static $MODULE_NAME = "promedia.orderanalytics";
	
	public function OnPageStartHandler() {
		$arUserData = array();
		$arUserData = self::GetUserCookieData();
		$obModuleUser = new PromediaAnalyticsUser($arUserData);
		
		$referer = $_SERVER["HTTP_REFERER"];
		
		$arParams = array();
		if($arSearchData = PromediaAnalyticsSearch::getSearchRequest($referer)) {
			$arParams = array_merge($arParams, $arSearchData);
			$arParams["REFERER"] = $referer;
		}
		if($arLabelData = PromediaAnalyticsHeaders::headerCheckValue()) {
			$arParams = array_merge($arParams, $arLabelData);
			$arParams["REFERER"] = $referer;
		}
		$arParams = PromediaAnalyticsTools::getUserData($arParams);
		
		if(!count($arParams)) {
			if(strlen($referer)) {
				if(self::CheckReferer($referer)) {
					$arParams["REFERER"] = $referer;
				}
			} else {
				$arParams["REFERER"] = GetMessage("pm_referer_lead");
				if(count($arUserData)) {
					if(strlen($arUserData["ADV_SERVICE"])) {
						$arParams["ADV_SERVICE"] = $arUserData["ADV_SERVICE"];
					}
					if(strlen($arUserData["ADV_CAMPAIGN"])) {
						$arParams["ADV_CAMPAIGN"] = $arUserData["ADV_CAMPAIGN"];
					}
					if(strlen($arUserData["ADV_AD"])) {
						$arParams["ADV_AD"] = $arUserData["ADV_AD"];
					}
					if(strlen($arUserData["ADV_SOURCE"])) {
						$arParams["ADV_SOURCE"] = $arUserData["ADV_SOURCE"];
					}
					if(strlen($arUserData["ADV_KW"])) {
						$arParams["ADV_KW"] = $arUserData["ADV_KW"];
					}
					if(strlen($arUserData["ADV_KEYWORDS"])) {
						$arParams["ADV_KEYWORDS"] = $arUserData["ADV_KEYWORDS"];
					}
					if(strlen($arUserData["ADV_POSITION"])) {
						$arParams["ADV_POSITION"] = $arUserData["ADV_POSITION"];
					}
				}
			}
		}
		
		if(count($arParams)) {
			$useHostAsReferer = COption::GetOptionString("promedia.orderanalytics", "itees_aso_use_host_as_referer", "");
			if(
				strlen($arParams["REFERER"]) && 
				$arParams["REFERER"] !== GetMessage("pm_referer_lead") &&
				strlen($useHostAsReferer)
			) {
				$arReferer = parse_url($arParams["REFERER"]);
				if(strlen($arReferer["host"])) {
					$arParams["REFERER"] = $arReferer["host"];
				}
			}
			$arUserData = $arParams;
			$obModuleUser->setUserParams($arUserData);
			self::SetUserCookieData($arUserData);
		}
		$GLOBALS["obModuleUser"] = $obModuleUser;
	}
	
	static function CheckReferer($referer) {
		if(!strlen($referer)) {
			return false;
		}
		$arReferer = parse_url($referer);
		
		$http_host = $_SERVER["HTTP_HOST"];
		$arHost = explode(":", $http_host);
		$http_host = $arHost[0];
		
		if($arReferer["host"] !== $http_host) {
			return true;
		}
		return false;
	}
	
	static function GetUserCookieData() {
		global $APPLICATION;
		$arUserData = array();
		$PM_USER_COOKIE_DATA = $APPLICATION->get_cookie(self::$USER_COOKIE_NAME);
		if(strlen($PM_USER_COOKIE_DATA)) {
			$arUserData = unserialize($PM_USER_COOKIE_DATA);
		} else {
			$arUserData = self::GetOldUserCookieData();
		}
		return $arUserData;
	}
	
	static function GetOldUserCookieData() {
		global $APPLICATION;
		
		$arUserData = array();
		$PM_USER_COOKIE_DATA = $APPLICATION->get_cookie("PromediaAnalytics");
		if(strlen($PM_USER_COOKIE_DATA)) {
			$arOldUserData = unserialize($PM_USER_COOKIE_DATA);
			foreach($arOldUserData as $key=>$data) {
				if($key == "SERVICE_ID") {
					$arUserData["ADV_SERVICE"] = $data;
				} elseif($key == "CAMPAIGN_ID") {
					$arUserData["ADV_CAMPAIGN"] = $data;
				} elseif($key == "ADV_ID") {
					$arUserData["ADV_AD"] = $data;
				} elseif($key == "SOURCE_ID") {
					$arUserData["ADV_SOURCE"] = $data;
				} elseif($key == "SEARCH_SERVICE") {
					$arUserData["SEARCH_ENGINE"] = $data;
				} elseif($key == "SEARCH_QUERY") {
					$arQueryData = explode(";", $data);
					foreach($arQueryData as $key=>$str) {
						$str = trim($str);
						if($key == 0) {			
							$arUserData["SEARCH_QUERY"] = substr($str, 8);
						} elseif($key == 1) {		
							$arUserData["SEARCH_REGION"] = substr($str, 8);
						} elseif($key == 2) {		
							$arUserData["SEARCH_FLAG"] = substr($str, 12);
						}
					}
				}
			}
		}
		return $arUserData;
	}
	
	static function SetUserCookieData($arUserData) {
		global $APPLICATION;
		
		$expire = COption::GetOptionString(self::$MODULE_NAME, "pm_expire_cookie", 30);
		
		$strUserData = serialize($arUserData);
		$APPLICATION->set_cookie(self::$USER_COOKIE_NAME, $strUserData, time()+60*60*24*$expire);
	}
	
	public function OnOrderAddHandler($ORDER_ID, $arFields) {
		PromediaAnalytics::SetOrderProps($ORDER_ID, $arFields);	
	}
	
	public function OnOrderSaveHandler($ORDER_ID, $arFields, $arOrder, $isNew) {
		PromediaAnalytics::SetOrderProps($ORDER_ID, $arFields);	
	}
	
	public function OnSaleComponentOrderOneStepCompleteHandler($ID, $arOrder) {
		PromediaAnalytics::SetOrderProps($ID, $arOrder);
	}
	
	function SetOrderProps($ORDER_ID, $arFields) {
		if(!CModule::IncludeModule("sale"))
			return false;
		global $obModuleUser;
		
		$arOrderProps = PromediaAnalyticsTools::getOrder();
		foreach($arOrderProps as $arProp) {
			$arOrderPropsCodes[] = $arProp["CODE"];
		}
		$db_props = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			array(
				"PERSON_TYPE_ID" => $arFields["PERSON_TYPE_ID"],
				"CODE" => $arOrderPropsCodes,
				"ACTIVE" => "Y"
			),
			false,
			false,
			array()
		);
		$arAllOrderProps = array();
		while($props = $db_props->GetNext()) {
			$arAllOrderProps[] = array(
				"ID" => $props["ID"],
				"PERSON_TYPE_ID" => $props["PERSON_TYPE_ID"],
				"CODE" => $props["CODE"],
				"NAME" => $props["NAME"],
			);
		}
		
		$arUserData = $obModuleUser->getUserParams();
		foreach($arAllOrderProps as $arProp) {
			$code = str_replace("pm_", "", $arProp["CODE"]);
			if(array_key_exists($code, $arUserData)) {
				$arPropFields = array(
					"ORDER_ID" => $ORDER_ID,
					"ORDER_PROPS_ID" => $arProp["ID"],
					"NAME" => $arProp["NAME"],
					"CODE" => $arProp["CODE"],
					"VALUE" => $arUserData[$code]
				);
				CSaleOrderPropsValue::Add($arPropFields);
			}
		}
	}
}
?>