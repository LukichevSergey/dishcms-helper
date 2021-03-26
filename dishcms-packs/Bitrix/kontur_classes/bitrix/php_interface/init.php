<?php
require_once('kontur/autoload.php');

AddEventHandler('main', 'OnBuildGlobalMenu', ['\Kontur\Custom', 'AdminMenu']);
