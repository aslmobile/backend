<?php

use yii\helpers\Html;
?>

<div class="language_selector">
    <ul>
		<li><a href="javascript:void(0);" class="selected"><?= $current->name.' '.Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $current->flag, '20x') ?></a>
			<ul>
				<?php foreach ($langs as $lang) { ?>
					<li>
						<?php
							$prefix = ($lang->default == 1)?Yii::$app->getRequest()->getLangUrl():'/'.$lang->url.Yii::$app->getRequest()->getLangUrl();
							$url = (!empty($prefix))?$prefix:'/';
							echo Html::a($lang->name.' '.Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $lang->flag, '20x'), $url);
						?>
					</li>
				<?php } ?>
			</ul>
		</li>
	</ul>
</div>