<?use common\components\helpers\HArray as A;?>
<?foreach(['meta_h1', 'meta_title','meta_key'] as $attribute) {
	$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>$attribute]));
}
$this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), ['attribute'=>'meta_desc']));
?>
