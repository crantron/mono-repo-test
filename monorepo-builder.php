<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([__DIR__ . '/packages']);

//    $mbConfig->dataToAppend([
//        ComposerJsonSection::REPOSITORIES => [
//            'composer' => [
//                'type' => 'composer',
//                'url' => 'https://repo.packagist.com/crantron/'
//            ]
//        ]
//    ]);
};
