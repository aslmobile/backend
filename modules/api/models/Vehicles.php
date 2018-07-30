<?php namespace app\modules\api\models;

class Vehicles extends \app\models\Vehicles
{
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        $images = ['image', 'insurance', 'registration', 'registration2'];
        foreach ($images as $field)
        {
            if (!empty ($this->$field) && intval($this->$field) > 0)
            {
                $file = UploadFiles::findOne($this->$field);
                if ($file)
                {
                    $array[$field . '_url'] = $file->file;
                }
                else $array[$field . '_url'] = null;
            }
            else $array[$field . '_url'] = null;
        }

        return $array;
    }
}