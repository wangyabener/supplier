<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SupplierSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="supplier-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);
    ?>

    <div class="row">
        <div class="col">
            <?= $form->field($model, 'id', [
                'template' => '
                <label class="control-label">ID</label>
                <div class="input-group">
                <button class="btn dropdown-toggle btn-outline-secondary" type="button" data-toggle="dropdown" aria-expanded="false">Select</button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"> > </a></li>
                    <li><a class="dropdown-item" href="#"> < </a></li>
                    <li><a class="dropdown-item" href="#"> >= </a></li>
                    <li><a class="dropdown-item" href="#"> <= </a></li>
                </ul>
                {input}
                </div>'
            ])->label('ID') ?>
        </div>
        <div class="col">
            <?= $form->field($model, 'name') ?>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <?= $form->field($model, 't_status')->label('Status')->dropDownList([
                'ok' => 'ok', 'hold' => 'hold'
            ],  ['prompt' => 'All status', 'class' => 'form-control']) ?>
        </div>
        <div class="col">
            <?= $form->field($model, 'code') ?>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
            </div>
        </div>
    </div>

    <?php
    ActiveForm::end();
    ?>

</div>