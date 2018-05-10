<?php
namespace jakharbek\datamanager\actions;

use jakharbek\langs\components\Lang;
use Yii;
use yii\db\Query;

class Action extends \yii\base\Action{

    /**
     * @var string имя таблица по который нужно произвести поиск
     */
    public $table = "posts";
    /**
     * @var string имя первичного ключа
     */
    public $primaryColumn = "post_id";
    /**
     * @var string имя поля по которому нужно искать
     */
    public $textColumn = "title";
    /**
     * @var string другие поля который нужно вернуть через пробел как и в SQL
     */
    public $otherColumns = '';

    /**
     * @var string
     */
    public $langColumn = "lang";


    public function run($q = "",$id = ""){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $table = Yii::$app->db->schema->getTableSchema($this->table);
        if (!is_null($q)) {
            $out = ['results' => ['id' => '', 'text' => '']];
            $query = new Query;
            $query->select($this->primaryColumn . ' as id, ' . $this->textColumn . ' AS text' . $this->otherColumns)
                ->from($this->table)
                ->where(['ilike', $this->textColumn, $q]);
                if(isset($table->columns[$this->langColumn])) {
                    $query->andWhere(['=','lang',Lang::getLangId()]);
                }
                $query->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
            return $out;
        }
        /*else{
            $out['results'] = ['id' => $id, 'text' => 'javharbek'];
        }*/
    }

}