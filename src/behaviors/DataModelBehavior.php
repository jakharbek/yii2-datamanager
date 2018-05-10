<?php
namespace jakharbek\datamanager\behaviors;

/**
 *
 * @author Jakhar <javhar_work@mail.ru>
 *
 */

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;


class DataModelBehavior extends AttributeBehavior
{
    /**
     * @var string имя атрибута откуда брать информацию из формы
     */
    public $attribute = "imagesdata";
    /**
     * @var string имя свзяи
     */
    public $relation_name = "images";
    /**
     * @var ActiveRecord model связи
     */
    public $relation_model;



    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'saveData',
            ActiveRecord::EVENT_BEFORE_UPDATE  => 'saveData'
        ];
    }

    public function saveData(){
        if(!$this->owner->isNewRecord):
            $this->unlinkData();
        endif;
        $this->linkData();
    }

    private function unlinkData(){
        $relation_data = $this->owner->{$this->relation_name};
        if(count($relation_data) == 0){return false;}
        foreach ($relation_data as $data):
            $this->owner->unlink($this->relation_name,$data,true);
        endforeach;
    }

    private function linkData(){

        $data = $this->owner->{$this->attribute};

        if(!is_array($data)){return false;}

        if(!count($data)){return false;}

        $model = $this->relation_model;

        $elements = $model::find()->where(['in', $model::primaryKey()[0], $data])->all();

        if($elements):
            foreach ($elements as $element)
            {
                $this->owner->link($this->relation_name,$element);
            }
        endif;
    }
}