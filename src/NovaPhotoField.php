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

    public function getCropBoxData(string $cropData = null)
    {
        return $this->withMeta(compact('cropData'));
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
            'useCropper' => $this->useCropper
        ]);
    }
}
