<?php
namespace ecommerce\ext\sync\behaviors;

class SettingsBehavior extends \CBehavior
{
    public $sync_url;
    public $sync_token;
    public $sync_limit;
    public $sync_reload_files;
    
    public function rules()
    {
        return [
            ['sync_limit', 'numerical', 'integerOnly'=>true, 'min'=>1],
            ['sync_token, sync_url, sync_reload_files', 'safe']
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'sync_token'=>'Ключ синхронизации',
            'sync_url'=>'URL скрипта выгрузки',
            'sync_limit'=>'Кол-во товаров синхронизируемых за один шаг',
            'sync_reload_files'=>'Обновить картинки и файлы всех товаров (если не отмечена, то картинки и файлы будут обновлены только для новых товаров)'
        ];
    }
}