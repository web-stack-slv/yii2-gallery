<?php
namespace farawayslv\gallery;

use Yii;
use yii\base\Widget;
use yii\data\Pagination;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\helpers\Html;

class Gallery extends Widget
{
    public $title = '';
    public $description = '';
    public $source;
    public $isDeep = true;
    public $files = [];

    public function init()
    {
        parent::init();
        
        \farawayslv\gallery\src\GalleryAsset::register($this->view);
        
        $this->files = $this->parseDir($this->source);
    }

    public function run()
    {  
        $this->renderGallery();
    }

    private function renderGallery()
    {
        $pageParam = $this->createPageParams();

        $pages = new Pagination(['totalCount' => count($this->files), 'pageSize'=>24, 'pageParam' => $pageParam]);
        
        $files = array_slice($this->files, $pages->offset, $pages->limit);
        
        $images = [];
        foreach ($files as $file) 
        {
            $images[] = Html::tag(
                'div', 
                Html::a(
                    Html::img($file, ['class' => 'img-fluid']), $file, ['data-lightbox' => 'images']
                ), 
                    ['class' => 'col-sm-6 col-md-3 col-lg-2 item']
                );
        }
        
       Pjax::begin(['timeout' => 5000, 'enablePushState' => false, 'class' => 'gallery-container']);

       echo Html::tag('div', 
                Html::tag('div', implode("\n", [
                    Html::tag('div', implode("\n", [
                        Html::tag('h2', Html::encode($this->title), ['class' => 'text-center']),
                        Html::tag('p', Html::encode($this->description), ['class' => 'text-center']),
                    ]),
                    ['class' => 'intro']),
                    Html::tag('div', implode("\n", array_filter($images)), ['class' => 'row images']),
                    Html::tag('div', 
                        Html::tag('div', 
                            LinkPager::widget([
                                'pagination' => $pages
                            ]), 
                            ['class' => 'container d-flex justify-content-center']
                        ),
                        ['class' => 'row']
                    ),
                    ]),
                    ['class' => 'container']
                ),
                ['class' => 'image-gallery']
            );
        
        Pjax::end(); 
    }

    private function parseDir($dir, &$results = [])
    {
        $rootPath = Yii::getAlias('@webroot');
        
        if(file_exists($rootPath.$dir)) 
        {
            $files = scandir($rootPath.$dir);

            foreach($files as $key => $value)
            {
                $path = $dir.DIRECTORY_SEPARATOR.$value;
                if(!in_array($value, ['.', '..'])) 
                {
                    if(is_dir($rootPath.$path)) 
                    {
                        if($this->isDeep) 
                        {
                            $this->parseDir($path, $results);
                        }
                    } 
                    else 
                    {
                        $fileData = pathinfo($path);
                        if(in_array($fileData['extension'], ['png', 'jpg'])) 
                        {
                            $results[] = Yii::$app->request->baseUrl.$path;
                        }
                    }
                }
            }
        }
    
        return $results;
    }

    private function createPageParams()
    {
        $tmp = mb_strtolower($this->source);

        $tmp = explode('/', trim($this->source, '/'));
        
        $count = count($tmp);

        if($count > 2) 
        {
            $tmp = array_slice($tmp, $count-2, 2);
        }

        return implode('-', $tmp);
    }
}
