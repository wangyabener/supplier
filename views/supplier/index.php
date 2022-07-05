<?php

use PHPUnit\Util\Log\JSON;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Suppliers';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="supplier-index">

    <?php

    echo $this->render('_search', ['model' => $searchModel]);
    ?>

    <div class="text-right">
        <?=
        Html::a('Export', ['export'], [
            'class' => 'btn btn-warning data-export',
            'data-toggle' => 'modal',
            'data-target' => '#export-modal',
        ]);
        ?>
    </div>
    <p class="d-none select-tips-page">All 20 conversations on this page have been selected. <a class="select-all">Select all conversations that match this search</a></p>
    <p class="d-none select-tips-all">All conversations in this search have been selected. <a class="select-clear">clear selection</a></p>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'footerOptions' => ['colspan' => 1],
                'footer' => '<a class="btn btn-danger">Delete</a>'
            ],
            'id',
            'name',
            'code',
            'mobile',
            ['attribute' => 't_status', 'label' => 'Status'],
            'created_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
        'showFooter' => true,
    ]);
    ?>
    <?= Html::input('hidden', 'is_all', 0, ['name' => 'is-select-all']) ?>

</div>

<?= Html::beginForm(Url::toRoute('export'), 'post', ['class' => ['export-form']]) ?>

<?php

Modal::begin([
    'id' => 'export-modal',
    'title' => '<h4 class="modal-title">Please select the columns to export</h4>',
]);
?>

<?= Html::checkboxList('select-columns', array_keys($columns), $columns, ['class' => 'form-group from-check', 'itemOptions' => [
    'labelOptions' => ['class' => 'col'],
]]) ?>
<?= Html::button('Submit', ['class' => ['btn', 'btn-success', 'export-submit']]) ?>

<?= Html::endForm() ?>

<?php
$url = Url::toRoute('export');

$this->registerJs(
    <<<JS
        $('.export-submit').on('click', function () {
            var select = localStorage.getItem('select-check-all');
            var columns = [];
            var ids = [];
            $('input[name="select-columns[]"]:checkbox:checked').each(function () {
                columns.push($(this).val());
            });
            $('input[name="selection[]"]:checkbox:checked').each(function() { 
                ids.push($(this).val());
            });

            $('.export-submit').click(function() {
                $.fileDownload("$url", {
                    data: {
                        'id': ids,
                        'columns': columns,
                        'is_all': $('input[name="is-select-all"]').val()
                    },
                    dataType: 'json',
                    // httpMethod: 'post',
                    success: function (result) {
                        console.log(result);
                    }
                });
            });
            $('.close').click();
        });
    JS
);

Modal::end();
?>

<?php

$this->registerJsFile('/js/jquery.fileDownload.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJs(
    <<<JS
        var page = $('.pagination').find('li.active > a').text();
        $('.select-on-check-all').on('click', function () {
            if ($(this).prop('checked')) {
                localStorage.setItem('select-check-page', page);
                $('.select-tips-page').addClass('d-block');
            } else {
                localStorage.setItem('select-check-page', 0);
                $('.select-tips-page').removeClass('d-block');
            }
        });
        $('.select-all').on('click', function () {
            localStorage.setItem('select-check-all', 'on');
            localStorage.setItem('select-check-page', 0);
            $('input:checkbox').prop('checked', true);
            $('.select-tips-page').removeClass('d-block');
            $('.select-tips-all').addClass('d-block');
            $('.').append('<input type="hidden" name="is-select-all" value=1>');
        });
        $('.select-clear').on('click', function () {
            localStorage.setItem('select-check-all', 'off');
            $('input:checkbox').prop('checked', false);
            $('.select-tips-all').removeClass('d-block');
        });
        if (localStorage.getItem('select-check-all') == 'on') {
            $('input:checkbox').prop('checked', true);
            $('.select-tips-all').addClass('d-block');
        }
        if (localStorage.getItem('select-check-page') == page) {
            $('input:checkbox').prop('checked', true);
            $('.select-tips-page').addClass('d-block');
        }
JS
);
?>
