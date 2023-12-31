<?php

namespace App\Message;

use App\Entity\Series;

class SeriesWasRemoved
{
    public function __construct(
        public readonly Series $series
    ) {
    }
}