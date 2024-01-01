<?php

namespace App\MessageHandler;

use App\Message\SeriesWasRemoved;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveSerieImageHandler
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly Filesystem $filesystem
    )
    {
    }

    /**
     * @param SeriesWasRemoved $message
     * @return void
     */
    public function __invoke(SeriesWasRemoved $message): void
    {
        $coverImagePath = $message->series->getCoverImagePath();
        $path = $this->parameterBag->get('cover_image_director')
            . DIRECTORY_SEPARATOR
            . $coverImagePath;

        $this->filesystem->remove($path);

    }
}