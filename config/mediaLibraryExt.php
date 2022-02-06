<?php

return [

    // Вкл./Откл. работу библиотеки
    'enabled' => env('MEDIA-LIBRARY-EXT-ENABLED', true),

    // Изображение по умолчанию для атрибутов <img src=""> и <source srcset="">
    'default_img' => env('MEDIA-LIBRARY-EXT-DEFAULT-IMG', '/img/default_image.jpg'),

    // Имя коллекции с css медией в виде ключа и значение
    'conversion' => env('MEDIA-LIBRARY-EXT-CONVERSION', ['488'=>'(max-width:488px)','668'=>'(min-width:489px)']),

    // Шаблоны
    'views' => env('MEDIA-LIBRARY-EXT-VIEWS', [
        // Шаблон для <img>
        'img'=>'vendor.media-library-ext._images._img',
        // Шаблон для <source>
        'source'=>'vendor.media-library-ext._images._source'
    ]),

    // Имя коллекции из media
    'collection' => env('MEDIA-LIBRARY-EXT-COLLECTION', 'images'),

    // По умолчанию размеры изображение в атрибут тега <img width="" height="">
    'size' => env('MEDIA-LIBRARY-EXT-SIZE', ['width'=>'625', 'height'=>'442']),

    // Подключаем ленивую загрузку
    'isLazy' => env('MEDIA-LIBRARY-EXT-IS-LAZY', true),

];
