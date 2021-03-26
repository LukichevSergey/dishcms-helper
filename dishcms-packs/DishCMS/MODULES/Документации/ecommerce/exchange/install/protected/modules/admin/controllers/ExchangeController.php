<?php 
use common\components\helpers\HFile;

class ExchangeController extends \ecommerce\modules\exchange\modules\admin\controllers\ExchangeController
{	
    /**
     *
     * {@inheritDoc}
     * @see \ecommerce\modules\exchange\modules\admin\controllers\ExchangeController::actionImport()
     */
    public function actionImport()
    {
        $this->render('import');
    }

    /**
     *
     * {@inheritDoc}
     * @see \ecommerce\modules\exchange\modules\admin\controllers\ExchangeController::actionExport()
     */
    public function actionExport()
    {
        $this->render('export');
    }
}
