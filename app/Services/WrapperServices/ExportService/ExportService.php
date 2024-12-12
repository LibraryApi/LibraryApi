<?php

namespace App\Services\WrapperServices\ExportService;

abstract class ExportService
{

    abstract public function prepareData(array $exportParams);
}
