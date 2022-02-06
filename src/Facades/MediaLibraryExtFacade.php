<?php

namespace Avxman\MediaLibraryExt\Facades;

use Avxman\MediaLibraryExt\Providers\MediaLibraryExtServiceProvider;
use Illuminate\Support\Facades\Facade;
use Avxman\MediaLibraryExt\Classes\MediaLibraryExtClass;

/**
 * Фасад дополнение библиотеки media-library
 *
 * @method static MediaLibraryExtClass reset()
 * @method static MediaLibraryExtClass init(\Illuminate\Database\Eloquent\Model $model)
 * @method static string toPicture()
 * @method static string toResponsive()
 * @method static MediaLibraryExtClass setAltName(string $altName)
 * @method static MediaLibraryExtClass setCollection(string $name)
 * @method static MediaLibraryExtClass setDefaultImg(string $imgUrl)
 * @method static MediaLibraryExtClass setConversion(array $conversion)
 * @method static MediaLibraryExtClass setViewImg(string $nameImg)
 * @method static MediaLibraryExtClass setViewSource(string $nameSource)
 * @method static MediaLibraryExtClass setWidth(string $width)
 * @method static MediaLibraryExtClass setHeight(string $height)
 * @method static MediaLibraryExtClass setSize(string ...$arg)
 * @method static MediaLibraryExtClass setLazy(bool $isLazy)
 *
 * @see MedialibraryExtClass
 */
class MediaLibraryExtFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MediaLibraryExtServiceProvider::class;
    }

}
