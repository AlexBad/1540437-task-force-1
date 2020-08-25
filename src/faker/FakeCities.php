<?php

namespace app\faker;

class FakeCities extends AbstractFakeModel
{
    public $id;
    public $name;
    public $lat;
    public $long;

    public static function tableName(): string
    {
        return 'cities';
    }

    public function setCity(string $val)
    {
        $this->name = $val;
    }
}
