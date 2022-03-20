<?php

namespace app\components;

use Yii;
use Faker\Factory;
use yii\base\Exception;
use yii\console\Application;

class FileCreator
{
    /** Сколко сгенерировать исходных фалов */
    public const COUNT_FILES = 10;

    /** Сколко строк сделать в исходном файле */
    public const COUNT_ROWS = 100000;

    /** Репорт в конслось каждые REPORT_WHEN_COUNT_ROWS строк */
    public const REPORT_WHEN_COUNT_ROWS = 1000;

    /** Входные файлы тут */
    public const INPUT_DIR = 'web/input';

    /** Выходные файлы тут */
    public const OUTPUT_DIR = 'web/output';

    /** @var int - Счетчик строк в файле обнуляется каждые REPORT_WHEN_COUNT_ROWS строк */
    private $currentCountRows = 0;

    /** @var bool - Консольно приложение */
    private bool $isConsoleApp = false;

    /** @var Factory */
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->isConsoleApp = (Yii::$app instanceof Application);
    }

    /**
     * Создать и заполнить исходные(входные) файлы
     *
     * @throws Exception
     */
    public function createInputFiles(): void
    {
        $this->checkFolder();
        for ($numberFile = 1; $numberFile <= self::COUNT_FILES; $numberFile++) {
            $this->createInputFile($numberFile);
        }
    }

    /**
     * Создать и заполнить выходные файлы (output)
     *
     * @param array $data
     * @throws Exception
     */
    public function createOutputFiles(array $data)
    {
        $this->checkFolder();
        foreach ($data as $name => $rows) {
            $file = Yii::getAlias('@app/' . self::OUTPUT_DIR . '/' . $name . '.txt');
            file_put_contents($file, $rows);
        }
    }

    /**
     * Создаст входной файл
     *
     * @param int $numberFile
     * @throws Exception
     */
    private function createInputFile(int $numberFile): void
    {
        $file = Yii::getAlias('@app/' . self::INPUT_DIR . '/file_' . $numberFile . '.txt');
        if (!($resource = fopen($file, 'a'))) {
            throw new Exception('Can not open file ' . $file);
        }

        for ($numberRow = 1; $numberRow <= self::COUNT_ROWS; $numberRow++) {
            fwrite($resource, $this->faker->sentence . PHP_EOL);
            $this->consoleReportCountRows();
        }

        fclose($resource);
        $this->consoleReportFileCreated();
    }

    /**
     * Создаст папки в нужных дирректориях
     *
     * @throws Exception
     */
    private function checkFolder(): void
    {
        $paths = [self::INPUT_DIR, self::OUTPUT_DIR];
        foreach ($paths as $path) {
            if (!file_exists($path)) {
                if (!mkdir($path, 0777, true)) {
                    throw new Exception('Can not create folder' . $path);
                }
            }
        }
    }

    /**
     * Репорт в консоль 1000 строк добалены
     */
    private function consoleReportCountRows(): void
    {
        if ($this->isConsoleApp) {
            $this->currentCountRows++;
            if ($this->currentCountRows === self::REPORT_WHEN_COUNT_ROWS) {
                echo '.';
                $this->currentCountRows = 0;
            }
        }
    }

    /**
     * Репорт в консоль файл добален
     */
    private function consoleReportFileCreated(): void
    {
        if ($this->isConsoleApp) {
            echo '+' . PHP_EOL;
        }
    }
}