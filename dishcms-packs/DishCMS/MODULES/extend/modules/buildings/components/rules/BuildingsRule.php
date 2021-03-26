<?php
/**
 * Правило маршрутизации для планировок
 */
namespace extend\modules\buildings\components\rules;

use extend\modules\buildings\models\Porch;
use extend\modules\buildings\models\Floor;
use extend\modules\buildings\models\Apartment;

class BuildingsRule extends \CBaseUrlRule
{
    public $baseUrl='planirovki';
    public $porchAlias='podjezd';
    public $floorAlias='etazh';
    public $apartmentAlias='kvartira';
    
    /**
     * (non-PHPdoc)
     * @see \CBaseUrlRule::createUrl()
     */
    public function createUrl($manager, $route, $params, $ampersand)
    {
        if(!empty($params['id'])) {
            $id = (int)$params['id'];
            unset($params['id']);
        }        
    
        $url = null;
        if($route == 'buildings/index') {
            $url = $this->baseUrl;
        }
        elseif($route == 'buildings/porch') {
            $url = $this->getPorchUrl($id); 
        }
        elseif($route == 'buildings/floor') {
            $url = $this->getFloorUrl($id); 
        }
        elseif($route == 'buildings/apartment') {
            $url = $this->getApartmentUrl($id);
        }
        
        if(!empty($url)) {
            if($this->baseUrl && ($url != $this->baseUrl)) {
                $url = $this->baseUrl. '/' . $url;
            }
            
            if(!empty($params)) {
                $url .= '?' . $manager->createPathInfo($params, '=', $ampersand);
            }
            
            return $url;
        }
        
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \CBaseUrlRule::parseUrl()
     */
    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {
        $chunks = explode('/', trim(preg_replace('#/+#', '/', $pathInfo), '/'));
        if(!empty($chunks) && (strcmp($chunks[0], $this->baseUrl) === 0)) {
            unset($chunks[0]);
            
            if(empty($chunks)) {
                return 'buildings/index';
            }
            
            $patterns = [
                ["/({$this->porchAlias})-([-]*\d+)/i", 'porchAlias', '\extend\modules\buildings\models\Porch', 'number'],
                ["/({$this->floorAlias})-([-]*\d+)/i", 'floorAlias', '\extend\modules\buildings\models\Floor', 'number', 'porch_id'],
                ["/({$this->apartmentAlias})-(\d+)/i", 'apartmentAlias', '\extend\modules\buildings\models\Apartment', 'id', 'floor_id'],
            ];
            
            $id = null;
            foreach($chunks as $chunk) {
                $data = array_shift($patterns);
                if(preg_match($data[0], $chunk, $m)) {
                    if($m[1] == $this->$data[1]) {
                        $columns = [$data[3]=>$m[2]];
                        if(isset($data[4])) $columns[$data[4]] = $id;
                        if($model = $data[2]::model()->wcolumns($columns)->find(['select'=>'id'])) {
                            $id = $model->id;
                            continue;
                        }
                    }
                }
                return false;
            }
            
            if($id) {
                $_GET['id'] = $id;
                if(empty($patterns)) {
                    return 'buildings/apartment';
                }
                elseif(count($patterns) === 1) {
                    return 'buildings/floor';
                }
                elseif(count($patterns) === 2) {
                    return 'buildings/porch';
                }
            }
        }
        
        return false;
    }
    
    public function getPorchUrl($id)
    {
        $url = null;
        if($porch=Porch::model()->findByPk($id, ['select'=>'id, number'])) {
            $url = $this->porchAlias . '-' . $porch->number;
        }
            
        return $url;
    }
    
    public function getFloorUrl($id)
    {
        $url = null;
        if($floor=Floor::model()->findByPk($id, ['select'=>'id, porch_id, number'])) {
            $url = $this->getPorchUrl($floor->porch_id) . '/' . $this->floorAlias . '-' . $floor->number;
        }
        
        return $url;
    }
    
    public function getApartmentUrl($id)
    {
        $url = null;
        if($apartment=Apartment::model()->findByPk($id, ['select'=>'id, floor_id'])) {
            $url = $this->getFloorUrl($apartment->floor_id) . '/' . $this->apartmentAlias . '-' . $apartment->id;
        }
        
        return $url;
    }
}
