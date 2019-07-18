<?
IncludeModuleLangFile(__FILE__);

class PromediaAnalyticsUser {
	public $ADV_SERVICE = false;		
	public $ADV_CAMPAIGN = false;		
	public $ADV_AD = false;				
	public $ADV_SOURCE = false;			
	public $ADV_KW = false;				
	
	public $SEARCH_ENGINE = false;		
	public $SEARCH_QUERY = false;		
	public $SEARCH_REGION = false;		
	public $SEARCH_FLAG = false;		
	
	public $REFERER = false;			
	
	function __construct($arParams = array()) {
		if (count($arParams)) {
			$this->setUserParams($arParams);
		}
	}
	
	public function setUserParams($arParams) {
		if (count($arParams)) {
			$this->ADV_SERVICE = $arParams["ADV_SERVICE"];
			$this->ADV_CAMPAIGN = $arParams["ADV_CAMPAIGN"];
			$this->ADV_AD = $arParams["ADV_AD"];
			$this->ADV_SOURCE = $arParams["ADV_SOURCE"];
			$this->ADV_KW = $arParams["ADV_KW"];
			$this->ADV_KEYWORDS = $arParams["ADV_KEYWORDS"];
			$this->ADV_POSITION = $arParams["ADV_POSITION"];
			
			$this->SEARCH_ENGINE = $arParams["SEARCH_ENGINE"];
			$this->SEARCH_QUERY = $arParams["SEARCH_QUERY"];
			$this->SEARCH_REGION = $arParams["SEARCH_REGION"];
			$this->SEARCH_FLAG = $arParams["SEARCH_FLAG"];
			$this->REFERER = $arParams["REFERER"];
		}
	}
	
	public function getUserParams() {
		return get_object_vars($this);
	}
}
?>