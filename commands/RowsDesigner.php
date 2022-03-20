<?php

namespace app\commands;

class RowsDesigner
{
    /** @var string  */
    private string $rows;

    public function __construct(string $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return string
     */
    public function getRowsWithBoltNumbers(): string
    {
        return preg_filter('#\d+#', '<b>$0</b>', $this->rows);
    }
}