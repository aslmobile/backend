<?php namespace app\modules\api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use dosamigos\transliterator\TransliteratorHelper;

/**
 * This is the model class for table "uploaded_files".
 *
 * @property int $id
 * @property string $file
 * @property int $created_at
 * @property int $updated_at
 */
class UploadFiles extends ActiveRecord
{
    const MAX_FILE_SIZE_MB = 16;

    public $extensions = ['png', 'jpg', 'jpeg'];
    public $path = '';

    public static function tableName()
    {
        return 'uploaded_files';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @var UploadedFile $uploadedFile
     */
    public $uploadedFile;

    public function rules()
    {
        return [
            ['id', 'integer'],
            [['file'], 'required'],
            [['file'], 'string']
        ];
    }

    /**
     * @param $path
     * @return bool
     */
    public static function validatePath($path)
    {
        if (!file_exists($path) && !@mkdir($path, 0777, true) && !is_dir($path)) return false;
        return true;
    }

    public function setPath($_path)
    {
        $_path = Yii::getAlias('@webroot' . $_path);

        if (self::validatePath($_path))
        {
            $this->path = $_path . DIRECTORY_SEPARATOR;
            return $this->path;
        }

        return false;
    }

    public function upload()
    {
        if ($this->validate())
        {
            $fileName = mb_strtolower(time() . '_' . $this->uploadedFile->baseName);
            $fileName = \Yii::$app->mv->transliterateUrl(str_ireplace(' ', '_', $fileName))  . '.' . $this->uploadedFile->extension;

            $this->uploadedFile->saveAs($this->path . $fileName);
            $filePath = str_replace([Yii::getAlias('@webroot'), '\\'], ['', '/'], $this->path) . $fileName;

            $id = $this->afterUpload($filePath);
            return [
                'file' => $filePath,
                'file_id' => intval($id)
            ];
        }

        return false;
    }

    public function afterUpload($file)
    {
        $this->file = $file;
        $this->save();

        return $this->id;
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return ($this->validateExtension($this->uploadedFile) == true && $this->validateSize($this->uploadedFile) == true);
    }

    /**
     * @param $st
     * @return mixed
     */
    public static function transliterate($st)
    {
        return TransliteratorHelper::process($st, '', 'en');
    }

    /**
     * @param $st
     * @return mixed|null|string|string[]
     */
    public static function transliterateUrl($st)
    {
        $st = strip_tags($st);
        $st = self::transliterate($st);
        $st = trim(mb_strtolower($st));
        $st = str_replace([' '], '-', $st);
        $st = preg_replace("/[^a-z0-9\/_-]+/", '', $st);
        return $st;
    }

    public function validationErrors()
    {
        $result = new \StdClass();
        if (!$this->validate()) {
            if (!$this->validateExtension($this->uploadedFile)) $result->format = 'Allowed extensions: ' . implode(', ', $this->extensions);
            if (!$this->validateSize($this->uploadedFile)) $result->size = 'File is too big, max size: ' . self::MAX_FILE_SIZE_MB . 'MB';

            return $result;
        }

        return true;
    }

    /**
     * Checks if given uploaded file have correct type (extension) according current validator settings.
     * @param UploadedFile $file
     * @return bool
     */
    protected function validateExtension($file)
    {
        $extension = mb_strtolower($file->extension, 'UTF-8');
        if (!in_array($extension, $this->extensions, true)) return false;
        return true;
    }

    /**
     * Checks if given uploaded file have correct size according current validator settings.
     * @param UploadedFile $file
     * @return bool
     */
    protected function validateSize($file)
    {
        if ($file->size == 0 || $file->size > (1024 * 1024 * self::MAX_FILE_SIZE_MB)) return false;
        return true;
    }
}
