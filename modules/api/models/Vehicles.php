<?php namespace app\modules\api\models;

use app\models\User;

class Vehicles extends \app\models\Vehicles
{
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        $type = VehicleTypes::findOne($this->vehicle_type_id);
        $model = VehicleModels::findOne($this->vehicle_model_id);
        $brand = VehicleBrands::findOne($this->vehicle_brand_id);
        $driver = User::findOne($this->user_id);

        if (!empty($type)) $array += ['type_image' => $type->image]; else $array += ['type_image' => '/files/sedan.png'];

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

            if ($field == 'rating') $array[$field] = !empty($driver) ? $driver->getRating() : 4.8;
            if ($field == 'vehicle_type_id') $array[$field] = !empty($type) ? $type->toArray() : [];
            if ($field == 'vehicle_model_id') $array[$field] = !empty($model) ? $model->toArray() : [];
            if ($field == 'vehicle_brand_id') $array[$field] = !empty($brand) ? $brand->toArray() : [];
        }

        return $array;
    }

    public function getPhotoUrl()
    {

        return $this->model->image;

    }
}
