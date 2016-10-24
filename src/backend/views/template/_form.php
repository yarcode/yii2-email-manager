<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yarcode\email\models\EmailTemplate $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="email-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'shortcut')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'language')->dropDownList(
        array_combine(\yarcode\email\EmailManager::getInstance()->languages,
            \yarcode\email\EmailManager::getInstance()->languages)
    ) ?>
    <?= $form->field($model, 'from')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'subject')->textInput(['maxlength' => 255]) ?>

    <?=
    $form->field($model, 'text', ['enableClientValidation' => false])
        ->widget('trntv\aceeditor\AceEditor', [
            'mode' => 'html',
        ])
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
