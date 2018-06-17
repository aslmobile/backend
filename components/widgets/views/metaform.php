<? if(!$k):?>
	<div class="card">
		<div class="col-sm-12">
			<h2><?= Yii::$app->mv->gt("Meta",[],0) ?></h2>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input type="hidden" name="Metadata[data_type]" value="<?=$data_type?>">
				<div class="col-sm-6">
					<div class="form-group field-meta_title ">
						<label class="control-label" for="meta_title"><?=Yii::$app->mv->gt("Meta title",[],0);?></label>
						<input type="text" id="meta_title" class="form-control" name="Metadata[title]">

						<div class="help-block"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group field-meta_keywords ">
						<label class="control-label" for="meta_keywords"><?=Yii::$app->mv->gt("Meta keywords",[],0);?></label>
						<input type="text" id="meta_keywords" class="form-control" name="Metadata[keywords]">

						<div class="help-block"></div>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group field-meta_description ">
						<label class="control-label" for="meta_description"><?=Yii::$app->mv->gt("Meta description",[],0);?></label>
						<input type="text" id="meta_description" class="form-control" name="Metadata[description]">

						<div class="help-block"></div>
					</div>
				</div>
			</div>
	
		</div>
	</div>
<? else: ?>

	<div class="card">
		<div class="col-sm-12">
			<h2><?= Yii::$app->mv->gt("Meta",[],0) ?></h2>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="col-sm-6">
					<div class="form-group field-meta_title_<?=$k?> ">
						<label class="control-label" for="meta_title_<?=$k?>"><?=Yii::$app->mv->gt("Meta title",[],0);?></label>
						<input type="text" id="meta_title_<?=$k?>" class="form-control" name="Metadata[title_<?=$k?>]">

						<div class="help-block"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group field-meta_keywords_<?=$k?> ">
						<label class="control-label" for="meta_keywords_<?=$k?>"><?=Yii::$app->mv->gt("Meta keywords",[],0);?></label>
						<input type="text" id="meta_keywords_<?=$k?>" class="form-control" name="Metadata[keywords_<?=$k?>]">

						<div class="help-block"></div>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group field-meta_description_<?=$k?>">
						<label class="control-label" for="meta_description_<?=$k?>"><?=Yii::$app->mv->gt("Meta description",[],0);?></label>
						<input type="text" id="meta_description_<?=$k?>" class="form-control" name="Metadata[description_<?=$k?>]">

						<div class="help-block"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
<? endif;?>
