<?php
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::registerAutoLoadClasses($module_id, include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classmap.php'));
