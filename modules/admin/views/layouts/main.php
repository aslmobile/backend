<?php

use app\modules\admin\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this); ?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title>Admin Panel <?= Yii::$app->name ?></title>

    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <?= Html::csrfMetaTags() ?>

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>

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
            <link rel="manifest" href="/files/favicon/manifest.json">
            <meta name="msapplication-TileColor" content="#ffffff">
            <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <link rel="icon" type="image/png" href="/files/favicon/favicon_logo_lemcore.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">


    <?php $this->head() ?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/admin_assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/admin_assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">

<div class="modal fade" id="action-result-modal" tabindex="-1" role="dialog" aria-labelledby="actionResultLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="actionResultLabel"><?= Yii::$app->mv->gt('Action result', [], false) ?></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<?php $this->beginBody() ?>
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <?= Html::a(
            Yii::$app->imageCache->img(Yii::getAlias('@webroot') . Yii::$app->controller->coreSettings->logo, 'x50', [
                'alt' => Yii::$app->controller->coreSettings->name,
                'class' => 'logo-lg'
            ]),
            ['/admin/default/index'],
            ['class' => 'logo']
        ) ?>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Messages: style can be found in dropdown.less-->
                    <?php /*
                            <li class="dropdown messages-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-envelope-o"></i>
                                    <span class="label label-success">4</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 4 messages</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li><!-- start message -->
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Support Team
                                                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                    </h4>
                                                    <p>Why not buy a new awesome theme?</p>
                                                </a>
                                            </li>
                                            <!-- end message -->
                                            <li>
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        AdminLTE Design Team
                                                        <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                                    </h4>
                                                    <p>Why not buy a new awesome theme?</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Developers
                                                        <small><i class="fa fa-clock-o"></i> Today</small>
                                                    </h4>
                                                    <p>Why not buy a new awesome theme?</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Sales Department
                                                        <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                                    </h4>
                                                    <p>Why not buy a new awesome theme?</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Reviewers
                                                        <small><i class="fa fa-clock-o"></i> 2 days</small>
                                                    </h4>
                                                    <p>Why not buy a new awesome theme?</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="#">See All Messages</a></li>
                                </ul>
                            </li>
                            <!-- Notifications: style can be found in dropdown.less -->
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-warning">10</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 10 notifications</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                                                    page and may cause design problems
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-users text-red"></i> 5 new members joined
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-user text-red"></i> You changed your username
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="#">View all</a></li>
                                </ul>
                            </li>
                            <!-- Tasks: style can be found in dropdown.less -->
                            <li class="dropdown tasks-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-flag-o"></i>
                                    <span class="label label-danger">9</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 9 tasks</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <h3>
                                                        Design some buttons
                                                        <small class="pull-right">20%</small>
                                                    </h3>
                                                    <div class="progress xs">
                                                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                            <span class="sr-only">20% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <h3>
                                                        Create a nice theme
                                                        <small class="pull-right">40%</small>
                                                    </h3>
                                                    <div class="progress xs">
                                                        <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                            <span class="sr-only">40% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <h3>
                                                        Some task I need to do
                                                        <small class="pull-right">60%</small>
                                                    </h3>
                                                    <div class="progress xs">
                                                        <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                            <span class="sr-only">60% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <h3>
                                                        Make beautiful transitions
                                                        <small class="pull-right">80%</small>
                                                    </h3>
                                                    <div class="progress xs">
                                                        <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                            <span class="sr-only">80% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                        </ul>
                                    </li>
                                    <li class="footer">
                                        <a href="#">View all tasks</a>
                                    </li>
                                </ul>
                            </li>
                            */ ?>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?/*= Yii::$app->imageCache->img(Yii::getAlias('@webroot') . Yii::$app->user->identity->avatar, '160x160', ['class' => 'user-image']) */?>
                            <span class="hidden-xs"><?= Yii::$app->user->identity->email ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <?/*= Yii::$app->imageCache->img(Yii::getAlias('@webroot') . Yii::$app->user->identity->avatar, '160x160', ['class' => 'user-image']) */?>
                                <p>
                                    <?= Yii::$app->user->identity->email ?>
                                    <small><?= Yii::$app->mv->gt('Зарегистирован: {date}', [
                                            'date' => Yii::$app->formatter->asDate(Yii::$app->user->identity->created_at)
                                        ], false) ?></small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <?= Html::a(Yii::$app->mv->gt('Настройки', [], false), ['/admin/settings/index']) ?>
                                        <a href="#"></a>
                                    </div>
                                    <div class="col-xs-4 text-center"></div>
                                    <div class="col-xs-4 text-center"></div>
                                </div>
                                <!-- /.row -->
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <?= Html::a(
                                        Yii::$app->mv->gt('Профиль', [], false),
                                        ['/admin/user/update', 'id' => Yii::$app->user->id],
                                        [
                                            'class' => "btn btn-default btn-flat",
                                        ]); ?>
                                </div>
                                <div class="pull-right">
                                    <?= Html::a(
                                        Yii::$app->mv->gt('Выход', [], false),
                                        ['/user/default/logout'],
                                        [
                                            'class' => "btn btn-default btn-flat",
                                            'data' => ['method' => 'post'],
                                        ]); ?>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Content Wrapper. Contains page content -->
    <?= $content ?>
    <!-- /.content-wrapper -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
