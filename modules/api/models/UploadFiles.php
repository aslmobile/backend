<?php namespace app\modules\api\models;

use Yii;
use yii\base\DynamicModel;
use yii\web\UploadedFile;
use dosamigos\transliterator\TransliteratorHelper;

class UploadFiles extends DynamicModel
{
    const MAX_FILE_SIZE_MB = 16;

    public $extensions = ['png', 'jpg', 'jpeg'];
    public $path = '';

    /**
     * @var UploadedFile
     */
    public $file;

    public function rules()
    {
        return [];
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
            $fileName = mb_strtolower(time() . '_' . $this->file->baseName);
            $fileName = \Yii::$app->mv->transliterateUrl(str_ireplace(' ', '_', $fileName))  . '.' . $this->file->extension;

            $this->file->saveAs($this->path . $fileName);
            $filePath = str_replace([Yii::getAlias('@webroot'), '\\'], ['', '/'], $this->path) . $fileName;

            return $filePath;
        }

        return false;
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return ($this->validateExtension($this->file) == true && $this->validateSize($this->file) == true);
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
            if (!$this->validateExtension($this->file)) {
                $result->format = 'Allowed extensions: ' . implode(', ', $this->extensions);
            }
            if (!$this->validateSize($this->file)) {
                $result->size = 'File is too big, max size: ' . self::MAX_FILE_SIZE_MB . 'MB';
            }
            return $result;
        }
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