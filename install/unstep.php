<?IncludeModuleLangFile(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="hidden" name="id" value="pm.orderanalytics">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?echo CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>
	<p><?echo GetMessage("pm_may_save")?></p>
	<p><input type="checkbox" name="savedata" id="savedata" value="Y" checked><label for="savedata"><?echo GetMessage("pm_save_request")?></label></p>
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL")?>">
</form>