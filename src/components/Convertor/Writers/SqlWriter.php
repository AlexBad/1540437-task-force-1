<?php
namespace app\components\Convertor\Writers;

use app\components\Convertor\Readers\AbstractFileReader;

class SqlWriter extends AbstractWriter
{
    /** @var string Путь до каталога */
    private $path;
    private $dbName = 'taskforce';
    protected $_batchInsertTemplate = 'INSERT INTO `{db}`.`{table}` ({columns}) VALUES{n}{rows};';

    /**
     * @param AbstractFileReader $reader
     * @return string
     */
    public function generateFileName(AbstractFileReader $reader): string
    {
        return "{$reader->getFileName(false)}.sql";
    }


    /** @return string Путь до коталога */
    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /** {@inheritDoc} */
    public function generate(AbstractFileReader $reader): string
    {
        foreach ($reader->getRows() as $row) {
            $rows[] = self::rows($row);
        }

        return strtr($this->_batchInsertTemplate, [
            '{db}' => $this->dbName,
            '{n}' => "\n",
            '{table}' => $reader->getFileName(false),
            '{columns}' => self::columns($reader->getColumns()),
            '{rows}' => implode(",\n", $rows)
        ]) . PHP_EOL;
    }

    /** {@inheritDoc} */
    public function saveAsFile(string $filename, string $data): int
    {
        $filename = "{$this->getPath()}/{$filename}";
        $file = new \SplFileObject($filename, "w+");
        $file->ftruncate(0);
        return $file->fwrite($data);
    }

    /**
     * Получение строки по заданному шаблону.
     *
     * Шаблон пол умолчанию `{t}({rows})`
     *
     * @param array $rows
     * @param string $template Шаблон для перобразования в строку
     * @return string
     */
    private static function rows(array $rows, $template = '{t}({rows})'): string
    {
        $data = [];
        foreach ($rows as $value) {
            $_data = is_numeric($value) ? $value : "'{$value}'";
            $_data = str_replace("\n", '\n', $_data);
            $data[] = $_data;
        }
        return strtr($template, ['{t}' => '  ', '{rows}' => implode(',', $data)]);
    }

    /**
    * Наименование Колонок
    *
    * @param array $attributes
    * @return string
    */
    private static function columns(array $columns): string
    {
        $data = [];
        foreach ($columns as $column) {
            $data[] = "`{$column}`";
        }
        return implode(',', $data);
    }
}