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

	it('writes empty content successfully', function () {
		$content = '';
		$filename = 'empty.txt';

		$this->fileSystem->writeFile($filename, $content);

		expect(file_exists($this->tempDir . '/' . $filename))->toBeTrue()
			->and(file_get_contents($this->tempDir . '/' . $filename))->toBe($content);
	});

	it('writes gzipped empty content successfully', function () {
		if (! function_exists('gzopen')) {
			$this->markTestSkipped('Gzip functions are not available');
		}

		$content = '';
		$filename = 'empty.txt.gz';

		$this->fileSystem->writeGzFile($filename, $content);

		expect(file_exists($this->tempDir . '/' . $filename))->toBeTrue();

		$decompressed = gzfile($this->tempDir . '/' . $filename);
		expect(implode('', $decompressed))->toBe($content);
	});

	it('writes large content successfully', function () {
		$content = str_repeat('a', 100000);
		$filename = 'large.txt';

		$this->fileSystem->writeFile($filename, $content);

		expect(file_exists($this->tempDir . '/' . $filename))->toBeTrue()
			->and(file_get_contents($this->tempDir . '/' . $filename))->toBe($content);
	});

	it('writes gzipped large content successfully', function () {
		if (! function_exists('gzopen')) {
			$this->markTestSkipped('Gzip functions are not available');
		}

		$content = str_repeat('a', 100000);
		$filename = 'large.txt.gz';

		$this->fileSystem->writeGzFile($filename, $content);

		expect(file_exists($this->tempDir . '/' . $filename))->toBeTrue();

		$decompressed = gzfile($this->tempDir . '/' . $filename);
		expect(implode('', $decompressed))->toBe($content);
	});

	it('writes file with special characters in filename', function () {
		$content = 'test';
		$filename = 'test file with spaces & symbols!.txt';

		$this->fileSystem->writeFile($filename, $content);

		expect(file_exists($this->tempDir . '/' . $filename))->toBeTrue()
			->and(file_get_contents($this->tempDir . '/' . $filename))->toBe($content);
	});

	it('throws exception when gzopen fails', function () {
		if (! function_exists('gzopen')) {
			$this->markTestSkipped('Gzip functions are not available');
		}

		$gzopenMock = function ($path, $mode) {
			return false; // Simulate gzopen failure
		};

		$fileSystem = new FileSystem($this->tempDir, gzopenFunc: $gzopenMock);

		expect(fn() => $fileSystem->writeGzFile('test.gz', 'content'))
			->toThrow(FileSystemException::class, 'Cannot create gzip file');
	});

	it('throws exception when gzwrite fails', function () {
		if (! function_exists('gzopen')) {
			$this->markTestSkipped('Gzip functions are not available');
		}

		$gzwriteMock = function ($gz, $content) {
			return false; // Simulate gzwrite failure
		};

		$fileSystem = new FileSystem($this->tempDir, gzwriteFunc: $gzwriteMock);

		expect(fn() => $fileSystem->writeGzFile('test.gz', 'content'))
			->toThrow(FileSystemException::class, 'Cannot write to gzip file');
	});

	it('throws exception when flock fails', function () {
		$flockMock = function ($fp, $operation) {
			return false; // Simulate flock failure
		};

		$fileSystem = new FileSystem($this->tempDir, flockFunc: $flockMock);

		expect(fn() => $fileSystem->writeFile('test.txt', 'content'))
			->toThrow(FileSystemException::class, 'Cannot lock file');
	});

	it('throws exception when fwrite fails', function () {
		$fwriteMock = function ($fp, $content) {
			return false; // Simulate fwrite failure
		};

		$fileSystem = new FileSystem($this->tempDir, fwriteFunc: $fwriteMock);

		expect(fn() => $fileSystem->writeFile('test.txt', 'content'))
			->toThrow(FileSystemException::class, 'Cannot write to file');
	});
});
