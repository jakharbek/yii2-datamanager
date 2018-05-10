Data manager
============
yii2 datamanager

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist jakharbek/yii2-datamanager "*"
```

or add

```
"jakharbek/yii2-datamanager": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Для начало успользованеи данного расширение вам нужно будет настроить
все связи перед темь как начать работу.

После в модель которую вы собираетесь использовать применити поведение:

```php
jakharbek\datamanager\behaviors\DataModelBehavior
```

Для примера мы будем использовать модель Постов (Posts) и модель Персон (Persons)
и сделаем связь между постами и персонами

пример:

```php
class Posts{

    private $_personsdata;
    
    public function behaviors()
    {
        return [
                    'data_persons_model' => [
                        'class' => \jakharbek\datamanager\behaviors\DataModelBehavior::className(),
                        'attribute' => 'personsdata',
                        'relation_name' => 'persons',
                        'relation_model' => new Persons(),
                    ]
                ];

   }
   
   public function getPersonsdata(){
        return $this->_personsdata;
   }
   public function setPersonsdata($value){
        return $this->_personsdata = $value;
   }
 }
```

Action
----
После того вам нужно подключть "действие" для того что расширение могла узнать где искать данные
```php
jakharbek\datamanager\actions\Action
```

свойство
```php
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
```

пример

```php
class Posts{

    public function actions(){
        return [
            'getdata' => [
                'class' => 'jakharbek\datamanager\actions\Action',
                'table' => 'posts',
                'primaryColumn' => 'post_id',
                'textColumn' => 'title'
            ],
        ];
    }
    
    
}
```

после того как вы настроили модель вам нужно вывести виджет

*   model_db - имя текушей модели
*   name  - имя инпута формы.
*   attribute - имя атрибута который присваемваться данные
*   attribute_id - имя поля первичного ключа в базе данных связанный таблице
*   attribute_text - имя поля по название в базе данных связанный таблице
```php
 echo jakharbek\datamanager\widgets\InputWidget::widget([
                    'model_db' => $model,
                    'name' => 'Posts[personsdata]',
                    'attribute' => 'personsdata',
                    'attribute_id' => 'person_id',
                    'attribute_text' => 'title',
                    'relation_name' => 'persons',
                    'url' => '/posts/posts/getdata/',
            ]);
```
