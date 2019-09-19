# nova-photo-field

```
MediaImage:make('Превью', 'preview')
    ->handleClass(SavePhoto::class)
    ->params([
        'folder'  => 'dresses-preview',
        'thumbs'  => config('thumbs.dresses-preview'),
        'cropper' => true,
        'ratio'   => 3 / 4,
    ])
    ->getPhoto('previewUrl')
    ->getPhotoDetail('previewUrl')
    ->getPhotoForm('previewUrl')
    ->getPhotoIndex('previewUrl'),
```
