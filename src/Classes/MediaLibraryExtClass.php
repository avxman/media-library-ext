<?php

namespace Avxman\MediaLibraryExt\Classes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MediaLibraryExtClass
{

    /**
     * Включение/Отключение работу библиотеки
     * @var bool $enabled = true
    */
    protected bool $enabled = true;

    /**
     * Настройки библиотеки
     * @var Collection $config
     */
    protected Collection $config;

    /**
     * Список изображений получены из модели
     *  @var Collection $images
     */
    protected Collection $images;

    /**
     * Обработанный список изображений в виде html кода
     *  @var Collection $result
     */
    protected Collection $result;

    /**
     * Шаблон для <img>
     *  @var string $nameViewImg = 'vendor.media-library-ext._images._img'
     */
    protected string $nameViewImg = 'vendor.media-library-ext._images._img';

    /**
     * Шаблон для <source>
     *  @var string $nameViewSource = 'vendor.media-library-ext._images._source'
     */
    protected string $nameViewSource = 'vendor.media-library-ext._images._source';

    /**
     * Имя коллекции media
     * @var string $collection = 'images'
     */
    protected string $collection = 'images';

    /**
     * Имя коллекции с css media в виде ключа и значение
     * @var array $conversion = ['488'=>'(max-width:488px)','668'=>'(min-width:489px)']
     */
    protected array $conversion = ['488'=>'(max-width:488px)','668'=>'(min-width:489px)'];

    /**
     * Текст в атрибут тег <img alt="">
     * @var string $altName = ''
     */
    protected string $altName = '';

    /**
     * Изображение по умолчанию для атрибутов <img src=""> и <source srcset="">
     * @var string $defaultSrc = '/frontend/img/main/no_photo.jpg'
     */
    protected string $defaultSrc = '/frontend/img/main/no_photo.jpg';

    /**
     * По умолчанию размеры изображение в атрибут тега <img width="" height="">
     * @var array $size = ['width'=>'625', 'height'=>'442']
     */
    protected array $size = ['width'=>'625', 'height'=>'442'];

    /**
     * Подключаем ленивую загрузку
     * @var bool $isLazy = true
     */
    protected bool $isLazy = true;

    /**
     * Приостанавливаем работу если найдена ошибка
     * @var bool $isError = false
    */
    protected bool $isError = false;

    /**
     * Сохранение ошибок
     * @var array $message = []
    */
    protected array $message = [];

    /**
     * Инициализация настроек библиотеки
     * @return void
    */
    protected function initConfig() : void{
        $this->enabled = $this->config->get('enabled');
        $this->defaultSrc = $this->config->get('default_img');
        $this->conversion = $this->config->get('conversion');
        $this->nameViewImg = $this->config->get('views')['img'];
        $this->nameViewSource = $this->config->get('views')['source'];
        $this->collection = $this->config->get('collection');
        $this->size = $this->config->get('size');
        $this->isLazy = $this->config->get('isLazy');
    }

    /**
     * Получаем список всех <source> тегов для тега <picture>
     * @param bool $isResponsive = true
     * @return void
     */
    protected function getSource(bool $isResponsive = true) : void{
        $viewSource = view($this->nameViewSource);
        if($this->images->count()) $this->images->each(function($image) use ($viewSource, $isResponsive){
            collect($this->conversion)->each(function ($width, $img) use ($image, $viewSource){
                if($image->hasGeneratedConversion($img)) {
                    $pathUrl = $image->getPath($img);
                    $type = File::mimeType($pathUrl);
                    $this->result->push($viewSource->with([
                        'src'=>asset($this->defaultSrc),
                        'srcset'=>$image->getUrl($img),
                        'media'=>$width,
                        'type'=>$type,
                        'is_responsive'=>false,
                        'lazy'=>$this->isLazy
                    ])->render());
                }
            });
            if($isResponsive) {
                $path = Str::beforeLast($image->getPath(),'/');
                $url = Str::beforeLast($image->getUrl(),'/');
                collect($image->responsive_images['medialibrary_original']['urls']??[])->each(function ($img) use ($path, $url, $viewSource){
                    $pathUrl = Str::start($img, Str::start('/responsive-images/', $path));
                    $fullUrl = Str::start($img, Str::start('/responsive-images/', $url));
                    $type = File::mimeType($pathUrl);
                    $size = Str::before(Str::after($img, '_original_'), '_');
                    $this->result->push($viewSource->with([
                        'src'=>asset($this->defaultSrc),
                        'srcset'=>$fullUrl,
                        'media'=>$this->conversion[$size]??'',
                        'type'=>$type,
                        'is_responsive'=>true,
                        'lazy'=>$this->isLazy
                    ])->render());
                });
            }
        });
    }

    /**
     * Получаем изображение в виде тега <img> для тега <picture>
     * @return void
     */
    protected function getImg() : void{
        $viewImg = view($this->nameViewImg);
        $image = $this->images->first();
        $this->result->push($viewImg->with([
            'src'=>asset($this->defaultSrc),
            'dataSrc'=>$image->getUrl(),
            'alt'=>$this->altName,
            'size'=>$this->size,
            'lazy'=>$this->isLazy
        ])->render());
    }

    /**
     * Получаем изображения тега <img> и <source>
     * @return void
     */
    protected function getAll() : void{
        $this->getSource();
        $this->getImg();
    }

    /**
     * Очищаем параметры к первоначальным
     * @return void
     */
    protected function getReset() : void{
        $this->images = collect();
        $this->result = collect();
        $this->initConfig();
    }

    /**
     * Конструктор
     * @return void
     */
    public function __construct()
    {
        if(!config()->has('medialibrary')) {
            $this->isError = true;
            $this->message = ['Не найдена или не подключена библиотека spatie/laravel-medialibrary'];
        }
        elseif (!config()->has('mediaLibraryExt')){
            $this->isError = true;
            $this->message = ['Не добавлены настройки текущей библиотеки'];
        }
        elseif (!config()->get('mediaLibraryExt.enabled')){
            $this->isError = true;
            $this->message = ['Работа библиотеки отключено'];
        }
        $this->config = collect(config()->get('mediaLibraryExt'));
        $this->getReset();
    }

    /**
     * Сброс параметров для нового изображения
     * @return self
     */
    public function reset() : self{
        $this->getReset();
        return $this;
    }

    /**
     * Получаем список изображений из модельки - вызывается предпоследним
     * @param Model $model
     * @return self
     */
    public function init(Model $model) : self{
        $this->images = $model->getMedia($this->collection);
        return $this;
    }

    /**
     * Получаем все изображения в виде html кода с тегом <picture> - вызывается последним
     * @return string
     */
    public function toPicture() : string {
        $this->getAll();
        $render = Str::start($this->result->join(''), '<picture>');
        return Str::finish($render, '</picture>');
    }

    /**
     * Получаем все изображения в виде html кода без тега <picture> - вызывается последним
     *  @return string
     */
    public function toResponsive() : string{
        $this->getAll();
        return $this->result->join('');
    }

    /**
     * Перезаписываем атрибут alt для тега img
     * @param string $altName
     * @return self
     */
    public function setAltName(string $altName) : self{
        $this->altName = $altName;
        return $this;
    }

    /**
     * Перезаписываем имя коллекции в media
     * @param string $name
     * @return self
     */
    public function setCollection(string $name) : self{
        $this->collection = $name;
        return $this;
    }

    /**
     * Перезаписываем ссылку дефолтного изображения
     * @param string $imgUrl
     * @return self
     */
    public function setDefaultImg(string $imgUrl) : self{
        $this->defaultSrc = $imgUrl;
        return $this;
    }

    /**
     * Перезаписываем Имя коллекции с css media
     * @param array $conversion
     * @return self
     */
    public function setConversion(array $conversion) : self{
        $this->conversion = $conversion;
        return $this;
    }

    /**
     * Перезаписываем шаблон для <img>
     * @param string $nameImg
     * @return self
     */
    public function setViewImg(string $nameImg) :self{
        $this->nameViewImg = $nameImg;
        return $this;
    }

    /**
     * Перезаписываем шаблон для <source>
     * @param string $nameSource
     * @return self
     */
    public function setViewSource(string $nameSource) : self{
        $this->nameViewSource = $nameSource;
        return $this;
    }

    /**
     * Перезаписываем ширину изображения для тега <img width="">
     * @param string $width
     * @return self
     */
    public function setWidth(string $width) : self{
        $this->size['width'] = $width;
        return $this;
    }

    /**
     * Перезаписываем высоту изображения для тега <img height="">
     * @param string $height
     * @return self
     */
    public function setHeight(string $height) : self{
        $this->size['height'] = $height;
        return $this;
    }

    /**
     * Перезаписываем размеры(ширину и высоту) изображения для тега <img width="" height="">
     * @param string ...$arg
     * @return self
     */
    public function setSize(string ...$arg) : self{
        if(count($arg) !== 2) return $this;
        $this->size = ['width'=>$arg[0], 'height'=>$arg[1]];
        return $this;
    }

    /**
     * Перезаписываем ленивую загрузку
     * @param bool $isLazy
     * @return self
     */
    public function setLazy(bool $isLazy) : self{
        $this->isLazy = $isLazy;
        return $this;
    }

}
