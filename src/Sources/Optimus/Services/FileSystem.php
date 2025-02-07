<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC3
 */

namespace Bugo\Optimus\Services;

final class FileSystem implements FileSystemInterface
{
	public function __construct(private readonly string $basePath) {}

	public function writeFile(string $filename, string $content): void
	{
		$path = $this->getFullPath($filename);

		$fp = fopen($path, 'w+b');
		if ($fp === false) {
			throw new FileSystemException("Cannot create file: $path");
		}

		try {
			if (! flock($fp, LOCK_EX)) {
				throw new FileSystemException("Cannot lock file: $path");
			}

			if (fwrite($fp, $content) === false) {
				throw new FileSystemException("Cannot write to file: $path");
			}

			fflush($fp);

			flock($fp, LOCK_UN);
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

		$gz = gzopen($path, 'wb9');
		if ($gz === false) {
			throw new FileSystemException("Cannot create gzip file: $path");
		}

		try {
			if (gzwrite($gz, $content) === false) {
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
