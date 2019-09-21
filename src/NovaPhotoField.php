<?php

namespace VysotskyProductions\NovaPhotoFiled;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class NovaPhotoField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'NovaPhotoField';

    public $handler;

    public function __construct($name, $attribute = null, $disk = 'public')
    {
        parent::__construct($name, $attribute);
    }

    public function handleClass($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    protected function fillAttributeFromRequest(NovaRequest $request,
                                                $requestAttribute,
                                                $model,
                                                $attribute)
    {
        $cropData = json_decode($request[$attribute . "_crop_data"]);

        $path = $this->meta['params']['folder'];
        $config = $this->meta['params']['thumbs'][$path];

        if ($request[$attribute . "_delete_id"]) {
            $model->{$attribute}()->dissociate();
            $model->save();
            $this->handler::delete($request[$attribute . "_delete_id"], $config, $path);
        }

        if ($request->file($attribute . "_file")) {

            $media = $this->handler::save(
                $request->file($attribute . "_file"),
                $cropData,
                $path,
                $config
            );
            $model->{$attribute}()->associate($media);
            $model->save();
        }

        if ($request[$attribute . "_update_id"] && $cropData) {
            $this->handler::update($request[$attribute . "_update_id"], $config, $path, $cropData);
        }
    }

    public function params(array $params)
    {
        return $this->withMeta(['params' => $params]);
    }

    public function getPhoto(string $previewUrl = null)
    {
        return $this->withMeta(compact('previewUrl'));
    }

    public function getPhotoDetail(string $previewDetailUrl = null)
    {
        return $this->withMeta(compact('previewDetailUrl'));
    }

    public function getPhotoForm(string $previewFormUrl = null)
    {
        return $this->withMeta(compact('previewFormUrl'));
    }

    public function getPhotoIndex(string $previewIndexUrl = null)
    {
        return $this->withMeta(compact('previewIndexUrl'));
    }
}
