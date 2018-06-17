<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "Fields".
 *
 * @property integer $id
 * @property string $folder
 * @property string $fields
 */
class Fields extends \yii\db\ActiveRecord
{
	public $sel;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fields'], 'string'],
            [['folder'], 'unique'],
            [['folder'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'folder' => 'Модель',
            'fields' => 'Поля',
        ];
    }
	
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			if(isset($_POST['Fields'],$_POST['Fields']['fdata'])){
				$this->fields = json_encode($_POST['Fields']['fdata']);
			}
			
			return true;
		}
		return false;
	}
	
	public function afterSave($insert, $changedAttributes){
		parent::afterSave($insert, $changedAttributes);
		if($this->fields){
		$backslash = '\\';
		$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/modules/admin/views/'.$this->folder.'/_form.php');
		$oldfilename = 'modules-admin-views-'.$this->folder.'-_form__'.date('d.m.Y-H.i.s').'.php';
		if($file){
            $modelname = explode('-' , $this->folder);
            foreach($modelname as &$m){
                $m = ucfirst($m);
            }
			$modelname = implode('', $modelname);
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/backupgen/'.$oldfilename, $file);
			$file = explode('<div class="col-sm-6">', $file);
			$filestart = $file[0];
			unset($file[0]);
			$file = implode('<div class="col-sm-6">', $file);
			$fileend = explode('</div>',$file);
			unset($fileend[0]);
			$fileend = implode('</div>', $fileend);
$fields = '';
			
			$beh = array();
			$behfields = '';
			if(strpos($fileend,'<?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>')!==false){
				$beh = explode('<?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>', $fileend);
			}
			
			foreach(json_decode($this->fields, true) as $field=>$data){
				if(isset($data['onform']) && $data['onform']){
				switch($data['type']){
					case 'textInput':
$ff=<<<HTML

<?= \$form->field(\$model, '$field', ['template' => '{input}{label}{error}{hint}'])->textInput(['maxlength' => true]) ?>


HTML;
					break;
					case 'image':
$ff=<<<HTML
<?= Yii::\$app->imageCache->img(\$_SERVER['DOCUMENT_ROOT'].'/web'.\$model->$field,'140x140', ['class'=>'img-circle']) ?>

<?= \$form->field(\$model, '$field')->widget(
	ElFinderInput::className(),
	[
		'connectorRoute' => 'el-finder/connector',
	]
) ?>


HTML;
					break;
					case 'file':
$ff=<<<HTML

<?= \$form->field(\$model, '$field')->widget(
	ElFinderInput::className(),
	[
		'connectorRoute' => 'el-finder/connector',
	]
) ?>


HTML;
					break;
					case 'textarea':
$ff=<<<HTML

<?= \$form->field(\$model, '$field', ['template' => '{input}{label}{error}{hint}'])->textarea(['rows' => 6]) ?>


HTML;
					break;
					case 'textareaMce':
$ff=<<<HTML

<?= \$form->field(\$model, '$field')->widget(
	TinyMce::className(),
	[
		'fileManager' => [
			'class' => TinyMceElFinder::className(),
			'connectorRoute' => 'el-finder/connector',
		],
	]
) ?>	


HTML;
					break;
					case 'select':
if(substr($data['vars'],0,5)=='model'){
	$mdata = explode(',', $data['vars']);
	$ava = "ArrayHelper::merge([0 => '(Не выбрано)'] , ArrayHelper::map(".$mdata[1]."::find()->all(), '".$mdata[2]."', '".$mdata[3]."'))";
	if(strpos($filestart,'models\\'.$mdata[1])===false){
$from = 'use yii\helpers\Html;';
$to = <<<HTML
use yii\helpers\Html;
use app\modules\admin\models$backslash$mdata[1];
HTML;
		$filestart = str_replace($from, $to, $filestart);
	}
}else{
	$ava = preg_split('/\r\n|[\r\n]/', $data['vars']);
	$ava = "['" . implode("', '", $ava) . "']";
}
$skobka = '[';
$ff=<<<HTML

<?= \$form->field(\$model, '$field',['template' => '{input}{label}{error}{hint}'])->dropdownList($ava, ['name' => '$modelname$skobka$field]']); ?>	


HTML;

					break;
					case 'date':
$ff=<<<HTML

<?= \$form->field(\$model, '$field', ['template' => '{input}{label}{error}{hint}'])->widget(\yii\jui\DatePicker::classname(), [
'language' => 'ru',
'options' => [
 'class' => 'form-control'       
],
'clientOptions' => [
 'changeMonth' => true,
 'changeYear'=> true,
 'showButtonPanel' => true,    
],
'dateFormat' => 'dd.MM.yyyy',
]) ?>


HTML;
					break;
					
					}
					$pod = '_';
					$fields.=$ff;
						if(count($beh) && strpos($beh[1], "'$field$pod'.\$k")!==false){
							$behfields.= str_replace("'$field'","'$field$pod'.\$k",$ff);
						}
				}else{
					$pod = '_';
						if(count($beh) && strpos($beh[1], "'$field$pod'.\$k")!==false){
							$behfields.= "<!--place for '$field$pod'.\$k-->";
						}
				}
			}
			
			
			//behaviors
				if(count($beh) && strpos($fileend,'<?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>')!==false){
					$behstart = $beh[0];
					$temp = explode('<?php } ?>', $fileend);
					unset($temp[0]);
					$behend = implode('<?php } ?>', $temp);
					
$behfields = <<<HTML
<?php foreach (Lang::getBehaviorsList() as \$k => \$v) { ?>
				<div class="tab-pane" id="<?= \$k ?>">
					<div class="card-body floating-label">
						$behfields
					</div>
				</div>
<?php } ?>
HTML;
					$fileend = $behstart.$behfields.$behend;
				}
			//behaviors
			
			
			$fields = '<div class="col-sm-6">'.$fields.'</div>';
			$file = $filestart.$fields.$fileend;
			#echo $file;
			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/modules/admin/views/'.$this->folder.'/_form.php', $file);
			#die();
			
			
			
			
			$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/modules/admin/views/'.$this->folder.'/index.php');
			$oldfilename = 'modules-admin-views-'.$this->folder.'-index__'.date('d.m.Y-H.i.s').'.php';
			if($file){
			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/backupgen/'.$oldfilename, $file);
				if(strpos($file, '/***GENERATED! DO NOT DELETE THIS COMMENT***/')){
					$file = explode('/***GENERATED! DO NOT DELETE THIS COMMENT***/', $file);
					$filestart = $file[0];
					$fileend = end($file);
				}else{
					$file = explode("['class' => 'yii\grid\CheckboxColumn'],", $file);
					$filestart = $file[0];
$filestart.=<<<HTML
['class' => 'yii\grid\CheckboxColumn'],

HTML;
					$fileend = end($file);

$e = <<<HTML
[
											'class' => 'yii\grid\ActionColumn',
HTML;
                    if(strpos($fileend, $e)){
                        $fileend = explode($e, $fileend);
                        $fileend = $fileend[1];

                        $fileend=$e . $fileend;
                    }
				}
				
				$fields = '';
				
	if(strpos($filestart,'use yii\helpers\ArrayHelper;')===false){
$from = 'use yii\helpers\Html;';
$to = <<<HTML
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
HTML;
		$filestart = str_replace($from, $to, $filestart);
	}
				foreach(json_decode($this->fields, true) as $field=>$data){
					if(isset($data['onmain']) && $data['onmain'])
				switch($data['type']){
					case 'textInput':
$fields.=<<<HTML

									'$field',

HTML;
					break;
					case 'image':
$fields.=<<<HTML

									[
										'attribute' => '$field',
										'filter' => true,
										'format' => 'html',
										'value' => function(\$data) {  
											return Yii::\$app->imageCache->img(\$_SERVER['DOCUMENT_ROOT'].'/web'.\$data->$field,'100x100', ['class'=>'img-circle width-1']); 
										},
									],

HTML;
					break;
					case 'file':
$fields.=<<<HTML

									[
										'attribute' => '$field',
										'filter' => true,
										'format' => 'html',
										'content' => function(\$data) {
											return '<a href="'.\$data->$field.'" target="_blank">'.basename(\$data->$field).'</a>';
										}
									],


HTML;
					break;
					case 'textarea':
$fields.=<<<HTML

									[
										'attribute' => '$field',
										'filter' => true,
										'format' => 'html',
										'content' => function(\$data) {
											return nl2br(\$data->$field);
										}
									],

HTML;
					break;
					case 'textareaMce':
$fields.=<<<HTML

									[
										'attribute' => '$field',
										'filter' => true,
										'format' => 'html',
										'content' => function(\$data) {
											return \$data->$field;
										}
									],


HTML;
					break;
					case 'select':
if(substr($data['vars'],0,5)=='model'){
	$mdata = explode(',', $data['vars']);
	$ava = "ArrayHelper::merge([0 => '(Не выбрано)'] , ArrayHelper::map(".$mdata[1]."::findAll(['id' => \$data->$field]), '".$mdata[2]."', '".$mdata[3]."'))";
	if(strpos($filestart,'models\\'.$mdata[1])===false){
$from = 'use yii\helpers\Html;';
$to = <<<HTML
use yii\helpers\Html;
use app\modules\admin\models$backslash$mdata[1];
HTML;
		$filestart = str_replace($from, $to, $filestart);
	}
}else{
	$ava = preg_split('/\r\n|[\r\n]/', $data['vars']);
	$ava = "['" . implode("', '", $ava) . "']";
}
$fields.=<<<HTML

									[
										'attribute' => '$field',
										'filter' => $ava,
										'format' => 'html',
										'content' => function(\$data) {
											\$arr = $ava;
											if(isset(\$arr[\$data->$field])){
												return \$arr[\$data->$field];
											}else{
												return '(не задано)';
											}
										}
									],


HTML;

					break;
					case 'date':
$fields.=<<<HTML

									'$field:datetime',

HTML;
					break;
				}
			}
			
$fields = <<<HTML

								/***GENERATED! DO NOT DELETE THIS COMMENT***/
									$fields
								/***GENERATED! DO NOT DELETE THIS COMMENT***/

HTML;
				
				$file = $filestart.$fields.$fileend;
				#echo $file;
				file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/modules/admin/views/'.$this->folder.'/index.php', $file);
				#die();
				
				/*Создание полей поиска*/
					$file = @file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/modules/admin/models/'.$modelname.'Search.php');
					if($file){
					    $from = [];
					    $to = [];
if(!strpos($file, 'GENERATED FILTERS')){

$from[] = <<<HTML
public function rules()
    {
        return [
HTML;

$to[] = <<<HTML
public function rules()
    {
        return [
            /*GENERATED FILTERS START*/
            /*GENERATED FILTERS FINISH*/

HTML;

}
$file = str_replace($from, $to, $file);


$filterWhere = [];
$filterWhereLike = [];

    $fields = [
        'integer' => [],
        'safe' => [],
        'number' => [],
    ];

    $fieldTypes = [
        'integer' => 'integer',
        'float' => 'number',
        'double' => 'number',
    ];

    foreach(json_decode($this->fields, true) as $field=>$data) {
        $absModelname = '\app\modules\admin\models\\' . $modelname;
        $model = new $absModelname;
        $modelnameSearch = $absModelname.'Search';
        $modelSearch = new $modelnameSearch;
        $type = $model->getTableSchema()->getColumn($field)->type;
        $has = false;

        foreach($modelSearch->rules() as $r){
            if(is_array($r[0])){
                if(in_array($field, $r[0])){
                    $has = true;
                }
            }elseif($r[0] == $field){
                $has = true;
            }
        }

        if(!$has) {
            $t = 'safe';
            if(isset($fieldTypes[$type]) && $data['type'] != 'date'){
                $t = $fieldTypes[$type];
            }
            $fields[$t][] = $field;
        }

        /*adding filters*/

        if(in_array($type, ['text', 'string'])){
            $filterWhereLike[] = $field;
        }else{
            $filterWhere[] = $field;
        }

        /*adding filters*/

    }

$lines = [];

    foreach($fields as $k=>$v){
        if($v) {
            $v = "'" . implode("','", $v) . "'";
$lines[] = <<<HTML
[[$v], '$k'],
HTML;
        }

    }
if($lines){
$imp = <<<HTML

            
HTML;

$lines = implode($imp, $lines);
$from = <<<HTML
/*GENERATED FILTERS START*/
HTML;
$to = <<<HTML
/*GENERATED FILTERS START*/
            $lines
HTML;
    $file = str_replace($from, $to, $file);

    /*writng filter where*/
}
        if(!strpos($file,'/*GENERATED FILTER WHERE START*/')){
$from = <<<HTML

        return \$dataProvider;
    }
}
HTML;
$to = <<<HTML

/*GENERATED FILTER WHERE START*/
/*GENERATED FILTER WHERE FINISH*/


        return \$dataProvider;
    }
}
HTML;
            $file = str_replace($from, $to, $file);
        }

        $fileAll = $file;
        $whereStart = explode('/*GENERATED FILTER WHERE START*/', $fileAll);
        $whereStart = $whereStart[0];

        $whereFinish = explode('/*GENERATED FILTER WHERE FINISH*/', $fileAll);
        $whereFinish = $whereFinish[1];

$filterWhereCont = <<<HTML

/*GENERATED FILTER WHERE START*/
/*GENERATED FILTER WHERE FINISH*/

HTML;

        $file = $whereStart . $filterWhereCont . $whereFinish;
$fieldsJson = json_decode($this->fields, true);

        foreach($filterWhere as $k=>&$v){
            if($fieldsJson[$v]['type']=='date') {
                $s = '\'' . $v . '\' => ($this->' . $v . '),';
            }else{
                $s = '\''.$v.'\' => $this->'.$v.',';
            }
            if(strpos($file, $s)){
                unset($filterWhere[$k]);
            }else{
                $v = $s;
            }
        }

        foreach($filterWhereLike as $k=>&$v){
            $s = '->andFilterWhere([\'like\', \'' . $v . '\', $this->' . $v . '])';

            if(strpos($file, $s)){
                unset($filterWhereLike[$k]);
            }else{
                $v = $s;
            }
        }


    if($filterWhere){
$g = <<<HTML


HTML;
        $j = implode($g, $filterWhere);

        $from = <<<HTML
/*GENERATED FILTER WHERE START*/
HTML;
        $to = <<<HTML
/*GENERATED FILTER WHERE START*/
\$query->andFilterWhere([
    $j
]);

HTML;
        $file = str_replace($from, $to, $file);
    }

    if($filterWhereLike){
$g = <<<HTML


HTML;
        $j = implode($g, $filterWhereLike);

        $from = <<<HTML
/*GENERATED FILTER WHERE START*/
HTML;
        $to = <<<HTML
/*GENERATED FILTER WHERE START*/
\$query$j;

HTML;
        $file = str_replace($from, $to, $file);
    }

    /*writng filter where*/


    $file = @file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/modules/admin/models/'.$modelname.'Search.php', $file);

					}
				/*Создание полей поиска*/
				
			}
			
			
		}
		}
	}
	
}
