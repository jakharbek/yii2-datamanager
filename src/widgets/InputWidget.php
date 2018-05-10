<?php
namespace jakharbek\datamanager\widgets;

use Yii;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use \kartik\select2\Select2;
use yii\web\View;
use \kartik\sortinput\SortableInput;

/**
 * Class InputWidget
 * @package jakharbek\datamanager\widgets
 */
class InputWidget extends Widget{
    /**
     * @var Active Record
     */
    public $model_db;
    /**
     * @var input name имя формы инпута
     */
    public $name;
    /**
     * @var Имя связи для получение данных через GET
     */
    public $attribute;

    /**
     * @var Имя атрибута поля первичного ключа
     */
    public $attribute_id = "post_id";
    /**
     * @var Имя атрибута поля текста
     */
    public $attribute_text = "title";

    /**
     * @var string имя связи второй таблице для получение данных
     */
    public $relation_name = "persons";

    /**
     * @var
     */
    public $options = ['placeholder' => 'Search for:','class' => 'data-javhar'];

    /**
     * @var string Шаблон для вывода резуьтата поиска
     * @input element
     */
    public $template = null;
    /**
     * @var string
     */
    public $searching_placeholder = "Waiting for results...";

    /**
     * @var string урл откуда будут браться данные для поиска
     */
    public $url = "/posts/posts/getdata";


    /**
     * @var private properties for result founded elements data
     */
    private $_data;
    /**
     * @var
     */
    private $_selected_id;
    /**
     * @var
     */
    private $_selected_text;


    /**
     *  init data
     */
    public function init(){
        parent::init();
        if($this->template == null)
        {
            $this->template = <<<JS

    var markup = "";
    
    if (element.text) {
      markup += '<h5>' + element.text + '</h5>';
    }
    
    return '<div style="overflow:hidden;">' + markup + '</div>';

JS;




            $data = <<<CSS
.select2-container--krajee .select2-selection--multiple .select2-selection__choice{
    width: 98%;
    text-overflow: ellipsis;
    overflow-x: hidden;
    height: 45px;
    padding:10px;
}
CSS;

            Yii::$app->view->registerCSS($data);

        }

        $data = $this->model_db->{$this->relation_name};

        if(count($data)){
            foreach ($data as $element){
                $this->_selected_id[] = $element->{$this->attribute_id};
                $this->_selected_text[] = $element->{$this->attribute_text};
            }
        }
    }

    /**
     * Widget core method for run its method will be run when you use widget
     */
    public function run(){

        $url = yii\helpers\Url::to([$this->url]);
        $formatJs = <<<JS
var format{$this->attribute} = function (element) { 
        {$this->template}       
}
var format{$this->attribute}Selection = function (element) {
    return element.text;
}

JS;

// Register the formatting script
        Yii::$app->view->registerJs($formatJs, View::POS_HEAD);

JS;
        echo Select2::widget([
            'name' => $this->name,
            'value' => $this->_selected_id,
            'initValueText' => $this->_selected_text,
            'options' => $this->options,
            'maintainOrder' => true,
            'showToggleAll' => true,
            'pluginOptions' => [
                'minimumInputLength' => 3,
                'multiple' => 'multiple',
                'language' => [
                    'errorLoading' => new JsExpression("function () { return '".$this->searching_placeholder."';}"),
                ],
                'ajax' => [
                    'url' => $url,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                ],

                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('format'.$this->attribute),
                'templateSelection' => new JsExpression('format'.$this->attribute.'Selection'),
            ],
        ]);

    }

}