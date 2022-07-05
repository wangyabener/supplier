<?php

use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],
            'id',
            'name',
            'code',
            'mobile',
            ['attribute' => 't_status', 'label' => 'Status'],
            'created_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
        'showFooter' => false,
    ]);
    ?>
    <?= Html::input('hidden', 'is-select-all', 0) ?>

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
            var columns = [];
            $('input[name="select-columns[]"]:checkbox:checked').each(function () {
                columns.push($(this).val());
            });
            var ids = [];
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

        var cur_page = $('.pagination').find('li.active > a').text();

        var json = sessionStorage.getItem('select-page');
        if (json) {
            pages = JSON.parse(json);
            for (var j = 0; j < pages.length; j++) {
                if (pages[j] == cur_page) {
                    $('input[name="selection_all"]').prop('checked', true);
                    $('.select-tips-page').addClass('d-block');
                }
            }
        }

        $('input[name="selection_all"]').on('click', function () {
            if ($(this).prop('checked')) {
                $('.select-tips-page').addClass('d-block');
                checkPage(true, cur_page);
            } else {
                $('.select-tips-page').removeClass('d-block');
                checkPage(false, cur_page);
            }
        });
        var json = sessionStorage.getItem('select-checked');
        if (json) {
            items = JSON.parse(json);
            for(var i = 0; i < items.length; i++) {
                $('tr[data-key=' + items[i] + '] > td > input').prop('checked', true);
            }
            // $('.select-tips-page').addClass('d-block');
        }

        if (sessionStorage.getItem('select-all')) {
            $('input:checkbox').prop('checked', true);
            $('.select-tips-page').removeClass('d-block');
            $('.select-tips-all').addClass('d-block');
        }

        $('.select-all').on('click', function () {
            checkAll(true);
        });
        $('.select-clear').on('click', function () {
            checkAll(false);
        });

        function checkAll(type = true)
        {
            sessionStorage.setItem('select-all', type);
            $('input:checkbox').prop('checked', type);
            if (type) {
                $('.select-tips-page').removeClass('d-block');
                $('.select-tips-all').addClass('d-block');

                $('input[name="is-select-all"]').val(1);
            } else {
                $('.select-tips-page').removeClass('d-block');
                $('.select-tips-all').removeClass('d-block');
                sessionStorage.clear();
            }
        }

        function checkPage(type = true, page)
        {
            var ids = JSON.parse(sessionStorage.getItem('select-checked'));
            if (!ids) ids = [];
            $('input[name="selection[]"]').each(function(i) { 
                console.log($(this).val());
                if (type) {
                    ids.push($(this).val());
                } else {
                    var index = $.inArray($(this).val(), ids);
                    ids.splice(index, 1);
                }
            });
            console.log(ids);

            sessionStorage.setItem(
                'select-checked', JSON.stringify(ids)
            );

            var select = JSON.parse(sessionStorage.getItem('select-page'));
            if (!select) select = [];
            if (type) {
                select.push(page);
            } else {
                var index = $.inArray(page, select);
                select.splice(index, 1);
            }
            sessionStorage.setItem(
                'select-page', JSON.stringify(select)
            );
        }
JS
);
?>