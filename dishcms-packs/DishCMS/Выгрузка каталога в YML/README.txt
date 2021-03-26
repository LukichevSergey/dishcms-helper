1) Подключение /protected/config/defaults.php

	'import'=>[
		...
		'ext.YmlGenerator.YmlGenerator',
	],
	...
	'components'=>array(
		'ymlGenerator'=>[
		    'class'=>'MyYmlGenerator',
		    // create file in DOCUMENT_ROOT directory
		    'outputFile'=>dirname($_SERVER['SCRIPT_FILENAME']).'/yml/export.yml'
		],
		

2) Указать наименование компании в файле /protected/components/MyYmlGenerator.php 

 protected function shopInfo() {
        return array(
            'name'=>'',
            'company'=>'',

