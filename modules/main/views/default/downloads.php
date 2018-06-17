<?php
/**
 * @var \yii\web\View $this
 */
use app\assets\AppAsset;

AppAsset::register($this);
?>

<div class="section downloads-sec-padding" id="download"> <!-- Tag for full page section -->

    <div class="container-fluid downloads-sec">
        <div class="row no-gutters downloads">
            <div class="col-lg-12 col-xl-3 col-xxl-3 downloads-block">
                <div class="downloads-title" data-aos="fade-right">
                    <div class="rect"></div>
                    <p><?= Yii::$app->mv->gt("Downloads") ?></p>
                </div>
                <p class="downloads-desc"></p>
            </div>

            <div class="col-lg-12 col-xl-9 col-xxl-9 download-items">
                <?php $category_title = ''; ?>
                <? foreach ($downloads as $download): ?> <!--data-aos="fade-down" -->
                <?php if ($category_title != $download['category']['title']): ?>
                <?php if ($category_title != ''): ?>
            </div>
            <?php endif; ?>
            <div class="download-item">
                <div class="di-in-wrap">
                    <h2><?= $download['category']['title'] ?></h2>
                    <?php endif; ?>
                    <a href="<?= $download['source'] ?>" target="_blank"><?= $download['title'] ?></a>
                    <span><?= $download['expansion'] ?>, <?= $download['size'] ?></span>
                    <?php if ($category_title != $download['category']['title']): ?>
                        <?php $category_title = $download['category']['title']; ?>
                    <?php endif; ?>
                </div>
                <? endforeach; ?>
            </div>
        </div>
    </div>

<? if(Yii::$app->controller->action->id != 'downloads'):?>
</div>
</div>
    <? endif;?>