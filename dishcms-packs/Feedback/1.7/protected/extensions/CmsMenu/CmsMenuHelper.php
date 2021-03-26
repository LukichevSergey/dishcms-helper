<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 19.01.12
 * Time: 8:52
 * To change this template use File | Settings | File Templates.
 */
/**
 * Menu item converter to route
 */
class CmsMenuHelper
{
    static $routes = null;

    static function getRoutes($index = false)
    {
        if (self::$routes == null) {
            self::$routes = include(Yii::getPathOfAlias('application.data').DS.'routes.php');
        }
        return $index ? self::$routes[$index] : self::$routes;
    }

    /**
     * @static
     * @param Menu $item
     * @return string
     */
    public static function adminRoute(& $item)
    {
        $route = '#';

        $routes     = self::getRoutes('admin');
        $model_name = $item->options['model'];

        if (isset($routes[$model_name])) {
            $url = isset($routes[$model_name]['update']) ? $routes[$model_name]['update'] : false;

            if ($url && isset($item->options['id']) && intval($item->options['id']) > 0) {
                if (strpos($url, 'class:') !== false) {
                    $class_name = str_replace('class:', '', $url);
                    $class = new $class_name;
                    $route = $class->route($item);
                } else
                    $route = array('/admin/'. $url, 'id'=>$item->options['id']);
            }
            else {
                if (isset($routes[$model_name]['all'])) {
                    $route = array('/admin/'. $routes[$model_name]['all']);
                }
                elseif(isset($routes[$model_name]['combine']) && isset($item->options['id'])) {
                	$route = array("/admin/{$routes[$model_name]['combine']}/{$item->options['id']}");
                }
            }
        }

        return $route;
    }

    /**
     * @static
     * @param Menu $item
     * @param array $itemOptions
     * @param array $linkOptions
     * @return array|string
     */
    public static function siteRoute(& $item, & $itemOptions = array(), & $linkOptions = array())
    {
        $route  = '#';

        $routes     = self::getRoutes('site');
        $model_name = $item->options['model'];

        if ($item->default) {
            $route = array('/site/index');
        }

        elseif(isset($routes[$model_name])) {
            $url = isset($routes[$model_name]['one']) ? $routes[$model_name]['one'] : false;

            if ($url && isset($item->options['id']) && intval($item->options['id']) > 0) {
                if (strpos($url, 'class:') !== false) {
                    Yii::import('ext.CmsMenu.routes.*');

                    $class_name = str_replace('class:', '', $url);
                    $class = new $class_name;
                    $route = $class->route($item, $itemOptions, $linkOptions);
                } else
                    $route = array($url, 'id'=>$item->options['id']);
            }
            else {
                $url = $routes[$model_name]['all'];
                if (isset($url))
                    $route = array($url);
            }
        }

        return $route;
    }
}
