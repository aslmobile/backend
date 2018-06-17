<?php
use yii\widgets\Breadcrumbs;
use yii\grid\GridView;

$this->title = Yii::$app->mv->gt("Рабочий стол",[],0);
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/adminlte/plugins/morris/morris.min.js', ['depends' => [\app\modules\admin\assets\JQueryAsset::className()]]);
$this->registerJsFile('@web/adminlte/dist/js/pages/dashboard.js', ['depends' => [\app\modules\admin\assets\JQueryAsset::className()]]);

$statuses = Yii::$app->params['statuses'];
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $this->title; ?>
        </h1>
        <?= Breadcrumbs::widget([
            'tag' => 'ol',
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
                      <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?= count(\app\modules\admin\models\User::getUserListArray()) ?></h3>
                        <p><?= Yii::$app->mv->gt('Пользователей', [], 0) ?></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
<!--                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                </div>
            </div>
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <section class="col-lg-6">

                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= Yii::$app->mv->gt('Активность', [], 0) ?></h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <!-- Morris chart - Sales -->
                        <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
                    </div>
                </div>
            </section>

            <section class="col-lg-6">

                <div class="box box-success">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <style>
                            .currency-box {
                                display: inline-block;
                                padding: 3px 0;
                                width: 12%;
                                text-align: center;
                            }
                            .currency-box.highlight {
                                background-color: whitesmoke;
                            }
                        </style>
                        <!-- Morris chart - Sales -->
                    </div>
                </div>

            </section>
            <!-- right col -->
        </div>
        <!-- /.row (main row) -->

    </section>
    <!-- /.content -->
</div>