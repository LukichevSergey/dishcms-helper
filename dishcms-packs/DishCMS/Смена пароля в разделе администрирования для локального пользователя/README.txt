1) Включить локальную авторизацию /protected/config/params.php
	'localauth' => true, 

2) Изменить тип сравнения пароля в /protected/components/UserAuth.php
private function localAuth() {
	...
	else if($users[$this->username]!==$this->password)
	заменить на 
	else if(!\CPasswordHelper::verifyPassword($this->password, $users[$this->username])) {

3) Скопировать файлы 

4) Добавить действие в /protected/modules/admin/controllers/DefaultController
	public function actions()
    {
        return A::m(parent::actions(), [
            'changePassword'=>'\admin\actions\ChangePasswordAction'
        ]);
    }

5) Добавить в меню
<li><a href="/cp/default/changePassword">Сменить пароль</a></li>

