<?
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/promedia.orderanalytics/classes/general/pm.tools.php");

class promedia_orderanalytics extends CModule {
	var $MODULE_ID = "promedia.orderanalytics";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;

	public function __construct() {
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_NAME = GetMessage("pm_mod_author");
		$this->PARTNER_URI = "https://promedia.io";

		$this->MODULE_NAME = GetMessage("pm_mod_name");
		$this->MODULE_DESCRIPTION = GetMessage("pm_mod_desc");
	}

	public function DoInstall() {
		RegisterModule($this->MODULE_ID);
		
		$this->InstallDB();
		
		RegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'PromediaAnalytics', 'OnPageStartHandler');
		RegisterModuleDependences('sale', 'OnOrderSave', $this->MODULE_ID, 'PromediaAnalytics', 'OnOrderSaveHandler');
		RegisterModuleDependences('sale', 'OnOrderAdd', $this->MODULE_ID, 'PromediaAnalytics', 'OnOrderAddHandler');
		RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepComplete', $this->MODULE_ID, 'PromediaAnalytics', 'OnSaleComponentOrderOneStepCompleteHandler');
		
		$this->AddOrderProps();
	}

	public function DoUninstall() {
		global $APPLICATION, $step;
		$step = IntVal($step);
		
		if($step < 2)
			$APPLICATION->IncludeAdminFile("ITEES_ASO_INSTALL_TITLE", $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/promedia.orderanalytics/install/unstep.php");
		elseif($step == 2)
		{
			if(!array_key_exists("savedata", $_REQUEST) || ($_REQUEST["savedata"] != "Y"))
			{
				$this->DeleteOrderProps();
			}
		}
		
		UnRegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'PromediaAnalytics', 'OnPageStartHandler');
		UnRegisterModuleDependences('sale', 'OnOrderSave', $this->MODULE_ID, 'PromediaAnalytics', 'OnOrderSaveHandler');
		UnRegisterModuleDependences('sale', 'OnOrderAdd', $this->MODULE_ID, 'PromediaAnalytics', 'OnOrderAddHandler');
		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepComplete', $this->MODULE_ID, 'PromediaAnalytics', 'OnSaleComponentOrderOneStepCompleteHandler');
		
		UnRegisterModule($this->MODULE_ID);
	}
	
	function InstallDB() { 
		return true; 
	}
	
	private function AddOrderProps() {
		if(!CModule::IncludeModule("sale")) {
			return false;
		}
		
		$db_ptype = CSalePersonType::GetList(array("SORT" => "ASC"));
		$arPersonTypes = array();
		while($ptype = $db_ptype->Fetch()) {
			$arPersonTypes[] = $ptype;
		}		
		if(!count($arPersonTypes)) {
			return false;
		}
		
		$arOrderProps = PromediaAnalyticsTools::getOrder();
		
		if(count($arPersonTypes)) {
			$arPropsGroupId = array();
			foreach($arPersonTypes as $arPType) {
				$id = $this->CheckOrderPropsGroup($arPType["ID"]);
				if($id) {
					$newGroupId = $id;
				} else {
					$arGroupAddFields = array(
						"PERSON_TYPE_ID" => $arPType["ID"],
						"NAME" => GetMessage("pm_group_name"),
						"SORT" => "500"
					);
					$newGroupId = CSaleOrderPropsGroup::Add($arGroupAddFields);
				}
				if($newGroupId) {
					$arPropsGroupId[] = $newGroupId;
					$arCheckedOrderProps = $this->CheckOrderProps($arPType["ID"]);
					foreach($arOrderProps as $arProp) {
						if(in_array($arProp["CODE"], $arCheckedOrderProps))
							continue;
						$arOrderPropFields = array(
							"PERSON_TYPE_ID" => $arPType["ID"],
							"ACTIVE" => "Y",
							"NAME" => $arProp["NAME"],
							"TYPE" => $arProp["TYPE"],
							"REQUIED" => "N",
							"CODE" => $arProp["CODE"],
							"SORT" => $arProp["SORT"],
							"USER_PROPS" => "N",
							"PROPS_GROUP_ID" => $newGroupId,
							"IS_FILTERED" => "Y",
							"UTIL" => "Y",
						);
						$ID = CSaleOrderProps::Add($arOrderPropFields);
					}
				}
			}
			COption::SetOptionString($this->MODULE_ID, "pm_group_name", implode(",", $arPropsGroupId));
		}
	}
	
	private function CheckOrderPropsGroup($personType) {
		if(!CModule::IncludeModule("sale")) {
			return false;
		}
		
		if(!intval($personType))
			return false;
		
		$strPropsGroupId = COption::GetOptionString($this->MODULE_ID, "pm_group_name", "");
		if(strlen($strPropsGroupId)) {
			$arPropsGroupId = explode(",", $strPropsGroupId);
			$db_propsGroup = CSaleOrderPropsGroup::GetList(
				array("SORT" => "ASC"),
				array("PERSON_TYPE_ID" => $personType, "ID" => $arPropsGroupId),
				false,
				false,
				array()
			);
			if($propsGroup = $db_propsGroup->GetNext()) {
				return $propsGroup["ID"];
			}else{
				return false;
			}
		}
	}
	
	private function CheckOrderProps($personType) {
		if(!CModule::IncludeModule("sale")) {
			return false;
		}
		
		if(!intval($personType))
			return false;
		
		$arOrderProps = PromediaAnalyticsTools::getOrder();
		$arOrderPropsCodes = array();
		foreach($arOrderProps as $arProp) {
			$arOrderPropsCodes[] = $arProp["CODE"];
		}
		
		$db_props = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			array(
				"PERSON_TYPE_ID" => $personType,
				"CODE" => $arOrderPropsCodes,
				"ACTIVE" => "Y"
			),
			false,
			false,
			array()
		);
		$arCheckedProps = array();
		while($props = $db_props->GetNext()) {
			$arCheckedProps[] = $props["CODE"];
		}
		return $arCheckedProps;
	}
	
	private function DeleteOrderProps() {
		if(!CModule::IncludeModule("sale")) {
			return false;
		}
		$strPropsGroupId = COption::GetOptionString($this->MODULE_ID, "pm_group_name", "");
		$arPropsGroupId = explode(",", $strPropsGroupId);
		if(count($arPropsGroupId)) {
			foreach($arPropsGroupId as $groupId) {
				CSaleOrderPropsGroup::Delete($groupId);
			}
		}
	}
}
?>