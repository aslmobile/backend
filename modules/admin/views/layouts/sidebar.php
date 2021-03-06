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
                <div class="col-sm-12 text-center"
                     style="color: white;"><?= Yii::$app->user->identity->fullName ?></div>
                <small class="col-sm-12 text-center"
                       style="color: white;"><?= Yii::$app->user->identity->email ?></small>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header"><?= Yii::$app->mv->gt('Основное управление', [], false) ?></li>
            <li<?= ($cont == 'default') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/default/index']) ?>">
                    <i class="fa fa-dashboard"></i>
                    <span><?= Yii::$app->mv->gt('Панель управления', [], false); ?></span>
                </a>
            </li>
            <li<?= ($cont == 'taxi') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/taxi/index']) ?>">
                    <i class="fa fa-taxi"></i>
                    <span><?= Yii::$app->mv->gt('Заказы Такси', [], false); ?></span>
                </a>
            </li>
            <li<?= ($cont == 'ticket') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/ticket/index']) ?>">
                    <i class="fa fa-ticket"></i>
                    <span><?= Yii::$app->mv->gt('Заявки вывода средств', [], false); ?></span>
                </a>
            </li>
            <li class="header"><?= Yii::$app->mv->gt('Контент', [], false); ?></li>
            <li class="treeview<?= ($cont == 'legal') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-file-text"></i>
                    <span><?= Yii::$app->mv->gt('Юр. информация', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'legal' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/legal/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'legal' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/legal/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'agreement') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-file-text"></i>
                    <span><?= Yii::$app->mv->gt('Польз. соглашение', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'agreement' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/agreement/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'agreement' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/agreement/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'sms-templates') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-file-text"></i>
                    <span><?= Yii::$app->mv->gt('Шаблоны SMS', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'sms-templates' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/sms-templates/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'sms-templates' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/sms-templates/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'mailtpl') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-envelope"></i>
                    <span><?= Yii::$app->mv->gt('Шаблоны писем', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'mailtpl' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/mailtpl/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'mailtpl' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/mailtpl/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'faq') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-question"></i>
                    <span><?= Yii::$app->mv->gt('Помощь', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'faq' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/faq/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'faq' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/faq/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'answers') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-question"></i>
                    <span><?= Yii::$app->mv->gt('Причины отмены', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'answers' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/answers/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'answers' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/answers/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
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

            <!-- --------------------------------------BOTS BEGIN-------------------------------------------->
            <li class="header"><?= Yii::$app->mv->gt('Боты', [], false); ?></li>

            <li class="treeview<?= ($cont == 'bot-trip') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-android"></i>
                    <span><?= Yii::$app->mv->gt('Поездки', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'bot-trip' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/bot-trip/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'bot-trip' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/bot-trip/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>

<!--            <li--><?//= ($cont == 'bots' && ($act == 'index')) ? ' class="active"' : '' ?><!-->
<!--                <a href="--><?//= Url::toRoute('/admin/bots/index') ?><!--">-->
<!--                    <i class="fa fa-tachometer"></i> <span>--><?//= Yii::$app->mv->gt('Управление', [], false) ?><!--</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li--><?//= ($cont == 'bots' && ($act == 'driver')) ? ' class="active"' : '' ?><!-->
<!--                <a href="--><?//= Url::toRoute('/admin/bots/driver') ?><!--">-->
<!--                    <i class="fa fa-taxi"></i> <span>--><?//= Yii::$app->mv->gt('Водитель', [], false) ?><!--</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li--><?//= ($cont == 'bots' && ($act == 'passenger')) ? ' class="active"' : '' ?><!-->
<!--                <a href="--><?//= Url::toRoute('/admin/bots/passenger') ?><!--">-->
<!--                    <i class="fa fa-user"></i> <span>--><?//= Yii::$app->mv->gt('Пассажир', [], false) ?><!--</span>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li--><?//= ($cont == 'bots' && ($act == 'transactions')) ? ' class="active"' : '' ?><!-->
<!--                <a href="--><?//= Url::toRoute('/admin/bots/transactions') ?><!--">-->
<!--                    <i class="fa fa-money"></i> <span>--><?//= Yii::$app->mv->gt('Транзакции', [], false) ?><!--</span>-->
<!--                </a>-->
<!--            </li>-->
            <!-- --------------------------------------BOTS END----------------------------------------------->

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
            <li class="header"><?= Yii::$app->mv->gt('Настройки', [], false) ?></li>
            <li<?= ($cont == 'settings') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/settings/index']) ?>">
                    <i class="fa fa-cogs"></i>
                    <span><?= Yii::$app->mv->gt('Настройки', [], false); ?></span>
                </a>
            </li>
            <li class="treeview<?= ($cont == 'translations' || $cont == 'message') ? ' active' : '' ?>">
                <a href="#">
                    <i class="fa fa-language" aria-hidden="true"></i>
                    <span><?= Yii::$app->mv->gt('Переводы', [], false); ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'translations' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/translations/index') ?>">
                            <i class="fa fa-th-list"></i>
                            <?= Yii::$app->mv->gt('Динамические', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'translations' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/translations/create') ?>">
                            <i class="fa fa-plus"></i>
                            <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'translations' && $act == 'edit') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/translations/edit') ?>">
                            <i class="fa fa-pencil"></i>
                            <?= Yii::$app->mv->gt('Изменить', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'message' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/message/index') ?>">
                            <i class="fa fa-th-list"></i>
                            <?= Yii::$app->mv->gt('Статические', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'message' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/message/create') ?>">
                            <i class="fa fa-plus"></i>
                            <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li<?= ($cont == 'file') ? ' class="active"' : '' ?>>
                <a href="<?= Url::toRoute(['/admin/file/index']) ?>">
                    <i class="fa fa-folder-open fa-fw"></i>
                    <span><?= Yii::$app->mv->gt('Файловый менеджер', [], false); ?></span>
                </a>
            </li>
            <li class="treeview<?= ($cont == 'lang') ? ' active' : '' ?>">
                <a href="#">
                    <i class="fa fa-file-text"></i>
                    <span><?= Yii::$app->mv->gt('Языки', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'lang' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lang/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'lang' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/lang/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новый', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'dynamic') ? ' active' : '' ?>">
                <a href="#">
                    <i class="fa fa-file-text"></i>
                    <span><?= Yii::$app->mv->gt('Страницы', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'dynamic' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/dynamic/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'dynamic' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/dynamic/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Новый', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview<?= ($cont == 'cities') ? ' active' : '' ?>">
                <a href="#"> <i class="fa fa-map-marker"></i>
                    <span><?= Yii::$app->mv->gt('Города', [], false); ?></span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li<?= ($cont == 'cities' && ($act == 'index' || $act == 'update' || $act == 'view')) ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/cities/index') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Список', [], false) ?>
                        </a>
                    </li>
                    <li<?= ($cont == 'cities' && $act == 'create') ? ' class="active"' : '' ?>>
                        <a href="<?= Url::toRoute('/admin/cities/create') ?>">
                            <i class="fa fa-circle-o"></i> <?= Yii::$app->mv->gt('Добавить', [], false) ?>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <div style="height: 80px;"></div>
    </section>
    <!-- /.sidebar -->
</aside>

<?= $content ?>

<?php $this->endContent(); ?>

