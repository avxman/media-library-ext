# Модуль дополнения до библиотеки spatie/laravel-medialibrary laravel >=8
#### Вывод изображения в шаблоне как picture

## Установка модуля с помощью composer
```dotenv
composer require avxman/media-library-ext
```

## Настройка модуля
После установки модуля не забываем запустить команду artisan
`php artisan vendor:publish --tag="avxman-media-library-ext-all"` - добавляем
конфигурационный файл и шаблоны в систему

### Команды artisan
- Выгружаем конфигурационный файл
```dotenv
php artisan vendor:publish --tag="avxman-media-library-ext-config"
```
- Выгружаем файлы шаблонов
```dotenv
php artisan vendor:publish --tag="avxman-media-library-ext-views"
```

## Методы
### Сброс настроек (очерёдность вызова метода - первичная)
- **`reset()`** - Сброс параметров для нового изображения
### Инициализация (очерёдность вызова метода - вторичная)
- **`init(\Illuminate\Database\Eloquent\Model $model)`** - Получаем список изображений Model
### Дополнительные (очерёдность вызова метода - вторичная)
- **`setAltName(string $altName)`** - Перезаписываем атрибут alt для тега img
- **`setCollection(string $name)`** - Перезаписываем имя коллекции в media
- **`setDefaultImg(string $imgUrl)`** - Перезаписываем ссылку дефолтного изображения
- **`setConversion(array $conversion)`** - Перезаписываем Имя коллекции с css media
- **`setViewImg(string $nameImg)`** - Перезаписываем шаблон для <img>
- **`setViewSource(string $nameSource)`** - Перезаписываем шаблон для <source>
- **`setWidth(string $width)`** - Перезаписываем ширину изображения для тега <img width="">
- **`setHeight(string $height)`** - Перезаписываем высоту изображения для тега <img height="">
- **`setSize(string ...$arg)`** - Перезаписываем размеры(ширину и высоту) изображения для тега <img width="" height="">
- **`setLazy(bool $isLazy)`** - Перезаписываем включена ли ленивая загрузку
### Вывод (очерёдность вызова метода - последняя)
- **`toPicture()`** - Получаем все изображения в виде html кода с тегом <picture>
- **`toResponsive()`** - Получаем все изображения в виде html кода без тега <picture>

### Примеры получения результатов
```injectablephp

use Avxman\MediaLibraryExt\Facades\MediaLibraryExtFacade;

// Сброс настроек изображения перед инициализацией
MediaLibraryExtFacade::reset();

// Инициализация изображений
MediaLibraryExtFacade::init(\App\Models\User::find(1));

// Перезаписать параметров (очерёдность - вторичная)
MediaLibraryExtFacade::setAltName('Новое имя alt в изображении');
MediaLibraryExtFacade::setDefaultImg('/uploads/default/no-image.jpg');
MediaLibraryExtFacade::setWidth('50%');
// или можно указать всё в один вызов
// (все методы из вторичной очередности можно перечислить в один вызов)
MediaLibraryExtFacade::setAltName('Новое имя alt в изображении')
                    ->setDefaultImg('/uploads/default/no-image.jpg')
                    ->setWidth('50%');

// после вызываем команды последней очереди
$view_images = MediaLibraryExtFacade::toPicture();
// или
$view_images = MediaLibraryExtFacade::toResponsive();


// Также весь вышеуказанный код можно перечислить в один вызов
// главное, соблюдать очередность методов
$view_images = MediaLibraryExtFacade::reset()
                    ->init(\App\Models\User::find(1))
                    ->setAltName('Новое имя alt в изображении')
                    ->setDefaultImg('/uploads/default/no-image.jpg')
                    ->setWidth('50%')
                    ->toPicture();

// Можно и так
$view_images = MediaLibraryExtFacade
                    // сброс настроек
                    ::reset()
                    // перезаписываем параметры
                    ->setAltName('Новое имя alt в изображении')
                    ->setDefaultImg('/uploads/default/no-image.jpg')
                    ->setWidth('50%')
                    // инициализируем изображения
                    ->init(\App\Models\User::find(1))
                    // получаем html код в виде тега <picture></picture>
                    ->toPicture();



```
Вызов во views
```injectablephp
{!! \Avxman\MediaLibraryExt\Facades\MediaLibraryExtFacade::reset() !!}
{!! \Avxman\MediaLibraryExt\Facades\MediaLibraryExtFacade::init(\App\Models\User::find(1)) !!}
{!! \Avxman\MediaLibraryExt\Facades\MediaLibraryExtFacade::setAltName('Новое имя alt в изображении') !!}
{!! \Avxman\MediaLibraryExt\Facades\MediaLibraryExtFacade::setWidth('50%') !!}
{!! \Avxman\MediaLibraryExt\Facades\MediaLibraryExtFacade::toPicture() !!}
```
или
```injectablephp
{!! \Avxman\MediaLibraryExt\Facades\MediaLibraryExtFacade
::reset()
->init(\App\Models\User::find(1))
->setAltName('Новое имя alt в изображении')
->setWidth('50%')
->toPicture() !!}
```
