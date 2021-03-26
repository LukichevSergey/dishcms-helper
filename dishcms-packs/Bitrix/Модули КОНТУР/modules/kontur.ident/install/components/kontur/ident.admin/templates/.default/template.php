<?php 
/** @var \Kontur\Ident\Component\Admin $component */
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();

use Bitrix\Main\Localization\Loc;
use Kontur\Ident\TicketTable;

Loc::loadMessages(__FILE__);

$tabControl=new \CAdminTabControl('identTabControl', [
    [
        'DIV'=>'tab_new_tickets', 
        'ICON'=>'iblock',
        'TAB'=>Loc::getMessage('NEW_TICKETS_TAB_TITLE'), 
        'TITLE'=>Loc::getMessage('NEW_TICKETS_TAB_TITLE')
    ],
    /*
    [
        'DIV'=>'tab_done_tickets', 
        'ICON'=>'iblock',
        'TAB'=>Loc::getMessage('DONE_TICKETS_TAB_TITLE'), 
        'TITLE'=>Loc::getMessage('DONE_TICKETS_TAB_TITLE')
    ],
    /**/
], false, true);

$fShowMessage=function($message) {
?>
<div class="adm-info-message-wrap">
    <div class="adm-info-message">
        <?= Loc::getMessage($message); ?>
    </div>
</div>
<?
};

$fShowTable=function($rs) {
    /** @var \Bitrix\Main\DB\Result $rs */
    $map=TicketTable::getMap();    
?>
<div class="kontur-ident-admin-tickets-table adm-list-table-wrap adm-list-table-without-header adm-list-table-without-footer">
    <div class="adm-list-table-top"></div>
    <table class="adm-list-table">
        <? if($rs->getSelectedRowsCount() > 0) { ?>
        <thead>
            <tr class="adm-list-table-header">
                <td class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">
                        <?= Loc::getMessage('TICKETS_TABLE_HEADER_DATE'); ?>
                    </div>
                </td>
                <th class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">
                        <?= Loc::getMessage('TICKETS_TABLE_HEADER_INFO'); ?>
                    </div>
                </td>
            </tr>
        </thead>
        <? } ?>
        <tbody>
            <? if($rs->getSelectedRowsCount() > 0) { ?>
                <? while($ticket=$rs->fetch()) { ?>
                    <tr class="adm-list-table-row">
                        <td class="adm-list-table-cell"><?= $ticket['DATE_AND_TIME']->format('d.m.Y H:i'); ?></td>
                        <td class="adm-list-table-cell adm-list-table-cell-last"><? 
                        foreach($ticket as $field=>$value) { 
                            if(empty($map[$field]) || in_array($field, ['ID', 'DATE_AND_TIME'])) continue;
                            if(empty($value)) continue;
                            ?>
                            <div class="ticket-info-row">
                                <span class="label"><?= $map[$field]->getTitle(); ?></span>
                                <span class="value"><?= nl2br(htmlspecialchars($value)); ?></span>
                            </div>
                            <?
                        } 
                        ?>
                        </td>
                    </tr>
                <? } ?>
            <? } else { ?>
                <tr class="adm-list-table-row">
                    <td colspan="2" class="adm-list-table-cell adm-list-table-cell-last">
                        <?= Loc::getMessage('TICKETS_TABLE_EMPTY'); ?>
                    </td>
                </tr>
            <? } ?>
        </tbody>
    </table>
</div>
<?
};

$tabControl->Begin();

$tabControl->BeginNextTab();
?><tr><td><?
$fShowMessage('NEW_TICKETS_NOTE');
$fShowTable($component->getNewTicketsDbResult());
?></td></tr><?
$tabControl->EndTab();

/*
$tabControl->BeginNextTab();
?><tr><td><?
$fShowMessage('DONE_TICKETS_NOTE');
$fShowTable($component->getDoneTicketsDbResult());
?></td></tr><?
$tabControl->EndTab();
/**/

$tabControl->End();