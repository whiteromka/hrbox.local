<?php

namespace app\components;

use app\commands\RowsDesigner;
use Yii;

class FileReader
{
    /** @var string - Все нечетные строки со всех файлов  */
    private $oddRows = '';

    /** @var string - Все четные строки со всех файлов  */
    private $evenRows = '';

    /** @var int - Номер текущей строки */
    private $currentOddRowNumber = 1;

    /** @var string - Первые 100 нечетных строк */
    private $firstHundredOddRows = '';

    /** @var string - Ключ кэша */
    public const HUNDRED_ODD_ROWS_CACHE_NAME = 'HUNDRED_ODD_ROWS_CACHE_NAME';

    /** @var int - Время жизни кэша */
    public const HUNDRED_ODD_ROWS_CACHE_DURATION = 60000;

    /** @var int - Кол-во нечетных строк которые нужны для вывода в браузер */
    private const NEEDED_COUNT_ODD_ROWS = 100;

    /**
     * @return string
     */
    public function getOddRows(): string
    {
        return $this->oddRows;
    }

    /**
     * @return string
     */
    public function getEvenRows(): string
    {
        return $this->evenRows;
    }

    /**
     * @return string
     */
    public function getFirstHundredOddRows(): string
    {
        return $this->firstHundredOddRows;
    }

    /**
     * Читает и парсит входящие(input) файлы
     *
     * @return $this
     */
    public function readFiles(): self
    {
        $files = $this->getFiles();
        foreach ($files as $number => $file) {
            $this->read($number, $file);
        }
        return $this;
    }

    /**
     * Читает файл заполняет четные и нечетные строки
     *
     * @param int $fileNumber
     * @param string $file
     */
    private function read(int $fileNumber, string $file)
    {
        $content = file_get_contents($file);
        $content = explode(PHP_EOL, $content);

        foreach ($content as $numberString => $row) {
            $numberString = $numberString + 1;
            $futureNumberString = $this->getFutureNumberString($numberString, $fileNumber);
            $replacedRow = str_replace(self::getVowelsSymbols(), $futureNumberString, $row);

            if ($futureNumberString && $replacedRow) {
                $this->fillRows($futureNumberString, $replacedRow);
            }
        }
    }

    /**
     * Вернет номер строки будто бы все файлы были склеены в один большой
     *
     * @param int $numberString
     * @param int $fileNumber
     * @return int
     */
    private function getFutureNumberString(int $numberString, int $fileNumber): int
    {
        if ($fileNumber === 1) {
            return $numberString;
        }
        $fileNumber = $fileNumber - 1;
        return (FileCreator::COUNT_ROWS * $fileNumber) + $numberString;
    }

    /**
     * Заполнит oddRows, evenRows и firstHundredOddRows
     *
     * @param int $numberString
     * @param string $replacedRow
     */
    private function fillRows(int $numberString, string $replacedRow)
    {
        if ($this->isEven($numberString)) {
            $this->evenRows .= $replacedRow . PHP_EOL;
        } else {
            $this->oddRows .= $replacedRow . PHP_EOL;
            if ($this->currentOddRowNumber == self::NEEDED_COUNT_ODD_ROWS) {
                $this->firstHundredOddRows = $this->oddRows;
                $rows = (new RowsDesigner($this->firstHundredOddRows))->getRowsWithBoltNumbers();
                Yii::$app->cache->add(self::HUNDRED_ODD_ROWS_CACHE_NAME, $rows, self::HUNDRED_ODD_ROWS_CACHE_DURATION);
            }
            $this->currentOddRowNumber++;
        }
    }

    /**
     * Четная строка
     *
     * @param $numberString
     * @return bool
     */
    private function isEven($numberString): bool
    {
        return ($numberString % 2) == 0;
    }

    /**
     * Получить файлы для чтения сортированные в правильном порядке
     *
     * @return array
     */
    private function getFiles()
    {
        $path = Yii::getAlias('@app/'. FileCreator::INPUT_DIR .'/');
        $dirtyFiles = scandir($path);
        $files = [];
        foreach ($dirtyFiles as $fileName) {
            $number = preg_replace('/[^0-9]/', '', $fileName);
            $file = $path . $fileName;
            if (is_file($file)) {
                $files[$number] = $file;
            }
        }
        ksort($files);
        return $files;
    }

    /**
     * @return string[]
     */
    public static function getVowelsSymbols(): array
    {
        return ['a', 'e', 'i', 'o', 'u', 'y', 'A', 'E', 'I', 'O', 'U', 'Y'];
    }
}