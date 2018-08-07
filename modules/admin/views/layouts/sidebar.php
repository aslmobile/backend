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
            <div class="info" style="min-height: 60px; position: relative; left: 0;">
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
                                    class="fa fa-th-list"></i> <?= Yii::$app->mv->gt('Динамические', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'translations' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/translations/create') ?>"><i
                                    class="fa fa-plus"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'translations' && $act == 'edit') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/translations/edit') ?>"><i
                                    class="fa fa-pencil"></i> <?= Yii::$app->mv->gt('Изменить', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'message' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/message/index') ?>"><i
                                    class="fa fa-th-list"></i> <?= Yii::$app->mv->gt('Статические', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'message' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/message/create') ?>"><i
                                    class="fa fa-plus"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="header"><?= Yii::$app->mv->gt('Контент', [], false); ?></li>
            <li class="treeview<?= ($cont == 'legal') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-file-text"></i>
                    <span><?= Yii::$app->mv->gt('Юр. инфо. водитель', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'legal' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/legal/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'legal' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/legal/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'sms-templates') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-file-text"></i>
                    <span><?= Yii::$app->mv->gt('Шаблоны SMS', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'sms-templates' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/sms-templates/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'sms-templates' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/sms-templates/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'faq') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-question"></i>
                    <span><?= Yii::$app->mv->gt('FAQ', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'faq' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/faq/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'faq' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/faq/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'answers') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-question"></i>
                    <span><?= Yii::$app->mv->gt('Быстрые ответы', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'answers' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/answers/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'answers' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/answers/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="header"><?= Yii::$app->mv->gt('Менеджмент', [], false); ?></li>
            <li<?= ($cont == 'user' && $act == 'passengers') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/user/passengers']) ?>">
                    <i class="fa fa-users"></i>
                    <span><?= Yii::$app->mv->gt('Пассажиры', [], false); ?></span>
                </a>
            </li>
            <li<?= ($cont == 'user' && $act == 'drivers') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/user/drivers']) ?>">
                    <i class="fa fa-users"></i>
                    <span><?= Yii::$app->mv->gt('Водители', [], false); ?></span>
                </a>
            </li>
            <li<?= ($cont == 'blacklist' && $act == 'index') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/blacklist/index']) ?>">
                    <i class="fa fa-user-secret"></i>
                    <span><?= Yii::$app->mv->gt('Черный список', [], false); ?></span>
                </a>
            </li>
            <li<?= ($cont == 'km' && $act == 'settings') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/km/settings']) ?>">
                    <i class="fa fa-users"></i>
                    <span><?= Yii::$app->mv->gt('Бесплатные КМ', [], false); ?></span>
                </a>
            </li>

            <li class="header"><?= Yii::$app->mv->gt('Маршруты', [], false); ?></li>
            <li<?= ($cont == 'route') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/route/index']) ?>">
                    <i class="fa fa-map"></i>
                    <span><?= Yii::$app->mv->gt('Маршруты', [], false); ?></span>
                </a>
            </li>
            <li<?= ($cont == 'lines' && ($act == 'checkpoints' || $act == 'checkpoint')) ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/lines/checkpoints']) ?>">
                    <i class="fa fa-map-marker"></i>
                    <span><?= Yii::$app->mv->gt('Остановки', [], false); ?></span>
                </a>
            </li>

            <li class="header"><?= Yii::$app->mv->gt('Линия', [], false); ?></li>
            <li<?= ($cont == 'lines' && ($act == 'vehicles-queue')) ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute('/admin/lines/vehicles-queue') ?>">
                    <i class="fa fa-car"></i> <span><?= Yii::$app->mv->gt('Машины в очереди', [], false) ?></span>
                </a>
            </li>
            <li<?= ($cont == 'lines' && ($act == 'vehicles-ready')) ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute('/admin/lines/vehicles-ready') ?>">
                    <i class="fa fa-car"></i> <span><?= Yii::$app->mv->gt('Машины на отправку', [], false) ?></span>
                </a>
            </li>
            <li<?= ($cont == 'lines' && ($act == 'vehicles-trip')) ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute('/admin/lines/vehicles-trip') ?>">
                    <i class="fa fa-car"></i> <span><?= Yii::$app->mv->gt('Машины в пути', [], false) ?></span>
                </a>
            </li>
            <li<?= ($cont == 'trips' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute('/admin/trips/index') ?>">
                    <i class="fa fa-users"></i> <span><?= Yii::$app->mv->gt('Пассажиры в очереди', [], false) ?></span>
                </a>
            </li>

            <li class="header"><?= Yii::$app->mv->gt('Автомобили', [], false); ?></li>
            <li class="treeview<?= (($cont == 'vehicles' || ($cont == 'lines' && $act == 'vehicles')) && $act != 'vehicles') ? ' active' : '' ?>">
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

            <div class="hidden">
            <li class="treeview<?= ($cont == 'trips') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-users"></i>
                    <span><?= Yii::$app->mv->gt('Поездки', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'trips' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/trips/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'trips' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/trips/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'vehicles' || ($cont == 'lines' && $act == 'vehicles')) ? ' active' : '' ?>">
                <a href="<?= Url::toRoute('/admin/vehicles') ?>">
                    <i class="fa fa-car"></i>
                    <span><?= Yii::$app->mv->gt('Автомобили', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'lines' && ($act == 'vehicles')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lines/vehicles') ?>">
                            <i class="fa fa-car"></i> <?= Yii::$app->mv->gt('На линии', [], false) ?>
                        </a>
                    </li>
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
            <li class="treeview<?= ($cont == 'lines' && $act != 'vehicles') ? ' active' : '' ?>">
                <a href="<?= Url::toRoute('/admin/lines') ?>">
                    <i class="fa fa-map-marker"></i>
                    <span><?= Yii::$app->mv->gt('Маршруты', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'lines' && ($act == 'routes' || $act == 'route')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lines/routes') ?>">
                            <i class="fa fa-arrows-h"></i> <?= Yii::$app->mv->gt('Список маршрутов', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'lines' && ($act == 'checkpoints' || $act == 'checkpoint')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lines/checkpoints') ?>">
                            <i class="fa fa-map-marker"></i> <?= Yii::$app->mv->gt('Контрольные точки', [], false) ?>
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
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
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
            <li class="treeview<?= ($cont == 'dynamic') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-th-list"></i>
                    <span><?= Yii::$app->mv->gt('Динамические страницы', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'dynamic' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/dynamic/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    <li<?= ($cont == 'dynamic' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/dynamic/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'dispatch') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-th-list"></i>
                    <span><?= Yii::$app->mv->gt('Диспетчеры', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'dispatch' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/dispatch/index') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    <li<?= ($cont == 'dispatch' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/dispatch/create') ?>"><i
                                    class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            </div>
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

