<?
CModule::IncludeModule("promedia.orderanalytics");
$CAT_RIGHT = $APPLICATION->GetGroupRight("promedia.orderanalytics");
if ($CAT_RIGHT < "R")
	return;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$arAllOptions = Array(
	array("pm_expire_cookie", GetMessage("pm_expire_cookie"), 30, array("text", 4)),
	array("itees_aso_order_props_group_id", "group_id", "", array("hidden", 20)),
	array("itees_aso_use_host_as_referer", GetMessage("pm_referer_lead"), "", array("checkbox")),
);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "ib_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
	
	
if ($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) {
    if (strlen($RestoreDefaults)>0){
        foreach($arAllOptions as $arOption){
			if($arOption[3][0] == "hidden")
				continue;
				
			COption::RemoveOption("promedia.orderanalytics", $arOption[0]);
		}
    } else {
        foreach ($arAllOptions as $arOption) {
            $name=$arOption[0];
            $val=$_REQUEST[$name];
            COption::SetOptionString("promedia.orderanalytics", $name, $val, $arOption[1]);
        }
    }
    if (strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
        LocalRedirect($_REQUEST["back_url_settings"]);
    else
        LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}


$tabControl->Begin();
?>
<form method="post" action="<?php echo $APPLICATION->GetCurPage();?>?mid=<?php echo urlencode($mid);?>&amp;lang=<?echo LANGUAGE_ID;?>">
<?$tabControl->BeginNextTab();?>
    <?
    foreach ($arAllOptions as $arOption):
        $val = COption::GetOptionString("promedia.orderanalytics", $arOption[0], $arOption[2]);
        $type = $arOption[3];
    ?>
	<?if($type[0] == "hidden"){?>
		<input type="hidden" size="<?php echo $type[1];?>" maxlength="255" value="<?php echo htmlspecialchars($val);?>" name="<?php echo htmlspecialchars($arOption[0]);?>">
		<?
		continue;
	}?>
    <tr>
		<td valign="top" width="50%">
            <?echo $arOption[1];?>:
        </td>
        <td valign="top" width="50%">
            <?if ($type[0]=="text"):?>
                <input type="text" size="<?php echo $type[1];?>" maxlength="255" value="<?php echo htmlspecialchars($val);?>" name="<?php echo htmlspecialchars($arOption[0]);?>">
            <?elseif ($type[0]=="textarea"):?>
                <textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($arOption[0])?>"><?echo htmlspecialchars($val)?></textarea>
            <?elseif ($type[0]=="checkbox"):?>
            	<input type="checkbox" name="<?echo htmlspecialchars($arOption[0])?>" <? if ($val) echo "checked"?>/>
            <?elseif($type[0]=="selectbox"):?>
				<select name="<?echo htmlspecialchars($arOption[0])?>" id="<?echo htmlspecialchars($arOption[0])?>">
				<?foreach($arOption[4] as $v => $k){
					?><option value="<?=$v?>"<?if($val==$v)echo" selected";?>><?=$k?></option><?
				}?>
				</select>
			<?endif?>
        </td>
    </tr>
    <?endforeach?>

<?$tabControl->Buttons();?>
    <input type="submit" name="Update" value="<?php echo GetMessage("MAIN_SAVE");?>" title="<?php echo GetMessage("MAIN_OPT_SAVE_TITLE");?>">
    <input type="submit" name="Apply" value="<?php echo GetMessage("MAIN_OPT_APPLY");?>" title="<?php echo GetMessage("MAIN_OPT_APPLY_TITLE");?>">
    <?if (strlen($_REQUEST["back_url_settings"])>0):?>
        <input type="button" name="Cancel" value="<?php echo GetMessage("MAIN_OPT_CANCEL");?>" title="<?php echo GetMessage("MAIN_OPT_CANCEL_TITLE");?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
        <input type="hidden" name="back_url_settings" value="<?php echo htmlspecialchars($_REQUEST["back_url_settings"]);?>">
    <?endif?>
    <input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
    <?php echo bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>