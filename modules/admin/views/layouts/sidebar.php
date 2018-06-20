<?php
$this->beginContent('@app/modules/admin/views/layouts/main.php');

use yii\helpers\Url;

$cont = Yii::$app->controller->id;
$act = Yii::$app->controller->action->id;

$image = new Imagick('');

?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?= Yii::$app->imageCache->img(Yii::getAlias('@webroot') . Yii::$app->user->identity->avatar, '160x160', ['class' => 'img-circle']) ?>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->email ?></p>
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
            <li class="treeview<?= (($cont == 'menu') || ($cont == 'menu-group')) ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-user"></i> <span><?= Yii::$app->mv->gt('Меню', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'menu' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/menu/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Пункты меню', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'menu' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/menu/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новый пункт меню', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'menu-group' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/menu-group/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Группы меню', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'menu-group' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/menu-group/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новая группа меню', [], false) ?>
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
            <li class="treeview<?= ($cont == 'products') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-product-hunt"></i>
                    <span><?= Yii::$app->mv->gt('Продукты', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'products' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/products/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'products' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/products/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новая', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'category') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-wrench"></i>
                    <span><?= Yii::$app->mv->gt('Категории', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'category' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/category/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'products' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/category/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новая', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'downloads') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-download"></i>
                    <span><?= Yii::$app->mv->gt('Загрузки', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'downloads' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/downloads/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'products' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/downloads/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новая', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'feedback') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-download"></i>
                    <span><?= Yii::$app->mv->gt('Обратная связь', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'feedback' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/feedback/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'countries') ? ' active' : '' ?>" style="display: none">
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
            <li class="treeview<?= ($cont == 'cities') ? ' active' : '' ?>" style="display: none;">
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
            <li class="header"><?= Yii::$app->mv->gt('Контент', [], false) ?></li>
            <li class="treeview<?= ($cont == 'dynamic') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-file-text-o"></i>
                    <span><?= Yii::$app->mv->gt('Страницы', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'dynamic' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/dynamic/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'dynamic' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/dynamic/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новая', [], false) ?>
                        </a>
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

