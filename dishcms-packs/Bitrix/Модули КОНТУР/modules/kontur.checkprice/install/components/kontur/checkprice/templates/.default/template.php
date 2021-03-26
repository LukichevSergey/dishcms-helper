<?php 
/** @var \Kontur\CheckPrice\Component\CheckPrice $component */
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();

use Kontur\CheckPrice\Helper;

\Bitrix\Main\UI\Extension::load("ui.buttons");

$snapCount=$component->getSnapCount();
$lastSnap=$component->getLastSnap();

\Bitrix\Main\Page\Asset::getInstance()->addJs('https://code.jquery.com/jquery-3.5.1.min.js');
?>
<div class="js-kontur-checkprice-progress-box" style="display:none"></div>
<div class="js-kontur-checkprice-workarea">
    <? if(!$snapCount) {  ?>
        <? \CAdminMessage::ShowMessage('Необходимо создать базовый снимок цен'); ?>
        <? if(Helper::checkAccessByCreateSnap(true)) {  ?>
        <input type="button" class="adm-btn-save js-kontur-checkprice-action" 
            data-action="SNAP" value="Создать базовый снимок цен" /> 
        <? } ?>
    <? } /* elseif($snapCount === 1) { ?>
        <? \CAdminMessage::ShowMessage('Не найдено ни одного снимка цен для сравнения'); ?>
        <? if(Helper::checkAccessByCreateSnap(true)) {  ?>
        <p>Базовый снимок: <?= $lastSnap->getCreateTime()->format('d.m.Y H:i'); ?></p>
        <input type="button" class="adm-btn-save js-kontur-checkprice-action" 
            data-action="SNAP" value="Сделать снимок цен" /> 
        <? } ?>
    <? } */ ?>
    
    <div class="kontur-checkprice-list">
    <?
    $snaps=$component->getSnaps();
    if(count($snaps) > 0) {
        $i=0;
        $tabs=[];
        $tabData=[];
        
        // Актуальный снимок
        /*
        $currentSnap=array_shift($snaps);
        $tabHeading='Актуальный';
        $tabTitle='<div>Изменения цен ' 
                . date('d.m.Y') 
                . '<sup>' . date('H:i') . '</sup>'
                . ' / ' 
                . $currentSnap['CREATE_TIME']->format('d.m.Y') 
                . '<sup>' . $currentSnap['CREATE_TIME']->format('H:i') . '</sup>'
                . '<a target=\'_blank\' href=\'' . Helper::getPriceTagListPageUrl() . '\' class=\'ui-btn ui-btn-primary\'>Список ценников</a></div>';
        $tabs[]=$component->getAdminTab($tabHeading, $tabTitle);
        $tabData[]=['CURRENT'=>$currentSnap, 'NEXT'=>null];
        /**/
        // Ближайшие 10 снимков
        $currentSnap=array_shift($snaps);
        while(($nextSnap=array_shift($snaps)) && ($i++ < 10)) { 
            $tabHeading=$currentSnap['CREATE_TIME']->format('d.m.Y');
            $tabTitle='<div>Изменения цен ' 
                . $nextSnap['CREATE_TIME']->format('d.m.Y') 
                // . '<sup>' . $nextSnap['CREATE_TIME']->format('H:i') . '</sup>'
                . ' / ' 
                . $currentSnap['CREATE_TIME']->format('d.m.Y')
                . (Helper::checkAccessByCreateSnap(true) ? '<sup>#' . $currentSnap['ID'] . '</sup>' : '')
                // . '<sup>' . $currentSnap['CREATE_TIME']->format('H:i') . '</sup>'
                . '<a target=\'_blank\' href=\'' . Helper::getPriceTagListPageUrl() . '\' class=\'ui-btn ui-btn-primary\'>Список ценников</a></div>';
            $tabOnSelect="konturCheckPrice.checkTab({$currentSnap['ID']},{$nextSnap['ID']})";
            $tabs[]=$component->getAdminTab($tabHeading, $tabTitle, $tabOnSelect);
            $tabData[]=['CURRENT'=>$currentSnap, 'NEXT'=>$nextSnap];
            $currentSnap=$nextSnap;
        }

        // Дополнительные снимки
        if(!empty($snaps)) {
            $tabHeading='Еще...';
            $tabTitle='<div>Еще...<a target=\'_blank\' href=\'' . Helper::getPriceTagListPageUrl() . '\' class=\'ui-btn ui-btn-primary\'>Список ценников</a></div>';
            $tabs[]=$component->getAdminTab($tabHeading, $tabTitle);
            $snapHistoryData=[];
            while($nextSnap=array_shift($snaps)) {
                $snapHistoryData[]=['CURRENT'=>$currentSnap, 'NEXT'=>$nextSnap];
                $currentSnap=$nextSnap;
            }
            $tabData[]=['HISTORY'=>$snapHistoryData];
        }

        $tabControl=new \CAdminTabControl('snapTabControl', $tabs, false, true);
        $tabControl->Begin();
        $isFirstDataTab=true;
        foreach($tabData as $data) { 
            $tabControl->BeginNextTab();
            ?>
            <tr><td>
                <? if(!empty($data['HISTORY'])) { ?>
                    <table width="100%" class="kontur-chekcprice-table-snaps-list">
                        <tbody>
                        <? $odd=true; ?>
                        <? foreach($data['HISTORY'] as $historyData) { $trCssClass=$odd?'odd':'even'; ?>
                            <tr class="<?=$trCssClass?>">
                                <td>
                                    <?='Изменения цен ' 
                                    . $historyData['NEXT']['CREATE_TIME']->format('d.m.Y') 
                                    // . '<sup>' . $historyData['NEXT']['CREATE_TIME']->format('H:i') . '</sup>'
                                    . ' / ' 
                                    . $historyData['CURRENT']['CREATE_TIME']->format('d.m.Y') 
                                    // . '<sup>' . $historyData['CURRENT']['CREATE_TIME']->format('H:i') . '</sup>'; ?>
                                </td>
                                <td><a href="javascript:;" 
                                    data-snap1="<?=$historyData['CURRENT']['ID']?>" 
                                    data-snap2="<?=$historyData['NEXT']['ID']?>" 
                                    class="js-kontur-checkprice-btn-snapview">показать</a></td>
                            </tr>
                        <? $odd=!$odd; } ?>
                        </tbody>
                    </table>
                <? } else { ?>
                    <? if($isFirstDataTab) { $isFirstDataTab=false; ?>
                    	<? /* ?>
                        <div class="js-kontur-checkprice-data-loading">
                            <p>идет загрузка данных, пожалуйста, подождите...</p>
                        </div>
                        <script>setTimeout(function(){
                            konturCheckPrice.checkTab(<?=$data['CURRENT']['ID']?>,'');
                        }, 1000);</script>
                        <? /**/ ?>
                        <?
                        /**/
                        $APPLICATION->IncludeComponent('kontur:checkprice', 'snaplist', [
                            'ITEMS_IBLOCK_ID'  => $arParams['ITEMS_IBLOCK_ID'],
                            'OFFERS_IBLOCK_ID' => $arParams['OFFERS_IBLOCK_ID'],
                            'SNAP_1'=>$data['CURRENT']['ID'],
                            'SNAP_2'=>$data['NEXT']['ID']
                        ]);
                        /**/
                        ?>
                    <? } else { ?>
                        <div class="js-kontur-checkprice-data-loading">
                            <p>идет загрузка данных, пожалуйста, подождите...</p>
                        </div>                        
                    <? } ?>
                <? } ?>
            </td></tr>
            <?            
            $tabControl->EndTab();
        }
        $tabControl->End();
    }
    ?>
    </div>

    <? if(Helper::checkAccessByCreateSnap(true) && ($snapCount > 0)) {  ?>
        <div class="adm-info-message-wrap">
            <div class="adm-info-message">
                <div class="kontur-checkprice-create-snap-form">
                    <table width="100%">
                        <tr>
                            <td>
                                Снимки цен создаются автоматически с интервалом указанном в настройках агента,
                                но, при необходимости, можно создать снимок вручную:&nbsp;
                            </td>
                            <td width="10%">
                                <input type="button" class="adm-btn-save js-kontur-checkprice-action" 
                                data-action="SNAP" value="Сделать снимок цен" />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    <? } ?>
</div>
<script>var konturCheckPrice=new window.konturCheckPriceComponent(); konturCheckPrice.init(<?= \CUtil::PhpToJSObject([
    'delay'=>$component->getSnapDelay(),
    'limit'=>$component->getSnapLimit(),
    'sess'=>bitrix_sessid_get()
]); ?>);</script>