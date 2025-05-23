<?php declare(strict_types=1);

use Bugo\Compat\Lang;
use Bugo\Optimus\Utils\Copyright;

it('gets link', function () {
	$link = Copyright::getLink();

	expect($link)->toContain('https://custom.simplemachines.org/mods/index.php?mod=2659');
});

it('gets link for Russian language', function () {
	Lang::setTxt('lang_dictionary', 'ru');

	$link = Copyright::getLink();

	expect($link)->toContain('https://dragomano.ru/mods/optimus');

	unset(Lang::$txt['lang_dictionary']);
});

it('gets years', function () {
	$link = Copyright::getYears();

	expect($link)->toContain(' &copy; 2010&ndash;' . date('Y') . ', Bugo');
});
