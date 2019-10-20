# nova-photo-field

##installation

composer require vysotsky-productions/nova-photo-field
php artisan vendor:publish --tag=nova-photo-field

```
            NovaPhotoField::make('Превью', 'preview')
                ->aspectRatio(3/4)
                ->getPhoto('original_url')
                ->getPhotoForm('preview_url')
                ->getPhotoDetail('preview_url')
                ->getPhotoIndex('preview_url')
                ->getCropBoxData('crop_data')
                ->setDeletable(false)
                ->setHandler(
                    new SavePhoto('persons/avatar', config('thumbs.user.persons/avatar'))
                ),
```
####getPhoto(string $original_url)
$original_attribute - attribute name for original url of Media instance 
####getPhotoForm(string $preview_url)
$original_attribute - attribute name for cropped url of Media instance 
####getPhotoDetail(string $preview_url)
$original_attribute - attribute name for preview url of Media instance 
####getPhotoIndex(string $preview_url)
$original_attribute - attribute name for preview url of Media instance 
####getCropBoxData(string $crop_data)
$original_attribute - attribute name for crop_data of Media instance 
####setHandler($handlerClass)
$handlerClass should implement 3 methods:
    save :: $file, $cropData -> new Media  
    update :: $mediaId, $cropData -> void  
    delete :: $mediaId -> void 
####setDeletable(bool $deletable) 
by default - true (it means that file will be deleted with 
$handlerClass delete method)