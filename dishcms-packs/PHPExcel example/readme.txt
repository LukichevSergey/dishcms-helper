public function actionTest()
{
    ini_set('max_execution_time', 300);

    $path = '/1.xls';

    // get a reference to the path of PHPExcel classes

    // Turn off our amazing library autoload
    spl_autoload_unregister(array('YiiBase','autoload'));

    include($_SERVER["DOCUMENT_ROOT"] . '/xls/PHPExcel.php');

    $objReader = new PHPExcel_Reader_Excel5();
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load( Yii::getPathOfAlias('webroot') . $path );

    $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

    $array_data = array();

    $rowIndex = 0;

    foreach($rowIterator as $row){
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        if (1 == $row->getRowIndex ()) {
            continue;
        }

        foreach ($cellIterator as $cell) {
            $array_data[$rowIndex][] = $cell->getCalculatedValue();
        }

        $rowIndex++;
    }

    print_r($array_data); die;

    spl_autoload_register(array('YiiBase','autoload'));
}