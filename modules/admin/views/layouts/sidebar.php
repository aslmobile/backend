<?php
$this->beginContent('@app/modules/admin/views/layouts/main.php');

use yii\helpers\Url;

$cont = Yii::$app->controller->id;
$act = Yii::$app->controller->action->id;
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="row">
                <div class="col-sm-12 text-center" style="color: white;"><?= Yii::$app->user->identity->fullName ?></div>
                <small class="col-sm-12 text-center" style="color: white;"><?= Yii::$app->user->identity->email ?></small>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header"><?= Yii::$app->mv->gt('Основное управление', [], false) ?></li>
            <li<?= ($cont == 'default') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/default/index']) ?>"> <i class="fa fa-dashboard"></i>
                    <span><?= Yii::$app->mv->gt('Панель управления', [], false); ?></span> </a>
            </li>
            <li class="treeview<?= ($cont == 'translations' || $cont == 'message') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-language" aria-hidden="true"></i>
                    <span><?= Yii::$app->mv->gt('Переводы', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'translations' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/translations/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Динамические переводы', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'translations' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/translations/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новый дин. перевод', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'translations' && $act == 'edit') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/translations/edit') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Редактировать дин. перевод', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'message' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/message/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Статические переводы', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'message' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/message/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новый статический перевод', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="header"><?= Yii::$app->mv->gt('Менеджмент', [], false); ?></li>
            <li class="treeview<?= ($cont == 'user') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-users"></i>
                    <span><?= Yii::$app->mv->gt('Пользователи', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'countries' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/user/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'countries' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/user/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новая', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'vehicles') ? ' active' : '' ?>">
                <a href="<?= Url::toRoute('/admin/vehicles') ?>">
                    <i class="fa fa-car"></i>
                    <span><?= Yii::$app->mv->gt('Автомобили', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'vehicles' && ($act == 'index' || $act == 'update' || $act == 'create')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/vehicles') ?>">
                            <i class="fa fa-car"></i> <?= Yii::$app->mv->gt('Список автомобилей', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'vehicles' && ($act == 'types' || $act == 'type' || $act == 'create-type')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/vehicles/types') ?>">
                            <i class="fa fa-th-list"></i> <?= Yii::$app->mv->gt('Типы', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'vehicles' && ($act == 'brands' || $act == 'brand' || $act == 'create-brand')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/vehicles/brands') ?>">
                            <i class="fa fa-th-list"></i> <?= Yii::$app->mv->gt('Бренды', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'vehicles' && ($act == 'models' || $act == 'model' || $act == 'create-model')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/vehicles/models') ?>">
                            <i class="fa fa-th-list"></i> <?= Yii::$app->mv->gt('Модели', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'lines') ? ' active' : '' ?>">
                <a href="<?= Url::toRoute('/admin/lines') ?>">
                    <i class="fa fa-map-marker"></i>
                    <span><?= Yii::$app->mv->gt('Маршруты', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'lines' && ($act == 'index' || $act == 'update' || $act == 'create')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lines') ?>">
                            <i class="fa fa-map-marker"></i> <?= Yii::$app->mv->gt('Все', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'lines' && ($act == 'routes' || $act == 'route')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lines/routes') ?>">
                            <i class="fa fa-map-marker"></i> <?= Yii::$app->mv->gt('Список маршрутов', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'lines' && ($act == 'checkpoints' || $act == 'checkpoint')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lines/checkpoints') ?>">
                            <i class="fa fa-th-list"></i> <?= Yii::$app->mv->gt('Контрольные точки', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="treeview<?= ($cont == 'countries') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-map"></i>
                    <span><?= Yii::$app->mv->gt('Страны', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'countries' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/countries/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'countries' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/countries/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новая', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'cities') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-map-marker"></i>
                    <span><?= Yii::$app->mv->gt('Города', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'cities' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/cities/index') ?>"><i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>                        </a>
                    </li>
                    <li<?= ($cont == 'cities' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/cities/create') ?>"><i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новый', [], false) ?>                        </a>
                    </li>
                </ul>
            </li>
            <li class="header"><?= Yii::$app->mv->gt('Настройки', [], false) ?></li>
            <li<?= ($cont == 'settings') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/settings/index']) ?>"> <i class="fa fa-cogs"></i>
                    <span><?= Yii::$app->mv->gt('Настройки', [], false); ?></span> </a>
            </li>
            <li<?= ($cont == 'file') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/file/index']) ?>"> <i class="fa fa-folder-open fa-fw"></i>
                    <span><?= Yii::$app->mv->gt('Файловый менеджер', [], false); ?></span> </a>
            </li>
            <li class="treeview<?= ($cont == 'lang') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-file-text"></i>
                    <span><?= Yii::$app->mv->gt('Языки', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'lang' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lang/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'lang' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lang/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новый', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

<?= $content ?>

<?php $this->endContent(); ?>

