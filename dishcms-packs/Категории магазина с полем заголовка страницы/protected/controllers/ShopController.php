<?php

class ShopController extends Controller
{
    public function actions()
    {
        return array(
            'robokassa_result'=>array('class'=>'ext.payment.Robokassa.result')
        );
    }

    public function actionIndex()
    {
        $products = Product::model()->findAll(array('order'=>'new DESC, ordering ASC, id DESC', 'limit'=>16));

        $this->prepareSeo('Магазин');

        if (Yii::app()->request->isAjaxRequest) {
            echo json_encode(array(
                'title'=>$this->pageTitle,
                'contentTitle'=>'Магазин',
                'content'=>$this->renderPartial('_products', compact('products'), true)
            ));
            Yii::app()->end();
        } else {
            $categories = Category::model()->findAll(array('order'=>'ordering'));
            $this->render('shop', compact('categories', 'products'));
        }
    }

    public function actionCategory($id)
    {
        $category = Category::model()->findByPk($id);
        $this->prepareSeo($category->page_title?:$category->title);

        $criteria = new CDbCriteria();
        $criteria->condition = 'category_id = ?';
        $criteria->params = array($id);
        $criteria->order = 'ordering ASC, created DESC, id DESC';

        $count = Product::model()->count($criteria);

        $pages = new CPagination($count);
        $pages->pageSize = Yii::app()->params['products_on_page'] ? Yii::app()->params['products_on_page'] : 20;
        $pages->applyLimit($criteria);

        $products = Product::model()->findAll($criteria);

        if (Yii::app()->request->isAjaxRequest) {
            echo json_encode(array(
                'title'=>$this->pageTitle,
                'contentTitle'=>$category->title,
                'description'=>$category->description,
                'content'=>$this->renderPartial('_products', compact('products', 'pages'), true)
            ));
            Yii::app()->end();
        } else {
            $categories = Category::model()->findAll(array('order'=>'ordering'));
            $this->render('category', compact('products', 'category', 'categories', 'pages'));
        }
    }

    /**
     * Action show a product page
     *
     * @param $id
     */
    public function actionProduct($id)
    {
        $product = Product::model()->findByPk($id);

        if (!$product)
            throw new CHttpException(404, 'Товар не найден в каталоге');

        $categories = Category::model()->findAll(array('order'=>'ordering'));

        $this->prepareSeo($product->title);
        $this->render('product', compact('product', 'categories'));
    }

    public function actionOrder()
    {
        $cart = CmsCart::getInstance();

        /*if ($cart->countAll() == 0) {
            $this->refresh();
        }*/

        $model = new Order();

        $model->checkPayment();

        if (isset($_POST['Order'])) {
            $model->attributes = $_POST['Order'];

            if ($model->validate()) {
                $model->save(false);

                $messageAdmin = $this->renderPartial('_admin_email', compact('model'), true);
                $messageClient = $this->renderPartial('_client_email', compact('model'), true);

                if (CmsCore::sendMail($messageAdmin)) {
                    CmsCore::sendMail($messageClient, 'Заказ #'. $model->id .' на сайте '.Yii::app()->name, $model->email);

                    CmsCart::getInstance()->clear();
                    Yii::app()->user->setFlash('order', 'Спасибо, Ваш заказ отправлен!');

                    if ($action = $model->getPaymentAction()) {
                        if (isset($action['url'])) {
                            Yii::app()->user->setState('order_id', $model->id);
                            $this->redirect(array($action['url']));
                        }
                    }
                } else
                    Yii::app()->user->setFlash('order', 'Ошибка отправки заказа');

                $this->redirect(array('orderSuccess'));
            }
        }

        $products = $cart->getResult(true);

        $this->prepareSeo('Оформление заказа');

        if (count($products)) {
            $this->render('order', compact('model', 'products'));
        } else
            $this->render('order_empty');
    }

    public function actionOrderSuccess()
    {
        $this->prepareSeo('Статус заказа');
        $this->render('order_success');
    }

    /**
     * Ajax adding products to cart
     * @param $id
     * @return void
     */
    public function actionAddToCart($id)
    {
        $count = (int)Yii::app()->request->getPost('count', 1);

        $cart = CmsCart::getInstance();
        $cart->add($id, $count);

        if (Yii::app()->request->isAjaxRequest) {
            echo $this->getJsonData($id);
            Yii::app()->end();
        }

        $this->redirect('/');
    }

    /**
     * Ajax update number products of cart
     * @return void
     */
    public function actionUpdateCart()
    {
        $counts = Yii::app()->request->getParam('count');

        if ($counts) {
            $cart = CmsCart::getInstance();

            $ids = array();

            foreach($counts as $id => $count) {
                $cart->update($id, intval($count));
                $ids[] = $id;
            }

            if (count($ids) == 1)
                echo $this->getJsonData($ids[0]);
            else
                echo json_encode(array());
        } else {
            //echo json_encode(array());
        }

        Yii::app()->end();
    }

    /**
     * Prepare Json update data
     * @param $id mixed
     * @return mixed
     */
    private function getJsonData($id)
    {
        $cart = CmsCart::getInstance();

        $data = array();
        $data['id'] = $id;
        $data['count'] = $cart->count($id);
        $data['summary_count'] = $cart->countAll();
        $data['summary_price'] = $cart->priceAll();

        if ($cart->isFirstProduct) {
            $data['summary']  = $cart->getHtmlSummary();
            $data['products'] = $cart->getHtmlProducts();
        }

        if ($cart->isFirstItem) {
            $data['products'] = $cart->getHtmlProducts();
        }

        return json_encode($data);
    }

    public function actionClearCart()
    {
        CmsCart::getInstance()->clear();

        if (Yii::app()->request->isAjaxRequest)
            Yii::app()->end();
        else
            $this->redirect(array('index'));
    }

    public function actionPayment()
    {
        $order_id = Yii::app()->user->getState('order_id');

        if (!$order_id)
            $this->redirect(array('order'));

        $order = Order::model()->findByPk((int)$order_id);

        $this->prepareSeo('Оплата');
        $this->render('payment', compact('order'));
    }

    public function actionPayment_success()
    {
        $this->render('payment_success');
    }

    public function actionPayment_fail()
    {
        $this->render('payment_fail');
    }

}
