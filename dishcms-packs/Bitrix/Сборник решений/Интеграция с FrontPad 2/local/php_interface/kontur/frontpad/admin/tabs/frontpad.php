<?php
use kontur\frontpad\FrontPad;

/**
 * Получить HTML код вариантов SELECT разделов сайта
 */
$fGetSelectOptions=function($sections, $selected=null, $empty='(не выбрано)', $depth=0) use (&$fGetSelectOptions) {
    $html='';
    if($empty) {
        $html.="<option value=\"\">{$empty}</option>";
    }
    
    foreach($sections as $section) {
        if(empty($section['CHILDS'])) {
            $prefix=($section['TYPE'] == FrontPad::SECTION_TYPE_IBLOCK) ? FrontPad::SECTION_TYPE_IBLOCK_PREFIX : '';
            $html.="<option value=\"{$prefix}{$section['ID']}\">" . str_repeat('&dash; ', $depth) . "{$section['NAME']}</option>";
        }
        else {
             $html.='<option disabled="disabled" value="">' . str_repeat('&dash; ', $depth) . "{$section['NAME']}</option>";
             $html.=$fGetSelectOptions($section['CHILDS'], $selected, null, $depth+1);
        }
    }
    
    return $html;
};

$sort=(isset($_REQUEST['s']) && ($_REQUEST['s'] == 'FPNAME')) ? 'NAME' : 'FRONTPAD_CODE';
$frontPadProducts=FrontPad::i()->getProducts($sort, true);
$bxFrontPadCodes=FrontPad::i()->getBxProductFrontPadCodes();

$tabControl=new \CAdminTabControl('tabControl', [FrontPad::i()->getAdminTab('Товары FrontPad')], false, true);
$tabControl->Begin();                
$tabControl->BeginNextTab();

?>
<tr><td><div style="height:700px;overflow:scroll">
    <table border="0" width="100%" class="frontpad__table">
        <thead>
            <tr>
                <th><a href="?s=FPCODE" title="Отсортировать по артикулу товара">Артикул<br/>FrontPad</a></th>
                <th><a href="?s=FPNAME" title="Отсортировать по наименованию товара">Наименование</a></th>
                <th><b>Цена</b></th>
                <th>
                    Раздел<br/>
                    <select name="DATA[NEW_PRODUCT][SECTION_DEFAULT]" title="Раздел по умолчанию для значения не выбрано">
                        <?= $fGetSelectOptions(FrontPad::i()->getSections()); ?>
                    </select>
                </th>
                <th>
                    <b style="display:block;margin-bottom:5px">Добавить товары</b>
                    <input type="checkbox" id="frontpad_tab_frontpad_checkall" value="" title="Отметить/Снять все товары" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="frontpad_tab_frontpad_checkall" title="Отметить/Снять все товары"></label>
                </th>
            </tr>
        </thead>
        <tbody>
            <? foreach($frontPadProducts as $code=>$product):?>
                <tr>
                    <td align="center"><?= $product['FRONTPAD_CODE']?></td>
                    <td><?= $product['NAME']; ?></td>                                    
                    <td align="center"><?= $product['PRICE']; ?> руб.</td>
                    <td align="center">
                    	<?php if(in_array($code, $bxFrontPadCodes)): ?>
                    	&nbsp;
                    	<?php else: ?>
                        <select name="DATA[NEW_PRODUCT][PRODUCTS][<?=$code?>][SECTION]">
                            <?= $fGetSelectOptions(FrontPad::i()->getSections(), $code); ?>
                        </select>
                        <?php endif; ?>
                    </td>
                    <td align="center">
                    	<?php if(in_array($code, $bxFrontPadCodes)): ?>
                    	добавлено
                    	<?php else: ?>
                        <input 
                            type="checkbox" 
                            name="DATA[NEW_PRODUCT][PRODUCTS][<?=$code?>][ADD]" 
                            id="DATA_NEW_PRODUCT_PRODUCTS_<?=$code?>" 
                            value="<?=$code?>" 
                            title="Добавить" 
                            class="adm-designed-checkbox"
                        />
                        <label class="adm-designed-checkbox-label" for="DATA_NEW_PRODUCT_PRODUCTS_<?=$code?>" title="Добавить"></label>
                        <?php endif; ?>
                    </td>
                </tr>
            <?endforeach;?>
        </tbody>                        
    </table>
</div></td></tr>
<?php
$tabControl->EndTab();
$tabControl->End();
?>
