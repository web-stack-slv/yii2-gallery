Image Gallery
=============
Image Gallery with custom folder as source

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist farawayslv/yii2-gallery "*"
```

or add

```
"farawayslv/yii2-gallery": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \farawayslv\gallery\Gallery::widget([
    'title' => (string) Gallery title. Optional,
    'description' => (string) Gallery description. Optional,
    'source' => (string) Path to images folder. (or set files directly),
    'files' => (Array[string]) Images urls (if not defined source parameter). Optional,
    'inRow' => (Integer) How many images in each row. optional,
    'isDeep' => (Bool) If "true" we get images not only source root directory, but and in all child           directories,
    'emptyMessage' => (String) If have no images default message. Optional,
    'pageSize' => (Integer) How many images will be show on one page. Optional,
    'imageClass' => (String) Custom class for images. Optional,
    'imageWrapClass' => (String) Custom class for images containers. Optional,
    'pagerOptions' => (Array) Standart LinkPager options (see Yii2 docs). Optional
]); ?>```
