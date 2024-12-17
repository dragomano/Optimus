<?php declare(strict_types=1);

use Bugo\Optimus\Services\FileSystem;
use Bugo\Optimus\Services\FileSystemException;

beforeEach(function () {
	$this->tempDir = sys_get_temp_dir() . '/optimus_test_' . uniqid();
	mkdir($this->tempDir, 0777, true);

	$this->fileSystem = new FileSystem($this->tempDir);
});

afterEach(function () {
	if (is_dir($this->tempDir)) {
		array_map('unlink', glob($this->tempDir . '/*'));
		rmdir($this->tempDir);
	}
});

describe('FileSystem', function () {
	it('writes file successfully', function () {
		$content = 'test content';
		$filename = 'test.txt';

		$this->fileSystem->writeFile($filename, $content);

		expect(file_exists($this->tempDir . '/' . $filename))->toBeTrue()
			->and(file_get_contents($this->tempDir . '/' . $filename))->toBe($content);
	});

	it('writes gzipped file successfully', function () {
		if (! function_exists('gzopen')) {
			$this->markTestSkipped('Gzip functions are not available');
		}

		$content = 'test content';
		$filename = 'test.txt.gz';

		$this->fileSystem->writeGzFile($filename, $content);

		expect(file_exists($this->tempDir . '/' . $filename))->toBeTrue();

		$decompressed = gzfile($this->tempDir . '/' . $filename);
		expect(implode('', $decompressed))->toBe($content);
	});

	it('throws exception when writing to non-existent directory', function () {
		$nonExistentDir = $this->tempDir . '/nonexistent';
		$fileSystem = new FileSystem($nonExistentDir);

		set_error_handler(function() {});

		try {
			expect(fn() => $fileSystem->writeFile('test.txt', 'content'))
				->toThrow(FileSystemException::class);
		} finally {
			restore_error_handler();
		}
	});
});
