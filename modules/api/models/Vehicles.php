<?php namespace app\modules\api\models;

class Vehicles extends \app\models\Vehicles
{
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        $images = ['image', 'insurance', 'registration', 'registration2'];
        foreach ($images as $field) {
            if (!empty ($this->$field) && intval($this->$field) > 0) {
                $file = UploadFiles::findOne($this->$field);
                if ($file) $array[$field . '_url'] = $file->file;
                else $array[$field . '_url'] = null;
            } else $array[$field . '_url'] = null;
        }

        $array['photos_url'] = null;

        if (isset ($array['photos']) && !empty ($array['photos'])) {
            $array['photos_url'] = $this->getVehiclePhotos();
            $array['photos'] = array_map(function ($id) {
                $file = UploadFiles::findOne(intval($id));
                if ($file) return [
                    'id' => $file->id,
                    'url' => $file->file
                ];

                return null;
            }, explode(',', $array['photos']));
        }

        foreach ($array as $field => $value) {
            if ($field == 'vehicle_type_id') $array[$field] = VehicleTypes::findOne($this->vehicle_type_id)->toArray();
            if ($field == 'vehicle_model_id') $array[$field] = VehicleModels::findOne($this->vehicle_model_id)->toArray();
            if ($field == 'vehicle_brand_id') $array[$field] = VehicleBrands::findOne($this->vehicle_brand_id)->toArray();
        }

        return $array;
    }

    public function getPhotoUrl()
    {
        if ($this->image && !empty ($this->image)) {

        }

        return $this->model->image;
    }
}
