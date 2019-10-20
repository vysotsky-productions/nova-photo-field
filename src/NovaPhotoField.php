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

    public $deletable = true;

    public $downloadable = true;

    public $useCropper = true;

    public $handler;

    public $showOnCreation = false;

    /*photo_path_attributes*/

    public $previewUrl = null;
    public $previewFormUrl = null;
    public $previewIndexUrl = null;
    public $previewDetailUrl = null;
    public $cropBoxDataAttribute = null;

    public function __construct($name, $attribute)
    {
        parent::__construct($name, $attribute);

        $this->previewUrl = config('nova-photo-field.photo_path_attributes.original_attribute');
        $this->previewFormUrl = config('nova-photo-field.photo_path_attributes.form_preview_attribute');
        $this->previewIndexUrl = config('nova-photo-field.photo_path_attributes.index_preview_attribute');
        $this->previewDetailUrl = config('nova-photo-field.photo_path_attributes.detail_preview_attribute');
        $this->cropBoxDataAttribute = config('nova-photo-field.cropper.crop_data_attribute');
    }

    /**
     * @param bool $deletable
     * @return NovaPhotoField
     */
    public function setDeletable(bool $deletable): NovaPhotoField
    {
//        todo:implement deletable logic only if deletable is true
        $this->deletable = $deletable;
        return $this;
    }

    /**
     * @param mixed $handler
     * @return NovaPhotoField
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @param bool $downloadable
     * @return NovaPhotoField
     */
    public function setDownloadable(bool $downloadable): NovaPhotoField
    {
        $this->downloadable = $downloadable;
        return $this;
    }

    /**
     * @return NovaPhotoField
     */
    public function disableCropper(): NovaPhotoField
    {
        $this->useCropper = false;
        return $this;
    }

    public function disableDownload()
    {
        $this->downloadable = false;

        return $this;
    }

    protected function fillAttributeFromRequest(NovaRequest $request,
                                                $requestAttribute,
                                                $model,
                                                $attribute)
    {
        $cropData = json_decode($request[$attribute . "_crop_data"], true);

        if ($request[$attribute . "_delete_id"]) {
            $model->{$attribute}()->dissociate();
            $model->save();
            if ($this->deletable) {
                $this->handler->delete($request[$attribute . "_delete_id"]);
            }
        }

        if ($request->file($attribute . "_file")) {

            $media = $this->handler->save(
                $request->file($attribute . "_file"),
                $cropData
            );
            $model->{$attribute}()->associate($media);
            $model->save();
        }

        if ($request[$attribute . "_update_id"] && $cropData) {
            $this->handler->update($request[$attribute . "_update_id"], $cropData);
        }
    }


    public function aspectRatio(float $aspectRatio)
    {
        return $this->withMeta(compact('aspectRatio'));
    }

    public function params(array $params)
    {
        return $this->withMeta(['params' => $params]);
    }

    public function getPhoto(string $previewUrl)
    {
        $this->previewUrl = $previewUrl;
        return $this;
    }

    public function getPhotoDetail(string $previewDetailUrl)
    {
        $this->previewDetailUrl = $previewDetailUrl;
        return $this;
    }

    public function getPhotoForm(string $previewFormUrl)
    {
        $this->previewFormUrl = $previewFormUrl;
        return $this;
    }

    public function getPhotoIndex(string $previewIndexUrl)
    {
        $this->previewIndexUrl = $previewIndexUrl;
        return $this;
    }

    public function getCropBoxData(string $cropData = null)
    {
        $this->cropData = $cropData;
        return $this;
    }


    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'downloadable' => $this->downloadable && !empty($this->value),
            'deletable' => $this->deletable,
            'useCropper' => $this->useCropper,
            'previewUrl' => $this->previewUrl,
            'previewDetailUrl' => $this->previewDetailUrl,
            'previewFormUrl' => $this->previewFormUrl,
            'previewIndexUrl' => $this->previewIndexUrl,
            'cropData' => $this->cropBoxDataAttribute
        ]);
    }
}
