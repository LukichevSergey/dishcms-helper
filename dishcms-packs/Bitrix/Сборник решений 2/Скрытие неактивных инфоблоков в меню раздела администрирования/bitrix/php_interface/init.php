<?php
require_once('kontur/Custom.php');

AddEventHandler('main', 'OnBuildGlobalMenu', ['\Kontur\Custom', 'AdminMenu']);
