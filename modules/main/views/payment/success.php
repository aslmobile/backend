<?php

$css = <<<CSS
.message{
    width: 50%;
    margin: 5% auto 0px;
    font-size: 25px;
}
.success-message{
  background-color: #b1ffb1;
  color: #039603;
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
            <?= Yii::$app->mv->gt('Оплата успешно завершена.',[],false); ?>
        </div>
    </div>
</div>