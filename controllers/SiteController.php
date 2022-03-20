<?php

namespace app\controllers;

use app\commands\RowsDesigner;
use app\components\FileReader;
use yii\web\Controller;
use Yii;

class SiteController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return \yii\web\Response
     */
    public function actionOddRows()
    {
        try {
            $rows = Yii::$app->cache->getOrSet(FileReader::HUNDRED_ODD_ROWS_CACHE_NAME, function() {
                $firstHundredOddRows = (new FileReader())->readFiles()->getFirstHundredOddRows();
                return (new RowsDesigner($firstHundredOddRows))->getRowsWithBoltNumbers();
            }, FileReader::HUNDRED_ODD_ROWS_CACHE_DURATION);
        } catch (\Exception $e) {
            $rows = false;
        }

        return $this->asJson([
            'success' => (bool) $rows,
            'html' => $rows,
            'error' => $rows ? '' : 'Не удалось получить данные'
        ]);
    }
}
