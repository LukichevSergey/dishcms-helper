<?php
namespace accounts\components\validators;

use accounts\components\helpers\HAccount;

/**
 * Проверка пароля для профиля аккаунта
 *
 */
class ProfilePasswordValidator extends \CValidator
{
    /**
     * {@inheritDoc}
     * @see \CValidator::validateAttribute()
     */
    public function validateAttribute($object, $attribute)
    {
        if($object->password) {
            $account=HAccount::account(true);
            if($object->password !== $object->repassword) {
                $this->addError($object, 'password', 'Пароли не совпадают');
            }
            /*
            if(!$account->validatePassword($object->lastpassword)) {
                $this->addError($object, 'lastpassword', 'Неверно указан текущий пароль');
            }
            */
        }
    }
}