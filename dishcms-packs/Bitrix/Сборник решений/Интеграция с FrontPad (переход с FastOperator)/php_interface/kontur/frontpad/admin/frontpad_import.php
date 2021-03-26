<?
/** @global CMain $APPLICATION */
use \kontur\frontpad\FrontPad;

require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");

set_time_limit(0);

$APPLICATION->SetTitle('Импорт товаров и обновление цен из сервиса FrontPad');

require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$imported = FrontPad::import();

$tmpSiteProducts = FrontPad::getSiteProductsBySections(FrontPad::PRODUCTS_IBLOCK_ID);
$siteSections = $tmpSiteProducts[0];
$siteProducts = $tmpSiteProducts[1];
$ingridientyProducts = FrontPad::getSiteProducts(FrontPad::INGRIDIENTY_IBLOCK_ID);
$ingridientyFrontPadValues = FrontPad::getFrontPadPropertyEnumValues(FrontPad::INGRIDIENTY_IBLOCK_ID, FrontPad::INGRIDIENTY_PROPERTY_FRONTPAD_CODE_ID);
$frontPadProducts = FrontPad::getProducts();
$frontPadValues = FrontPad::getFrontPadPropertyEnumValues(FrontPad::PRODUCTS_IBLOCK_ID);
$siteProductSections = FrontPad::getSiteSections(FrontPad::PRODUCTS_IBLOCK_ID);
$siteIngridientySections = FrontPad::getSiteSections(FrontPad::INGRIDIENTY_IBLOCK_ID);
$siteProductPrices = [];
$siteProductSectionsByFrontPadCode = [];
$batterThinPriceCode=FrontPad::getBatterPriceCode(FrontPad::INGRIDIENTY_PROPERTY_IS_BATTER_THIN_VALUE_ID);
$batterFatPriceCode=FrontPad::getBatterPriceCode(FrontPad::INGRIDIENTY_PROPERTY_IS_BATTER_FAT_VALUE_ID);
?>
<? if(is_array($imported)): ?>
    <? 
    \CAdminMessage::ShowMessage([
        'TYPE'=>'OK', 
        'MESSAGE'=>"Процесс иморта товаров успешно завершен
            Обновлено: {$imported[1]}
            Обновлено цен: {$imported[2]}
            Добавлено: {$imported[0]}
            Обновлено начинок: {$imported[3]}
            Обновлено цен начинок: {$imported[4]}"
    ]); ?>
<? endif; ?>
<style>table.frontpad__products > tbody > tr:nth-child(odd){background:#d0d9dc;}</style>
<script>BX.ready(function(){
    BX.bind(BX('frontpad_products_checkall'), 'click', function() {
        var checkAll = BX('frontpad_products_checkall').checked;
        document.querySelectorAll("[name*='FRONTPAD_IMPORT_DATA[NEW_PRODUCT]']").forEach(function(ch){
            ch.checked = checkAll;
        });
    });
});</script>
<div class="form">
    <form method="post">
        <input type="hidden" name="RUN" value="Y" />
        <table border="0">
            <tr>
                <td width="50%" valign="top">
                <?
                $aTabs = [];
                foreach($siteSections as $sectionId=>$section) {
                    $aTabs[] = [
                        "DIV" => "frontpadimport_tab_" . $sectionId,
                        "TAB" => $section['NAME'],
                        "ICON" => "iblock",
                        "TITLE" => $section['NAME'],
                    ];
                }
                $aTabs[] = [
                    "DIV" => "frontpadimport_tab_ingridienty",
                    "TAB" => 'Начинки',
                    "ICON" => "iblock",
                    "TITLE" => 'Начинки',
                ];
                $tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
                $tabControl->Begin();
                foreach($siteSections as $sectionId=>$section):
                    $tabControl->BeginNextTab();
                    ?>
                    <tr><td>        		        
                    <div style="height:700px;overflow:scroll">
                        <table border="0" class="frontpad__products" width="100%">
                            <tbody>
                                <tr>
                                    <td style="text-align:center;padding:5px 0 3px;"><b>ИД</b></td>
                                    <td style="text-align:center;padding:5px 0 3px;"><b>Наименование</b></td>
                                    <td style="text-align:center;padding:5px 0 3px;"><b>Цены / Артикул FrontPad</b></td>
                                </tr>
                                <?foreach($siteProducts[$sectionId] as $id=>$product):?>
                                    <tr>
                                        <td><?= $product['ID']; ?></td>
                                        <td><?= $product['NAME']; ?></td>
                                        <td>
                                            <?if(!empty($product['PROPERTY_FO_PRICE_VALUE'])) {?>
                                            <table width="100%" border="1">
                                                <?foreach($product['PROPERTY_FO_PRICE_VALUE'] as $id=>$price) { 
                                                    $xmlId=$price['CODE']; 
                                                    if(isset($frontPadValues[$xmlId]['VALUE'])) {
                                                        $siteProductPrices[]=$frontPadValues[$xmlId]['VALUE']; 
                                                        $siteProductSectionsByFrontPadCode[$frontPadValues[$xmlId]['VALUE']]=$section['NAME'];
                                                    }
                                                    ?>
                                                <tr>
                                                    <td>
                                                        <?=$price['PRICE']?> руб. 
                                                        <?php
                                                        $html=''; 
                                                        foreach(['WEIGHT'=>'Вес', 'SIZE'=>'Размер', 'THICK'=>'Толщина'] as $priceProp=>$pricePropLabel) {
                                                            if(!empty($price[$priceProp])) {
                                                                $html .= ($html?', ':'') . "{$pricePropLabel}: {$price[$priceProp]}";
                                                            }
                                                        }
                                                        if((!empty($html))) {
                                                            echo "<small>({$html})</small>";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input title="Артикул FrontPad" 
                                                            type="text" 
                                                            name="FRONTPAD_IMPORT_DATA[FRONTPAD_CODE][<?=$product['ID']?>][<?=$id?>][<?=$price['CODE']?>]" 
                                                            value="<? if(isset($frontPadValues[$xmlId])) { echo $frontPadValues[$xmlId]['VALUE']; } ?>" 
                                                            style="vertical-align:top;margin-top:3px;"                                                        
                                                        />
                                                        <? if($sectionId == FrontPad::SECTION_PIZZA_ID): ?>
                                                        <?php $combo=@unserialize($price['PARTS']); ?>
                                                        <div class="batter__block" style="display:inline-block;">
                                                        	<div style="margin-bottom:2px;">
                                                            	<?php $checkboxId="FRONTPAD_IMPORT_DATA_FRONTPAD_CODE_BATTER_THIN_{$product['ID']}_{$id}_{$price['CODE']}"; ?>
                                                            	<input 
                                                                    type="checkbox" 
                                                                    name="FRONTPAD_IMPORT_DATA[IS_BATTER_THIN][<?=$product['ID']?>][<?=$id?>][<?=$price['CODE']?>]" 
                                                                    id="<?= $checkboxId; ?>" 
                                                                    value="<?= $batterThinPriceCode; ?>" 
                                                                    title="Тонкое тесто" 
                                                                    class="adm-designed-checkbox"
                                                                    <?php if(!empty($combo['Combo']) && in_array($batterThinPriceCode, $combo['Combo'])) echo ' checked="checked"'; ?>
                                                                />
                                                                <label class="adm-designed-checkbox-label" for="<?= $checkboxId; ?>" style="min-width:100px;padding-left:20px;">Тонкое тесто</label>
                                                            </div>
                                                            <div>
                                                                <?php $checkboxId="FRONTPAD_IMPORT_DATA_FRONTPAD_CODE_BATTER_FAT_{$product['ID']}_{$id}_{$price['CODE']}"; ?>
                                                                <input 
                                                                    type="checkbox" 
                                                                    name="FRONTPAD_IMPORT_DATA[IS_BATTER_FAT][<?=$product['ID']?>][<?=$id?>][<?=$price['CODE']?>]" 
                                                                    id="<?= $checkboxId; ?>" 
                                                                    value="<?= $batterFatPriceCode; ?>" 
                                                                    title="Толстое тесто" 
                                                                    class="adm-designed-checkbox"
                                                                    <?php if(!empty($combo['Combo']) && in_array($batterFatPriceCode, $combo['Combo'])) echo ' checked="checked"'; ?>
                                                                />
                                                                <label class="adm-designed-checkbox-label" for="<?= $checkboxId; ?>" style="min-width:100px;padding-left:20px;">Толстое тесто</label>
                                                            </div>
                                                        </div>
                                                        <? endif; ?>
                                                    </td>
                                                </tr>
                                                <?}?>
                                            </table>
                                            <?}?>
                                        </td>
                                    </tr>
                                <?endforeach;?>
                            </tbody>
                        </table>
                        </div>
                        </td></tr>
                        <?php
                        $tabControl->EndTab();
                    endforeach;
                    $tabControl->BeginNextTab();
                    ?>
                    <tr><td>
                	<div style="height:700px;overflow:scroll">
                    <table border="0" class="frontpad__products" width="100%">
                        <tbody>
                            <tr>
                                <td style="text-align:center;padding:5px 0 3px;"><b>ИД</b></td>
                                <td style="text-align:center;padding:5px 0 3px;"><b>Наименование</b></td>
                                <td style="text-align:center;padding:5px 0 3px;"><b>Цены / Артикул FrontPad</b></td>
                            </tr>
                            <?foreach($ingridientyProducts as $id=>$product):?>
                                <tr>
                                    <td><?= $product['ID']; ?></td>
                                    <td><?= $product['NAME']; ?></td>
                                    <td>
                                    	<?php $isBatter=!empty($product['PROPERTY_IS_BATTER_VALUE']); ?>
                                        <?if(!empty($product['PROPERTY_FO_PRICE_VALUE'])) {?>
                                        <table width="100%" border="1">
                                            <?foreach($product['PROPERTY_FO_PRICE_VALUE'] as $id=>$price) { 
                                                $xmlId=$price['CODE'];
                                                if(isset($ingridientyFrontPadValues[$xmlId]['VALUE'])) {
                                                    $siteProductPrices[]=$ingridientyFrontPadValues[$xmlId]['VALUE'];
                                                    $siteProductSectionsByFrontPadCode[$ingridientyFrontPadValues[$xmlId]['VALUE']]='Начинки';
                                                }
                                                ?>
                                            <tr>
                                                <td>
                                                	<?php if($isBatter) echo "<b>Установлено как \"{$product['PROPERTY_IS_BATTER_VALUE']}\"</b> - "; ?>
                                                    <?=$price['PRICE']?> руб.
                                                    <?php
                                                    $html=''; 
                                                    foreach(['WEIGHT'=>'Вес', 'SIZE'=>'Размер', 'THICK'=>'Толщина'] as $priceProp=>$pricePropLabel) {
                                                        if(!empty($price[$priceProp])) {
                                                            $html .= ($html?', ':'') . "{$pricePropLabel}: {$price[$priceProp]}";
                                                        }
                                                    }
                                                    if((!empty($html))) {
                                                        echo "<small>({$html})</small>";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input title="Артикул FrontPad" 
                                                        type="text" 
                                                        name="FRONTPAD_IMPORT_DATA_INGRIDIENTY[FRONTPAD_CODE][<?=$product['ID']?>][<?=$id?>][<?=$price['CODE']?>]" 
                                                        value="<? if(isset($ingridientyFrontPadValues[$xmlId])) { echo $ingridientyFrontPadValues[$xmlId]['VALUE']; } ?>"                                                         
                                                    />
                                                </td>
                                            </tr>
                                            <?}?>
                                        </table>
                                        <?}?>
                                    </td>
                                </tr>
                            <?endforeach;?>
                        </tbody>
                    </table>
                    </div>
                    </td></tr>
                    <?php
                    $tabControl->EndTab();
                    $tabControl->End();
                    ?>                    
                </td>
                <td width="1%" sstyle="background:#d1dadd;">
                    &nbsp;
                </td>
                <td width="50%" valign="top">
                <?
                $aTabs = array(
                    array(
                        "DIV" => "edit1",
                        "TAB" => 'Товары FrontPad',
                        "ICON" => "iblock",
                        "TITLE" => 'Товары FrontPad',
                    ),
                );
                $tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
                $tabControl->Begin();                
                $tabControl->BeginNextTab();
                ?><tr><td>
                    <div style="height:700px;overflow:scroll">
                        <? 
                        $sectionSelectOptions='<optgroup label="Меню">';
                        foreach($siteProductSections as $sectionId=>$sectionName) {
                            $sectionSelectOptions.='<option value="'.$sectionId.'">'.$sectionName.'</option>';
                        }
                        $sectionSelectOptions.='</optgroup><optgroup label="Начинки">';
                        $sectionSelectOptions.='<option value="'.FrontPad::INGRIDIENTY_SECTION_KEY.'">Начинки</option>';
                        foreach($siteIngridientySections as $sectionId=>$sectionName) {
                            $sectionSelectOptions.='<option value="'.$sectionId.'">'.$sectionName.'</option>';
                        }
                        $sectionSelectOptions.='</optgroup>';
                        $sectionSelectOptions.='<optgroup label="Дополнительно">
                        	<option value="IS_THIN_BATTER">Тонкое тесто</option>
                        	<option value="IS_FAT_BATTER">Толстое тесто</option>
                        </optgroup>';
                        ?>
                    <table border="0" width="100%" class="frontpad__products">
                        <tbody>
                            <tr>
                                <td align="center" style="text-align:center;padding:5px 0 3px;"><a href="?s=FPCODE" title="Отсортировать по артикулу товара"><b>Артикул<br/>FrontPad</b></a></td>
                                <td align="center" style="text-align:center;padding:5px 0 3px;"><a href="?s=FPNAME" title="Отсортировать по наименованию товара"><b>Наименование</b></a></td>
                                <td align="center" style="text-align:center;padding:5px 0 3px;"><b>Цена</b></td>
                                <td align="center" style="text-align:center;padding:5px 0 3px;">
                                    <b>Раздел</b><br/>
                                    <select name="FRONTPAD_IMPORT_DATA[NEW_PRODUCT_SECTION_DEFAULT]" title="Раздел по умолчанию для значения не выбрано">
                                        <option value="">(не выбрано)</option>
                                        <?=$sectionSelectOptions?> 
                                    </select>
                                </td>
                                <td align="center" style="text-align:center;padding:5px 0 3px;"><b style="display:block;margin-bottom:5px">Добавить товары</b>
                                <input type="checkbox" id="frontpad_products_checkall" value="" title="Отметить/Снять все товары" class="adm-designed-checkbox">
                                <label class="adm-designed-checkbox-label" for="frontpad_products_checkall" title="Отметить/Снять все товары"></label>
                                </td>
                            </tr>
                            <?
                            $codes = array_map(function($item){return $item['VALUE'];}, $frontPadValues + $ingridientyFrontPadValues);
                            if(isset($_GET['s'])) {
                                $sortField = ($_GET['s'] == 'FPCODE') ? 'FRONTPAD_CODE' : 'NAME';
                                uasort($frontPadProducts, function($a, $b) use ($sortField) {
                                    return strcmp($a[$sortField], $b[$sortField]);
                                });
                            }
                            foreach($frontPadProducts as $id=>$product):?>
                                <tr>
                                    <td align="center"><?= $product['FRONTPAD_CODE']?></td>
                                    <td><?= $product['NAME']; ?></td>                                    
                                    <td align="center"><?= $product['PRICE']; ?> руб.</td>
                                    <td align="center">
                                    	<? if(in_array($product['FRONTPAD_CODE'], $codes) && in_array($product['FRONTPAD_CODE'], $siteProductPrices)):?>
                                    		<?php 
                                    		if(isset($siteProductSectionsByFrontPadCode[$product['FRONTPAD_CODE']])) echo $siteProductSectionsByFrontPadCode[$product['FRONTPAD_CODE']];
                                    		else echo '&nbsp;';
                                    		?>
                                    	<?php else: ?>
                                        <select name="FRONTPAD_IMPORT_DATA[NEW_PRODUCT_SECTION][<?=$id?>]">
                                            <option value="">(не выбрано)</option>
                                            <?=$sectionSelectOptions?> 
                                        </select>
                                        <?php endif; ?>
                                    </td>
                                    <td align="center">
                                        <? if(in_array($product['FRONTPAD_CODE'], $codes) && in_array($product['FRONTPAD_CODE'], $siteProductPrices)):?>
                                            есть
                                        <? else: ?>
                                        <input 
                                            type="checkbox" 
                                            name="FRONTPAD_IMPORT_DATA[NEW_PRODUCT][<?=$id?>]" 
                                            id="FRONTPAD_IMPORT_NEW_PRODUCT_<?=$product['FRONTPAD_CODE']?>" 
                                            value="<?=$product['FRONTPAD_CODE']?>" 
                                            title="Добавить" 
                                            class="adm-designed-checkbox"
                                        />
                                        <label class="adm-designed-checkbox-label" for="FRONTPAD_IMPORT_NEW_PRODUCT_<?=$product['FRONTPAD_CODE']?>" title="Добавить"></label>
                                        <? endif; ?>
                                    </td>
                                </tr>
                            <?endforeach;?>
                        </tbody>
                    </table>
                    </div>
                    </td></tr>
                    <?php
                    $tabControl->EndTab();
                    $tabControl->End();
                    ?>
                </td>
            </tr>
        </table>
        <br/>
        <input type="submit" class="adm-btn-save" value="Импортировать / Обновить" />
    </form>
</div>

<?require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
