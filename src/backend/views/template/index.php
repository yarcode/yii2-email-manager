<?php

use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->title = 'Email Templates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-template-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Email Template', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'shortcut',
            'language',
            'from',
            'subject',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{test} {update} {delete}',
                'buttons' => [
                    'test' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-upload"></span>',
                            ['test', 'shortcut' => $model->shortcut, 'language' => $model->language]);
                    }
                ]
            ],
        ],
    ]); ?>

</div>
