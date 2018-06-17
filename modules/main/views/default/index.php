<?php
/**
 * @var \yii\web\View $this
 */
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);

$categories = \app\models\Category::findAll([
        'status' => \app\models\Category::STATUS_ACTIVE
])
?>
<div class="section container-fluid first-sec mp-sect" id="first">
    <div class="row no-gutters">
        <div class="col-md-12 col-lg-7 info-wrap">
            <div class="info-block" data-aos="fade-right" style="background-image:url(<?=Yii::$app->controller->getTune(19,false); ?>);">
                <div class="info">
                    <div class="info-item">
                        <p class="num"><?=Yii::$app->controller->getTune(25, false);?></p>
                        <p class="desc"><?=Yii::$app->controller->getTune(21, false);?></p>
                        <p class="tyr"><?=Yii::$app->controller->getTune(20,false);?></p>
                    </div>
                    <div class="info-item">
                        <p class="num"><?=Yii::$app->controller->getTune(26, false);?></p>
                        <p class="desc"><?=Yii::$app->controller->getTune(22,false);?></p>
                        <p class="tyr"><?=Yii::$app->controller->getTune(20,false);?></p>
                    </div>
                    <div class="info-item">
                        <p class="num"><?=Yii::$app->controller->getTune(27, false);?></p>
                        <p class="desc"><?=Yii::$app->controller->getTune(23,false);?></p>
                        <p class="tyr"><?=Yii::$app->controller->getTune(20,false);?></p>
                    </div>
                    <div class="info-item">
                        <p class="num"><?=Yii::$app->controller->getTune(28, false);?></p>
                        <p class="desc"><?=Yii::$app->controller->getTune(24,false);?></p>
                        <p class="tyr"><?=Yii::$app->controller->getTune(20,false);?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-5 description-wrapper" data-aos="fade-down">
            <div class="description-block">
                <div class="description-under"><?=Yii::$app->controller->getTune(29, true);?></div>
            </div>
        </div>
    </div>

    <div class="row no-gutters details-wrap">
        <div class="col-lg-12 col-lg-7">
            <div class="details-block">
                <div class="details-items" data-aos="fade-up">
                    <div class="details-item"><?=Yii::$app->controller->getTune(33, true) ?></div>
                </div>
                <div class="scroll-block" data-aos="fade" data-aos-offset="0">
                    <a class="products-sec-link" href="<?= Yii::$app->urlManager->createUrl('#products-sec') ?>"><img src="<?=Yii::$app->controller->getTune(35,false);?>" alt="mouse"></a>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-lg-5 details-img-wrap">
            <div class="details-img-block" data-aos="fade-left">
                <img src="<?=Yii::$app->controller->getTune(32,false);?>" style="center center" alt="office">
            </div>
        </div>
    </div>
</div>

<div class="section container-fluid products-sec mp-sect" id="products">
    <div class="row no-gutters products">
        <div class="col-xl-4 col-xxl-3 product-block" data-aos="fade-right"> <!-- data-aos="fade-right" -->
            <div class="product-item-text base">
                <div class="product-item-text-wrap">
                    <div class="product-title">
                        <div class="rect"></div>
                        <p><?=Yii::$app->mv->gt("Products")?></p>
                    </div>
                </div>
            </div>
            <?foreach ($categories as $category):?>
            <div class="product-item-text <?=$category->link ?>">
                <div class="product-item-text-wrap">
                    <div class="product-title">
                        <div class="rect"></div>
                        <p><?=Yii::$app->mv->gt($category->title);?></p>
                    </div>
                    <div class="product-desc"><?=$category->short_description;?></div>
                </div>
            </div>
            <?endforeach;?>
        </div>
        <div class="col-xl-8 col-xxl-9 product-items">
            <?foreach ($categories as $category):?>
            <div class="product-item <?=$category->link ?>" data-aos="fade-down" style="background-image:url(<?=$category->small_image;?>)"> <!-- data-aos="flip-left" -->
                <div class="product-item-title">
                    <p><?=$category->title?></p>
                    <div class="product-btn">
                        <a href="<?=\yii\helpers\Url::toRoute(['/main/default/products', 'category_id' => $category->id]);?>">
                            <img src="<?=Yii::$app->controller->getTune(40,false);?>" alt="btn">
                        </a>
                    </div>
                </div>
            </div>
            <?endforeach;?>
        </div>
    </div>
</div>

<div class="section container-fluid about-sec mp-sect" id="about">
    <div class="row no-gutters about">
        <div class="col-xl-4 col-xxl-3 about-block">
            <div data-aos="fade-right"> <!-- data-aos="fade-right" -->
                <div class="about-title">
                    <div class="rect"></div>
                    <p><?=Yii::$app->mv->gt("About")?></p>
                </div>
                <p class="about-desc"><?=Yii::$app->controller->getTune(49,true);?></p>
            </div>
        </div>
        <div class="col-xl-8 col-xxl-9 about-items">
            <div class="about-item" data-aos="fade-down"> <!-- data-aos="fade-down" -->
                <p class="about-item-title"><?=Yii::$app->controller->getTune(50,true);?></p>
                <p class="about-item-date"><?=Yii::$app->controller->getTune(53,true);?></p>
                <p class="about-item-desc"><?=Yii::$app->controller->getTune(56,true);?></p>
            </div>
            <div class="about-item" data-aos="fade-down"> <!-- data-aos="fade-down" -->
                <p class="about-item-title"><?=Yii::$app->controller->getTune(51,true);?></p>
                <p class="about-item-date"><?=Yii::$app->controller->getTune(54,true);?></p>
                <p class="about-item-desc"><?=Yii::$app->controller->getTune(57,true);?></p>
            </div>
            <div class="about-item" data-aos="fade-down"> <!-- data-aos="fade-down" -->
                <p class="about-item-title"><?=Yii::$app->controller->getTune(52,true);?></p>
                <p class="about-item-date"><?=Yii::$app->controller->getTune(55,true);?></p>
                <p class="about-item-desc"><?=Yii::$app->controller->getTune(58,true);?></p>
            </div>
            <div class="about-img" data-aos="fade-up" data-aos-offset="0" style="background-image:url(<?=Yii::$app->controller->getTune(59,false); ?>);"> <!-- data-aos="fade-up" data-aos-offset="0" -->
            </div>
        </div>
    </div>
</div>