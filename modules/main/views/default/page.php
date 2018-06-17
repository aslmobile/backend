<?php
/**
 * @var \yii\web\View $this
 */
use app\assets\AppAsset;

AppAsset::register($this);
$this->title = Yii::$app->mv->gt($model->title, [], 0);

?>
<div class="section container-fluid">
        <div class="row no-gutters">
        <div class="col-xl-4 col-xxl-3">
            <h1><?= $model->title;?></h1>
        </div>
    </div>
    <div class="row no-gutters">
        <?
        // TEXT
        if ($model && $model->text):?>
            <?= $model->text; ?>
        <?endif;?>
    </div>
</div>







