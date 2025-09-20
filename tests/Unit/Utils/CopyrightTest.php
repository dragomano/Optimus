<?php declare(strict_types=1);

use Bugo\Compat\Lang;
use Bugo\Optimus\Utils\Copyright;

dataset('language links', [
    ['ru', 'https://dragomano.ru/mods/optimus'],
    [null, 'https://custom.simplemachines.org/mods/index.php?mod=2659'],
]);

it('gets correct link for language', function ($lang, $expectedUrl) {
    if ($lang !== null) {
        Lang::setTxt('lang_dictionary', $lang);
    }

    $link = Copyright::getLink();

    expect($link)->toContain($expectedUrl);

    if ($lang !== null) {
        unset(Lang::$txt['lang_dictionary']);
    }
})->with('language links');

it('gets years', function () {
    $link = Copyright::getYears();

    expect($link)->toContain(' &copy; 2010&ndash;' . date('Y') . ', Bugo');
});

it('returns valid HTML link', function () {
    $link = Copyright::getLink();

    expect($link)->toContain('<a href="https://custom.simplemachines.org/mods/index.php?mod=2659"');
    expect($link)->toContain('target="_blank"');
    expect($link)->toContain('rel="noopener"');
    expect($link)->toContain('title="');
    expect($link)->toContain('</a>');
});

it('returns correct copyright years format', function () {
    $years = Copyright::getYears();

    expect($years)->toMatch('/ &copy; 2010&ndash;\d{4}, Bugo/');
});
