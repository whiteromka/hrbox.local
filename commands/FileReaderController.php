<?php

namespace app\commands;

use app\components\FileCreator;
use app\components\FileReader;
use yii\console\Controller;

class FileReaderController extends Controller
{
    /**
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        echo 'Process is runing. Plz wait A little...' . PHP_EOL;
        $time = time();
        $reader = new FileReader();
        $reader->readFiles();

        $data = [
            'oddRows' => $reader->getOddRows(),
            'evenRows' => $reader->getEvenRows()
        ];
        (new FileCreator())->createOutputFiles($data);

        echo 'Done! ' . (time() - $time) . ' sec ' . PHP_EOL;
        echo 'Memory usage ' . memory_get_usage() . ' Bytes';
    }

}