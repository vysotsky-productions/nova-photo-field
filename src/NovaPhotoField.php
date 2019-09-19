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

    protected function fillAttributeFromRequest(NovaRequest $request,
                                                $requestAttribute,
                                                $model,
                                                $attribute)
    {

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
