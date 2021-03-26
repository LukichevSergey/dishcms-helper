<?php
use kontur\frontpad\FrontPad;

$fTabs=function(&$tabs, &$tabSections, $sections, $path=null) use (&$fTabs) { 
    static $tabIndex=0;
    foreach($sections as $section) {
        if(!empty($section['CHILDS'])) {
            $fTabs($tabs, $tabSections, $section['CHILDS'], "{$path}{$section['NAME']} / ");
        }
        else {
            $filter=[];
            if($section['TYPE'] == FrontPad::SECTION_TYPE_IBLOCK) {
                $filter['IBLOCK_ID']=$section['ID'];
            }
            elseif($section['TYPE'] == FrontPad::SECTION_TYPE_SECTION) {
                $filter['IBLOCK_ID']=$section['IBLOCK_ID'];
                $filter['SECTION_ID']=$section['ID'];
            }
            
            if(!empty($filter)) {
                $section['PRODUCTS']=FrontPad::i()->getBxProducts($filter);
                
                if(!empty($section['PRODUCTS'])) {
                    $tabs[$tabIndex]=FrontPad::i()->getAdminTab($path . $section['NAME']);
                    $tabSections[$tabIndex]=$section;
                    $tabIndex++;
                }
            }
        }
    }    
};

$tabs=[];
$tabSections=[];
$fTabs($tabs, $tabSections, FrontPad::i()->getSections()); 

$tabControl = new \CAdminTabControl('tabControl', $tabs, false, true);
$tabControl->Begin();
foreach($tabSections as $section):
    $tabControl->BeginNextTab();
    ?>
    <tr><td><div style="height:700px;overflow:scroll">
        <table border="0" class="frontpad__table" width="100%">
            <thead>
                <tr>
                    <th>ИД</th>
                    <th>Товар</th>
                </tr>
            </thead>
            <tbody>
            <?
            $properties=FrontPad::i()->getProperties();
            $priceProperties=FrontPad::i()->getPriceProperties();
            foreach($section['PRODUCTS'] as $product):
                ?>
                <tr>
                    <td valign="top"><?= $product['ID']; ?></td>
                    <td>
                        <input type="hidden" name="DATA[PRODUCTS][<?=$product['ID']?>][IBLOCK_ID]" value="<?= $product['IBLOCK_ID']; ?>" />
                        <div class="product__title">
                            <?= $product['NAME']; ?>
                            <? if($frontPadPropertyCode=FrontPad::i()->getFrontPadPropertyCode($product['IBLOCK_ID'])): ?>
                                <input 
                                    type="text" 
                                    name="DATA[PRODUCTS][<?=$product['ID']?>][PROPERTIES][<?=$frontPadPropertyCode?>]" 
                                    size="14" 
                                    style="float:right" 
                                    placeholder="Артикул FrontPad"
                                    <? if(!empty($product["PROPERTY_{$frontPadPropertyCode}_VALUE"])): ?>
                                        value="<?=$product["PROPERTY_{$frontPadPropertyCode}_VALUE"]?>"
                                    <? endif; ?>
                                />
                            <? endif; ?>
                            <? if(!empty($priceProperties[(int)$product['IBLOCK_ID']]['PRICE'])): ?>
                                <? $pricePropertyCode=$priceProperties[(int)$product['IBLOCK_ID']]['PRICE']; ?>
                                <input 
                                    type="text" 
                                    readonly="readonly" 
                                    size="8" 
                                    style="float:right;margin-right:5px;opacity:1;background:#f5f9f9;color:#979999;" 
                                    placeholder="Цена"
                                    <? if(!empty($product["PROPERTY_{$pricePropertyCode}_VALUE"])): ?>
                                        value="<?=$product["PROPERTY_{$pricePropertyCode}_VALUE"]?> руб"
                                    <? endif; ?>
                                />
                            <? endif; ?>
                        </div>
                        <? if(!empty($properties[(int)$product['IBLOCK_ID']])): ?>
                            <? foreach($properties[(int)$product['IBLOCK_ID']] as $code=>$prop): ?>
                                <? if(!empty($prop['USER_TYPE_DATA'])): ?>
                                    <?= call_user_func_array($prop['USER_TYPE_DATA']['GetPropertyFieldHtml'], [
                                        $prop,
                                        ['VALUE'=>empty($product["{$prop['SELECT_VALUE']}_VALUE"]) ? [] : $product["{$prop['SELECT_VALUE']}_VALUE"]],
                                        ['VALUE'=>"DATA[PRODUCTS][{$product['ID']}][PROPERTIES][{$code}]", 'MODE'=>'EDIT_FORM'],
                                        ['PRICE']
                                    ]); ?>
                                <? endif; ?>
                            <? endforeach; ?>
                        <? endif; ?>
                    </td>
                </tr>
                <?
            endforeach;
            ?>
            </tbody>
        </table>
    </div></td></tr><?
    $tabControl->EndTab();        
endforeach;
$tabControl->End();
?>




<?
/*


$fSetTabs=function(&$aTabs, $sections) {
    foreach($sections as $section) {
        FrontPad::i()->getAdminTab($section);
    }
}

$aTabs = [];
$fSetTabs($aTabs, FrontPad::i()->getSections());

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
<? /**/ ?>
