<?php

namespace Bike\Partner\Db;

interface EntityInterface
{
    public function toArray();

    public function toArrayForInsert();

    public function toArrayForUpdate();

    public function fromArray(array $data);

    public static function getPrimaryKey();
}
