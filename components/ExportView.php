<?php

namespace app\Components;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

class ExportView extends GridView
{
    protected $filename = 'export';

    protected $extension = 'xlsx';

    protected $type = 'Xlsx';

    public $columns;

    public function init()
    {
        parent::init();
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setTitle('')->setCreated(date('Y-m-d H:i:s'));

        $cell = $spreadsheet->getActiveSheet();

        $provider = $this->dataProvider;

        $models = $provider->getModels();

        $columns = $this->columns;
        if (!$columns) {
            foreach (reset($models) as $name => $value) {
                $columns[] = (string) $name;
            }
        }

        // Header
        foreach ($columns as $index => $column) {
            $cell->setCellValue(self::columnTitle(++$index) . 1, $column);
        }

        $begin_row = 1;
        $end_row = 0;

        // Body
        foreach ($models as $index => $model) {
            $end_col = 0;
            foreach ($columns as $column) {
                $value = ArrayHelper::getValue($model, $column, '');
                $end_col++;
                $cell->setCellValue(
                    self::columnTitle($end_col) . ($end_row + $begin_row + 1),
                    $value
                );
            }
            $end_row++;
        }

        $this->setHttpHeaders();

        $writer = IOFactory::createWriter($spreadsheet, $this->type);
        $writer->save('php://output');
        exit();

        // return json_encode(
        //     [
        //         'filename' => $this->filename . '.' . $this->extension,
        //         'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        //     ]
        // );
    }

    protected static function columnTitle($index)
    {
        $i = $index - 1;
        if ($i >= 0 && $i < 26) {
            return chr(ord('A') + $i);
        }
        if ($i > 25) {
            return (self::columnTitle($i / 26)) . (self::columnTitle($i % 26 + 1));
        }
        return 'A';
    }

    public function setColumns($columns = [])
    {
        $this->columns = $columns;
        return $this;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename . '-' . date('YmdHi');
        return $this;
    }

    protected function setHttpHeaders()
    {
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        header("Content-Disposition: attachment; filename=\"{$this->filename}.{$this->extension}\"");
    }
}
