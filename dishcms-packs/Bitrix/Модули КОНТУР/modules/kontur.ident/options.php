<?php
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;
use Kontur\Ident\Helper;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();
$moduleId = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

if(!Loader::includeModule('kontur.ident')) return;
if(!$USER->IsAdmin()) return;

$aTabs = [
    [
        'DIV' => 'kontur_ident_main_tab',
        'TAB' => Loc::getMessage('KONTUR_IDENT_OPTION_TAB_MAIN'),
        'OPTIONS' => [
            Loc::getMessage('KONTUR_IDENT_OPTION_SECTION_AUTH'),
            [
                'auth_key',
                Loc::getMessage('KONTUR_IDENT_OPTION_AUTH_KEY'),
                null,
                ['text', 52],
            ],
            ['note'=>Loc::getMessage('KONTUR_IDENT_OPTION_AUTH_KEY_NOTE', ['#RANDOM_KEY#'=>Helper::guid()])]
        ]
    ]
];

if ($request->isPost() && !!$request['save'] && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        __AdmSettingsSaveOptions($moduleId, $aTab['OPTIONS']);
    }

    LocalRedirect((new Uri($APPLICATION->GetCurPage()))->addParams([
        'lang'=>LANGUAGE_ID,
        'mid_menu'=>1,
        'mid'=>$moduleId,
        'tabControl_active_tab'=>$request['tabControl_active_tab']
    ])->getUri());
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>
<form method='post' action='' name='bootstrap'>
    <? $tabControl->Begin();

    foreach ($aTabs as $aTab)
    {
        $tabControl->BeginNextTab();
        __AdmSettingsDrawList($moduleId, $aTab['OPTIONS']);
    }

    $tabControl->Buttons(array('btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false)); ?>

    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>