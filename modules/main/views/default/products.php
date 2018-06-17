<?php
/* @var $this yii\web\View */
?>
<div class="section container-fluid products-page-sec">
    <div class="row no-gutters products-page">
        <div class="col-lg-4 col-xl-4 col-xxl-3 products-page-block">
            <div data-aos="fade-right">
                <div class="products-page-title">
                    <div class="rect"></div>
                    <p><?=$category->title?> <?=Yii::$app->mv->gt("Products")?></p>
                </div>
                <ul class="products-page-links">

                    <? $number = 0 ?>
                    <? foreach ($products as $product):?>
                        <?php $number++?>
                        <li class="products-page-link">
                        <?php if($number<10):?>
                            <span><?='0'.$number ?></span>
                        <?php else: ?>
                            <span><?=$number ?></span>
                        <?php endif ?>
                        <a><?= $product['title'] ?></a>
                        </li>
                    <? endforeach; ?>

                </ul>
            </div>
        </div>
        <div class="col-12 col-md-12 col-lg-8 col-xl-8 col-xxl-9 products-page-content">
            <div class="products-page-img" data-aos="fade-left">
                <img src="<?=$category->image ?>" alt="steel">
            </div>
            <div class="products-page-desc" data-aos="fade-up">
                <div class="products-page-text">
                    <?=$category->description  ?>
                </div>
                <div class="products-page-btn">
                    <label>Downloads</label>
                    <a href="<?= $category->file ?>" target="_blank"><img src="<?=Yii::$app->controller->getTune(64,false)?>" alt="download"></a>
                </div>
            </div>
        </div>
    </div>
</div>
