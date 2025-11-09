<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC5
 */

namespace Bugo\Optimus\Services;

final class FileSystem implements FileSystemInterface
{
	public function __construct(
		private readonly string $basePath,
		private readonly mixed $fopenFunc = 'fopen',
		private readonly mixed $gzopenFunc = 'gzopen',
		private readonly mixed $gzwriteFunc = 'gzwrite',
		private readonly mixed $flockFunc = 'flock',
		private readonly mixed $fwriteFunc = 'fwrite'
	) {}

	public function writeFile(string $filename, string $content): void
	{
		$path = $this->getFullPath($filename);

		$fp = ($this->fopenFunc)($path, 'w+b');
		if ($fp === false) {
			throw new FileSystemException("Cannot create file: $path");
		}

		try {
			if (! ($this->flockFunc)($fp, LOCK_EX)) {
				throw new FileSystemException("Cannot lock file: $path");
			}

			if (($this->fwriteFunc)($fp, $content) === false) {
				throw new FileSystemException("Cannot write to file: $path");
			}

			fflush($fp);

			($this->flockFunc)($fp, LOCK_UN);
		} finally {
			fclose($fp);
		}
	}

	public function writeGzFile(string $filename, string $content): void
	{
		if (! function_exists('gzopen')) {
			throw new FileSystemException('Gzip functions are not available');
		}

		$path = $this->getFullPath($filename);

		$gz = ($this->gzopenFunc)($path, 'wb9');
		if ($gz === false) {
			throw new FileSystemException("Cannot create gzip file: $path");
		}

		try {
			if (($this->gzwriteFunc)($gz, $content) === false) {
				throw new FileSystemException("Cannot write to gzip file: $path");
			}
		} finally {
			gzclose($gz);
		}
	}

	private function getFullPath(string $filename): string
	{
		return $this->basePath . DIRECTORY_SEPARATOR . $filename;
	}
}
