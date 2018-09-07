<?php
namespace console\controllers;

use yii\console\Controller;
use common\models\User;
use yii\console\ExitCode;

class AdminController extends Controller
{

    public function actionAdd()
    {
        if (User::find()->count()) {
            $this->stdout('User already exists!' . PHP_EOL);
            return;
        }

        $login = $this->prompt('Login:', [
            'required' => true,
            'validator' => function($value, &$error) {
                if(!preg_match('/[a-z0-9_\-]{1,32}/', $value)) {
                    $error = 'Only latin characters, digits, - and _ symbols allowed. 32 characters max.';
                    return false;
                }
                return true;
            }
        ]);
        $email = $this->prompt('E-mail:', [
            'required' => true,
            'validator' => function($value, &$error) {
                if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalidate e-mail format';
                    return false;
                }
                return true;
            }
        ]);
        $password = $this->prompt('Пароль:', [
            'required' => true,
            'validator' => function($value, &$error) {
                if(mb_strlen($value) < 4) {
                    $error = 'The password should contain 4 characters minimum';
                    return false;
                }
                return true;
            }
        ]);
        $confirmMessage = <<<EOF

===================================

Is it correct data:
Login: {$login}
E-mail: {$email}
Password: {$password}

CTRL+C for cancel
EOF;

        if(!$this->confirm($confirmMessage, true)) {
            $this->stdout('Cancelled.' . PHP_EOL);
            return $this->actionAdd();
        }

        $admin = new User();
        $admin->username = $login;
        $admin->email = $email;
        $admin->password_hash = \Yii::$app->getSecurity()->generatePasswordHash($password);
        $admin->auth_key = \Yii::$app->getSecurity()->generateRandomString(32);
        $admin->updated_at = time();
        if(!$admin->save()) {
            $this->stderr("При сохранении админа произошла одна или несколько ошибок\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }

}
