<?php declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Set\ValueObject\SetList;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(PhpVersion::PHP_80);

    $rectorConfig->paths([
        __DIR__ . '/src/Sources',
        __DIR__ . '/src/Themes',
    ]);

    $rectorConfig->skip([
        __DIR__ . '**/Libs/*',
        JsonThrowOnErrorRector::class,
        StringClassNameToClassConstantRector::class,
    ]);

    $rectorConfig->parallel(360);
    $rectorConfig->indent("\t", 4);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    $rectorConfig->sets([
        SetList::DEAD_CODE,
        LevelSetList::UP_TO_PHP_80
    ]);
};
