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
    // string, optional parameter
    public $title = ''; 

    // string, optional parameter
    public $description = ''; 

    // string, optional parameter. Subscribe where we have to get images
    public $source; 

    /* boolean, optional parameter. 
    * if  - true, we get images from all child folders, 
    * else  - only from root folder
    */
    public $isDeep = true;

    /*
     * array of string - images url, optional parameter.
     * we can define images directly, without source folder
     */
    public $files = [];

    // string, optional parameter
    public $emptyMessage = 'No available photos ...';

    // integer, optional parameter. Haw many photos we want to display on page
    public $pageSize;

    // string, optional parameter
    public $imageClass = 'img-fluid';

    // string, optional parameter
    public $imageWrapClass = '';

    // integer, optional parameter. Haw many photos we want to display on one row
    public $inRow;

    public function init()
    {
        parent::init();
        
        \farawayslv\gallery\GalleryAsset::register($this->view);
        
        if($this->source && $this->source != '') 
        {
            $this->files = $this->parseDir($this->source);
        }
        
        if(!is_int($this->pageSize) || $this->pageSize < 1) 
        {
            $this->pageSize = 24;
        }

        if(!$this->inRow || !is_int($this->inRow) || $this->inRow < 1)
        {
            $this->inRow = 6;
        }

        $col = ceil(12 / $this->inRow);
        
        $this->imageWrapClass .= ' col-sm-6 col-md-3 col-lg-'.$col;

    }

    public function run()
    {  
        $this->renderGallery();
    }

    private function renderGallery()
    {
        $pageParam = $this->createPageParams();

        $pages = new Pagination([
            'totalCount' => count($this->files), 
            'pageSize'=>$this->pageSize, 
            'pageParam' => $pageParam
            ]);
        
        $files = array_slice($this->files, $pages->offset, $pages->limit);
        
        $images = [];
        foreach ($files as $file) 
        {
            $images[] = Html::tag(
                'div', 
                Html::a(
                    Html::img($file, ['class' => $this->imageClass]), $file, ['data-lightbox' => 'images']
                ), 
                    ['class' => $this->imageWrapClass.' item']
                );
        }

        if(count($images) == 0) {
            $images[] = Html::tag('div', 
                Html::tag('h3', $this->emptyMessage, ['class' => 'message']), 
                ['class'=>'container d-flex justify-content-center']
            );
        }
        
       Pjax::begin(['timeout' => 5000, 'enablePushState' => false, 'class' => 'gallery-container']);

       echo Html::tag('div', //image-gallery
                Html::tag('div', implode("\n", [ // container
                    Html::tag('div', implode("\n", [ // title and description
                        Html::tag('h2', Html::encode($this->title), ['class' => 'text-center']),
                        Html::tag('p', Html::encode($this->description), ['class' => 'text-center']),
                    ]),
                    ['class' => 'intro']),
                    Html::tag('div', implode("\n", array_filter($images)), ['class' => 'row images']), // images                 
                    Html::tag('div',  // pagination
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

    /*
    * if isDeep = true recursive get all images from source folder
    */
    
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

    /*
    * if we have several galleries on one page need to create unique routes for each from folders name
    */
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
