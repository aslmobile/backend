<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 31.05.18
 * Time: 17:12
 */
?>
<div class="section container-fluid contacts-page-sec" id="contacts-page">
    <div class="row no-gutters">
        <div class="col-12 col-md-5 col-lg-5 col-xl-4 contacts-page-wrap">
            <div class="contacts-page-block" data-aos="fade-right">
                <h1 class="contacts-page-title"><?=Yii::$app->mv->gt("Contacts")?></h1>
                <p class="contacts-page-p"><?=Yii::$app->mv->gt("is a leading independent service provider for the steel industry.");?></p>
            </div>
        </div>
        <div class="col-12 col-md-7 col-lg-7 col-xl-8 contacts-page-items">
            <div class="contacts-page-item" data-aos="fade-down">
                <div class="cpi-inn-wrap">
                    <p class="contacts-page-adress"><?= Yii::$app->controller->coreSettings->address ?></p>
                </div>
            </div>
            <div class="contacts-page-item" data-aos="fade-up">
                <div class="cpi-inn-wrap">
                    <a href="tel:<?= Yii::$app->controller->coreSettings->phone ?>"><?= Yii::$app->controller->coreSettings->phone ?></a><br>
                    <a href="tel:<?= Yii::$app->controller->coreSettings->addphone ?>"><?= Yii::$app->controller->coreSettings->addphone ?></a>
                </div>
            </div>
            <div class="contacts-page-item" data-aos="fade-down">
                <div class="cpi-inn-wrap">
                    <a href="mailto:<?= Yii::$app->controller->coreSettings->site_email ?>"><?= Yii::$app->controller->coreSettings->site_email ?></a><br>
                    <a href="mailto:<?= Yii::$app->controller->coreSettings->add_email ?>"><?= Yii::$app->controller->coreSettings->add_email ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row no-gutters">
        <div class="col-12">
            <div data-aos="fade" id="map" data-aos-duration="1300"></div>
        </div>
    </div>
</div>

