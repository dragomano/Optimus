<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2026 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0
 */

namespace Bugo\Optimus\Services;

final readonly class FileSystem implements FileSystemInterface
{
	private const TEMP_SUFFIX = '.tmp';

	public function __construct(
		private string $basePath,
		private mixed $fopenFunc = 'fopen',
		private mixed $gzopenFunc = 'gzopen',
		private mixed $gzwriteFunc = 'gzwrite',
		private mixed $flockFunc = 'flock',
		private mixed $fwriteFunc = 'fwrite',
		private mixed $renameFunc = 'rename'
	) {}

	public function writeFile(string $filename, string $content): void
	{
		$path = $this->getFullPath($filename);
		$temp = $path . self::TEMP_SUFFIX;

		$fp = ($this->fopenFunc)($temp, 'w+b');
		if ($fp === false) {
			throw new FileSystemException("Cannot create file: $path");
		}

		$written = false;

		try {
			if (! ($this->flockFunc)($fp, LOCK_EX)) {
				throw new FileSystemException("Cannot lock file: $path");
			}

			if (($this->fwriteFunc)($fp, $content) === false) {
				throw new FileSystemException("Cannot write to file: $path");
			}

			fflush($fp);

			($this->flockFunc)($fp, LOCK_UN);

			$written = true;
		} finally {
			if (is_resource($fp)) {
				fclose($fp);
			}

			if (! $written) {
				$this->removeTemp($temp);
			}
		}

		$this->publish($temp, $path);
	}

	public function writeGzFile(string $filename, string $content): void
	{
		if (! function_exists('gzopen')) {
			throw new FileSystemException('Gzip functions are not available');
		}

		$path = $this->getFullPath($filename);
		$temp = $path . self::TEMP_SUFFIX;

		$gz = ($this->gzopenFunc)($temp, 'wb9');
		if ($gz === false) {
			throw new FileSystemException("Cannot create gzip file: $path");
		}

		$written = false;

		try {
			if (($this->gzwriteFunc)($gz, $content) === false) {
				throw new FileSystemException("Cannot write to gzip file: $path");
			}

			$written = true;
		} finally {
			if (is_resource($gz)) {
				gzclose($gz);
			}

			if (! $written) {
				$this->removeTemp($temp);
			}
		}

		$this->publish($temp, $path);
	}

	/**
	 * Swaps the finished temporary file in, so that readers never see a half-written sitemap
	 *
	 * @throws FileSystemException
	 */
	private function publish(string $temp, string $path): void
	{
		if (($this->renameFunc)($temp, $path)) {
			return;
		}

		$this->removeTemp($temp);

		throw new FileSystemException("Cannot replace file: $path");
	}

	private function removeTemp(string $temp): void
	{
		if (is_file($temp)) {
			@unlink($temp);
		}
	}

	private function getFullPath(string $filename): string
	{
		return $this->basePath . DIRECTORY_SEPARATOR . $filename;
	}
}
