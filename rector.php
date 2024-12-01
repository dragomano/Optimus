<?php declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;

return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/src/Sources',
		__DIR__ . '/src/Themes',
	])
	->withSkip([
		__DIR__ . '**/Libs/*',
		NullToStrictStringFuncCallArgRector::class,
	])
	->withParallel(360)
	->withIndent(indentChar: "\t")
	->withImportNames(importShortClasses: false)
	->withPreparedSets(deadCode: true)
	->withPhpSets();
