<?php

$css = <<<CSS
.message{
    width: 50%;
    margin: 5% auto 0px;
    font-size: 25px;
}
.error-message{
  background-color: #ffcccc;
  color: #f00;
  padding: 10px 5px;
  border-radius: 3px;
  margin-bottom: 15px;
}
.thumbnail{
    border: none;
}
CSS;

$this->registerCss($css);
?>
<div class="row">
    <div class="message">
        <div class="text-center">
            <?= Yii::$app->mv->gt('Неудалось совершить оплату.',[],false); ?>
        </div>
    </div>
</div>