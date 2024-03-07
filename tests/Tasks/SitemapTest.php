<?php declare(strict_types=1);

use Bugo\Optimus\Tasks\Sitemap;

abstract class SMF_BackgroundTask
{
	protected array $_details;

	public function __construct(array $details)
	{
		$this->_details = $details;
	}
};

it('execute method', function () {
	expect(method_exists(Sitemap::class, 'execute'))->toBeTrue();
});
