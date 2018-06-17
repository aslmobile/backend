<?php

use app\modules\admin\assets\AppAsset;

AppAsset::register($this);

$this->title = Yii::$app->mv->gt("Error", [], 0) . " #" . $exception->statusCode;

$this->params['breadcrumbs'][] = $this->title;

if ($exception->statusCode == 403) {
    $link = '<a href="/logout">'.Yii::$app->mv->gt("Logout", [], 0).'</a>';
} else {
    $link = '<a href="/">'.Yii::$app->mv->gt("Return to main", [], 0).'</a>';
}

?>

<section class="content" style="margin-top: 30%">
    <div class="error-page">
        <h2 class="headline text-yellow"> <?= $exception->statusCode ?></h2>

        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> Oops! <?= $exception->getMessage() ?></h3>
            <p>
                <?= Yii::$app->mv->gt('We can not refer you to the page you were looking for. Meanwhile, you may ' . $link . '.', [], false) ?>
            </p>
        </div>
    </div>
</section>






