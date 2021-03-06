<?php
/**
 * Created by PhpStorm.
 * User: Graf
 * Date: 17.11.2017
 * Time: 12:44
 */

use yii\helpers\Html;

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
<html>
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?= Html::csrfMetaTags() ?>
    <meta name="robots" content="index, follow">
    <meta name="author" content="v-jet group">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- FAVICONS -->
    <!--            <link rel="apple-touch-icon" sizes="57x57" href="/files/favicon/apple-icon-57x57.png">-->
    <!--            <link rel="apple-touch-icon" sizes="60x60" href="/files/favicon/apple-icon-60x60.png">-->
    <!--            <link rel="apple-touch-icon" sizes="72x72" href="/files/favicon/apple-icon-72x72.png">-->
    <!--            <link rel="apple-touch-icon" sizes="76x76" href="/files/favicon/apple-icon-76x76.png">-->
    <!--            <link rel="apple-touch-icon" sizes="114x114" href="/files/favicon/apple-icon-114x114.png">-->
    <!--            <link rel="apple-touch-icon" sizes="120x120" href="/files/favicon/apple-icon-120x120.png">-->
    <!--            <link rel="apple-touch-icon" sizes="144x144" href="/files/favicon/apple-icon-144x144.png">-->
    <!--            <link rel="apple-touch-icon" sizes="152x152" href="/files/favicon/apple-icon-152x152.png">-->
    <!--            <link rel="apple-touch-icon" sizes="180x180" href="/files/favicon/apple-icon-180x180.png">-->
    <!--            <link rel="icon" type="image/png" sizes="192x192"  href="/files/favicon/android-icon-192x192.png">-->
    <!--            <link rel="icon" type="image/png" sizes="32x32" href="/files/favicon/favicon-32x32.png">-->
    <!--            <link rel="icon" type="image/png" sizes="96x96" href="/files/favicon/favicon-96x96.png">-->
    <!--            <link rel="icon" type="image/png" sizes="16x16" href="/files/favicon/favicon-16x16.png">-->
    <meta name="msapplication-TileColor" content="#ffffff">
    <!--        <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">-->
    <meta name="theme-color" content="#ffffff">

    <title><?= ($this->title) ? Html::encode($this->title) : Html::encode("ASL Mobile") ?></title>

    <?php $this->head() ?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

</head>
<body class="hold-transition">
<?php $this->beginBody() ?>

<?= $content; ?>

<?php $this->endBody() ?>
</html>
<?php $this->endPage() ?>
