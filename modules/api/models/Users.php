<?php namespace app\modules\api\models;

class Users extends \app\models\User
{
    public function getScheme()
    {
        $schema = <<<'JSON'
{
    "type": "object",
    "properties": {
        "id": {"type": "integer"},
        "email": {"type": "string","format": "email"},
        "first_name": {"type": "string"},
        "second_name": { "type": "string"},
        "created_at": {"type": "integer"},
        "created_by": {"type": "integer"},
        "updated_at": {"type": "integer"},
        "updated_by": {"type": "integer"},
        "approval_at": {"type": "integer"},
        "approval_by": {"type": "integer"},
        "blocked_at": {"type": "integer"},
        "blocked_by": {"type": "integer"},
        "country_id": {"type": "integer"},
        "city_id": {"type": "integer"},
        "gender": {"type": "integer"},
        "status": {"type": "integer"},
        "type": {"type": "integer"},
        "phone": {"type": "string", "format": "phone"}
    }
}
JSON;

        return $schema;
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);
        if (isset ($array['phone']) && !empty ($array['phone'])) $array['phone'] = (string) $array['phone'];

        $image_file = UploadFiles::findOne($this->image);
        if ($image_file)
        {
            $array['image_url'] = $image_file->file;
        }
        else $array['image_url'] = null;

        return $array;
    }
}