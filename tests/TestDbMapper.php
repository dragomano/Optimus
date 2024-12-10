<?php declare(strict_types=1);

namespace Tests;

abstract class TestDbMapper
{
	abstract public function testQuery($query, $params = []): array;

	public function query($id, $query, $params = []): array
	{
		expect($id)->toBeString()
			->and($query)->toBeString()
			->and($query)->not->toBeEmpty()
			->and($params)->toBeArray();

		return $this->testQuery($query, $params);
	}

	public function fetch_row($result): array|false|null
	{
		return $result ?? false;
	}

	public function fetch_assoc(&$result): array|false|null
	{
		return $result ? array_shift($result) : false;
	}

	public function fetch_all($result): array
	{
		return $result ?? [];
	}

	public function free_result($result): bool
	{
		return count($result) === 0;
	}

	public function insert(
		string $method,
		string $table,
		array $columns,
		array $data,
		array $keys,
		int $returnmode = 0
	): int|array|null
	{
		return $returnmode ? 1 : [];
	}

	public function get_version(): string
	{
		return '';
	}
}
