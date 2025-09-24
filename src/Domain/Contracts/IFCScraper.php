<?php

namespace App\Domain\Contracts;

interface IFCScraper{
    public function scrapNews(): array;
}
