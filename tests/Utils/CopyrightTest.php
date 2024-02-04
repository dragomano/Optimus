<?php declare(strict_types=1);

use Bugo\Optimus\Utils\Copyright;

it('gets link', function () {
	$link = Copyright::getLink();

	expect($link)
		->toContain('https://custom.simplemachines.org/mods/index.php?mod=2659');
});

it('gets years', function () {
	$years = Copyright::getYears();

	expect($years)
		->toEqual(' &copy; 2010&ndash;' . date('Y') . ', Bugo');
});
