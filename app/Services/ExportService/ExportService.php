<?php

namespace App\Services\ExportService;

abstract class ExportService
{

    abstract public function prepareData(array $exportParams);
}
