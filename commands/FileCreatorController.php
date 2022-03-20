<?php

namespace app\commands;

use app\components\FileCreator;
use yii\console\Controller;

class FileCreatorController extends Controller
{
    public function actionIndex()
    {
        echo 'Start creating input files. Plz wait 3-5 minutes' . PHP_EOL;
        echo '. - ' . FileCreator::REPORT_WHEN_COUNT_ROWS . ' rows added in file' . PHP_EOL;
        echo '+ - 1 file with ' . FileCreator::COUNT_ROWS . ' rows created ' . PHP_EOL;

        $fileCreator = new FileCreator();
        $fileCreator->createInputFiles();
        echo 'Done!' . PHP_EOL;
    }
}
