<?php

/**
 * @var \yii\web\View $this
 */

use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="<?= Yii::$app->controller->getOldLangAssoc() ?>"> <!--<![endif]-->
<head>

    
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1">
    <?= Html::csrfMetaTags() ?>
    <meta name="robots" content="index, follow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="apple-touch-icon" sizes="57x57" href="/files/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/files/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/files/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/files/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/files/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/files/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/files/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/files/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/files/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/files/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/files/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/files/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/files/favicon/favicon-16x16.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/files/favicon/ms-icon-144x144.png">

    <title><?= ($this->title) ? Html::encode($this->title) : Yii::$app->controller->coreSettings->name ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <?php $this->head() ?>


    <script>
        var rand = '<?=Yii::$app->controller->rand;?>';
        var YII_CSRF_TOKEN = '<?=Yii::$app->request->csrfToken;?>';
        var matrix = {};
        matrix.uid = <?=intval(Yii::$app->user->id);?>;
        matrix.onload = [];
        matrix.t = {
            cancel: '<?=Yii::$app->mv->gt('Cancel');?>',
            confirmation_required: '<?=Yii::$app->mv->gt('Confirmation required');?>',
            loading: '<?=Yii::$app->mv->gt('Loading', [], false);?>',
        };
        matrix.lang = '<?=Yii::$app->controller->lang?>';
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDZdY5p12DfwjOQMoSiCAnQT_t9kGKwV6w"></script>
</head>

<body class="<?= implode(' ', Yii::$app->controller->bodyClass); ?>">

<?php $this->beginBody() ?>

<div id="fullpage">

    <?= Yii::$app->controller->renderPartial('//layouts/header'); ?>

    <?= $content; ?>

    <?= Yii::$app->controller->renderPartial('//layouts/footer'); ?>
</div><!-- End wrap -->

<?=Yii::$app->mv->curPageGt();?>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
