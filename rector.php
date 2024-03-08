<?php declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/src/Sources',
		__DIR__ . '/src/Themes',
	])
	->withSkip([
		__DIR__ . '**/Libs/*',
	])
	->withParallel(360)
	->withIndent(indentChar: "\t")
	->withImportNames(importShortClasses: false)
	->withPreparedSets(deadCode: true)
	->withPhpSets();
