<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class SeriesCreateInputDTO
{
    public function __construct(

        #[Assert\NotNull]
        #[Assert\NotBlank]
        #[Assert\Length(min: 3)]
        public string $seriesName = '',
        #[Assert\Positive]
        public int $seasonsQuantity = 0,
        #[Assert\Positive]
        public int $episodePerSeason = 0,
        public ?string $coverImage = null
    ) {
    }
}