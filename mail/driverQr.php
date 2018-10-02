<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */

?>

<h3>Здравствуйте, <?= Html::encode($user->getFullName()) ?>!</h3>
<h4>Во вложении файл QR кода для вашего автомобиля.</h4>
