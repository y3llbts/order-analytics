<?
IncludeModuleLangFile(__FILE__);

class PromediaAnalyticsTools {
	public function getOrder() {
		$arOrderProps = array(
			array("CODE" => "pm_service", "NAME" => GetMessage("pm_service"), "TYPE" => "TEXT", "SORT" => "100"),
			array("CODE" => "pm_campaign", "NAME" => GetMessage("pm_campaign"), "TYPE" => "TEXT", "SORT" => "200"),
			array("CODE" => "pm_ad", "NAME" => GetMessage("pm_ad"), "TYPE" => "TEXT", "SORT" => "300"),
			array("CODE" => "pm_source", "NAME" => GetMessage("pm_source"), "TYPE" => "TEXT", "SORT" => "400"),
			array("CODE" => "pm_kw", "NAME" => GetMessage("pm_kw"), "TYPE" => "TEXT", "SORT" => "500"),
			array("CODE" => "pm_keyword", "NAME" => GetMessage("pm_keyword"), "TYPE" => "TEXT", "SORT" => "510"),
			array("CODE" => "pm_pos", "NAME" => GetMessage("pm_pos"), "TYPE" => "TEXT", "SORT" => "520"),
			array("CODE" => "pm_search_system", "NAME" => GetMessage("pm_search_system"), "TYPE" => "TEXT", "SORT" => "600"),
			array("CODE" => "pm_search_query", "NAME" => GetMessage("pm_search_query"), "TYPE" => "TEXT", "SORT" => "700"),
			array("CODE" => "pm_region", "NAME" => GetMessage("pm_region"), "TYPE" => "TEXT", "SORT" => "800"),
			array("CODE" => "pm_search_flag", "NAME" => GetMessage("pm_search_flag"), "TYPE" => "TEXT", "SORT" => "900"),
			array("CODE" => "pm_referer", "NAME" => GetMessage("pm_referer"), "TYPE" => "TEXT", "SORT" => "1000"),
		);
		return $arOrderProps;
	}
	
	public function getUserData($arParams) {
		if(!is_array($arParams) || !count($arParams))
			return array();
		
		$arCheckedParams = array();
		foreach($arParams as $key=>$param){
			$param = trim($param);
			if(strlen($param))
				$arCheckedParams[$key] = $param;
		}
		return $arCheckedParams;
	}
}
?>