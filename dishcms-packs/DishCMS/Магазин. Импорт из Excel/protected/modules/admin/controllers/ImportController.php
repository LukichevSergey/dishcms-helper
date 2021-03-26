<?php
Yii::import('application.xls.PHPExcel', true);

class ImportController extends AdminController
{
    public function actionIndex()
    {
        $message = $error = '';
        $i = 0;

        $mimeTypes = [
            'application/vnd.ms-excel',
            'text/plain',
            'text/csv',
            'text/tsv',
        ];

        $start = microtime(true);

        set_time_limit(0);
        // ini_set('max_execution_time', 1*3600); // 1 hour

        if (isset($_FILES['import']) && in_array($_FILES['import']['type'], $mimeTypes)) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/import/tmp.xls';

            move_uploaded_file($_FILES['import']['tmp_name'], $filePath);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $filterSubset = new MyReadFilter();

            $inputFileType = 'Excel5';
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);

            $objReader->setReadFilter($filterSubset);
            $objReader->setReadDataOnly(true);

            $objPHPExcel = $objReader->load($filePath);

            $arrayData = [];

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $arrayData = $worksheet->toArray();

                break;
            }

            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);

            /*
            [0] => Наименование товара
            [1] => Код товара
            [2] => Б/у/Новый
            [3] => Себестоимость (у.е.)
            [4] => Себестоимость (руб.)
            [5] => Рекомендованная цена (у.е.)
            [6] => Рекомендованная цена (руб.)
            [7] => Марка
            [8] => Модель
            [9] => Кузов
            [10] => Номер
            [11] => Двигатель
            [12] => Перед/Зад
            [13] => Право/Лево
            [14] => Верх/Низ
            [15] => Производитель
            [16] => Код производителя
            [17] => OEM код
            [18] => Цвет
            [19] => Дата выпуска
            [20] => Примечание
            [21] => Штрихкод
            [22] => Количество
            */

            $productIDs = [];

            $arrayData = array_slice($arrayData, 1);

            foreach ($arrayData as $item) {
                $i++;

                /*foreach ($item as &$rowItem) {
                    $rowItem = $this->toUTF8($rowItem);
                }*/

                unset($rowItem);

                $title = $item[0];
                $code = str_pad($item[1], 9, "0", STR_PAD_LEFT);
                $alias = $this->str2url(implode('-', [$title, $code]));

                if (!($product = Product::model()->find(['condition' => 'code = :code', 'params' => [':code' => $code]]))) {
                    $product = new Product();
                }

                $product->title = $title;
                $product->code = $code;

                $categories = [];

                $categories[] = $item[7];

                if (empty($item[8]) || ctype_space($item[8])) {
                    $item[8] = '-';
                }

                $categories[] = $item[8];

                $product->category_id = $this->findProductCategory($categories);

                $product->alias = $alias;

                $product->body = $item[9];
                $product->detail_number = $item[10];
                $product->engine = $item[11];
                $product->front_back = $item[12];
                $product->right_left = $item[13];
                $product->top_down = $item[14];
                $product->release = $item[19];
                $product->notice = $item[20];
                $product->stock_count = $item[22];

                $product->price = $item[6];

                $product->save();

                $productIDs[] = $product->id;
            }

            if ($productIDs) {
                $criteria = new CDbCriteria();
                $criteria->addNotInCondition('id', $productIDs);

                Product::model()->deleteAll($criteria);
            }

            unlink($filePath);

            $time = microtime(true) - $start;

            $message = '<b>Выгрузка завершена.</b>';
            $message .= '<br>';
            $message .= 'Время выгрузки: ' . $time . ' с.';
            $message .= '<br>';
            $message .= 'Обновлено товаров: ' . ($i - 1);

            Yii::app()->cache->flush();
        }

        $this->render('index', [
            'error' => $error,
            'message' => $message,
            'count' => $i,
        ]);
    }

    protected function findProductCategory($categories)
    {
        $category_id = false;
        $root = false;
        $level = 1;

        foreach ($categories as $key => $currentCategory) {
            if ($currentCategory) {
                $criteria = new CDbCriteria();

                $criteria->condition = 'title = :title AND level = ' . $level;

                $criteria->params = [
                    ':title' => $currentCategory,
                ];

                if ($category_id && $root) {
                    $criteria->addCondition('root = ' . $root);
                }

                $category = Category::model()->find($criteria);

                if (!$category) {
                    $category = new Category();
                    $category->title = $currentCategory;
                    $category->alias = $this->str2url($category->title);

                    if ($category_id) {
                        $parent = Category::model()->findByPk($category_id);

                        if (!$category->appendTo($parent)) {
                            $category->alias = $this->str2url($category->title . '-' . substr(uniqid(), 3));

                            if (!$category->appendTo($parent)) {
                                return false;
                            }
                        }
                    } else {
                        if (!$category->saveNode()) {
                            $category->alias = $this->str2url($category->title . '-' . substr(uniqid(), 3));
                            $category->saveNode();

                            if (!$category->saveNode()) {
                                return false;
                            }
                        }
                    }
                }

                $category_id = $category->id;

                if ($level == 1) {
                    $root = $category_id;
                }

                $level++;
            }
        }

        return $category_id;
    }

    protected function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '',  'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    protected function str2url($str) {
        // переводим в транслит
        $str = $this->rus2translit($str);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");
        return $str;
    }

    /**
     * @param $string string
     * @return string
     */
    protected function toUTF8($string)
    {
        if (!$string) {
            return $string;
        }

        return iconv('WINDOWS-1251', 'UTF-8//IGNORE', $string);
    }
}

class MyReadFilter implements PHPExcel_Reader_IReadFilter {
    public function readCell($column, $row, $worksheetName = '') {
        //  Read rows 1 to 7 and columns A to E only
        if ($row >= 1 && $row <= 10000) {
           if (in_array($column,range('A','W'))) {
              return true;
           }
        }
        return false;
    }
}
