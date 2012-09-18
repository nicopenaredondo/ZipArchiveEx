<?php

# Include requirements if not testing:
$wd = getcwd();
chdir(dirname(__FILE__));
require('../vendor/autoload.php');
chdir($wd);

# Start logging
LogMore::open('ZipArchiveEx');

/**
 * Class: ZipArchiveEx
 */
class ZipArchiveEx extends ZipArchive {

	/**
	 * Function: addDir
	 *
	 * Wrapper for the recursiveAddDir method.
	 *
	 * Parameters:
	 *
	 * 	$dirname - The directory to add.
	 *
	 * Returns:
	 *
	 * 	TRUE on success or FALSE on failure.
	 */
	public function addDir($dirname) {
		LogMore::debug('In addDir');
		return $this->recursiveAddDir($dirname);
	}

	/**
	 * Function: recursiveAddDir
	 *
	 * Recursively adds the passed directory and all files
	 * and folders beneath it.
	 *
	 * Parameters:
	 *
	 * 	$dirname - The directory to add.
	 * 	$basedir - The basedir where $dirname resides.
	 *
	 * Returns:
	 *
	 * 	TRUE on success or FALSE on failure.
	 */
	private function recursiveAddDir($dirname, $basedir=null) {
		LogMore::debug('In recursiveAddDir');
		$rc = false;

		# If $dirname is a directory
		if (is_dir($dirname)) {
			LogMore::debug('Is a directory: %s', $dirname);

			# Save current working directory
			$working_directory = getcwd();

			# Switch to passed directory
			chdir($dirname);

			# Get basename of passed directory
			$basename = $basedir . basename($dirname);

			# Add empty directory with the name of the passed directory
			LogMore::debug('Add empty dir %s', $basename);
			$rc = $this->addEmptyDir($basename);
			LogMore::debug('RC: %d', $rc);

			# Get all files in the directory
			$files = glob('*');
			LogMore::debug('Number of files in %s: %d',
				$basename,
				sizeof($files));

			# Loop through files
			foreach ($files as $f) {
				# If file is directory
				if (is_dir($f)) {
					# Call recursiveAdd
					LogMore::debug('Add dir %s', $f);
					$this->recursiveAddDir($f, $basename . '/');
				} else {
					LogMore::debug('Add file %s', $basename . '/' . $f);
					$rc = $this->addFile($f, $basename . '/' . $f);
					LogMore::debug('RC: %d', $rc);
				}
			}

			# Switch back to current working directory
			chdir($working_directory);

			$rc = true;
		}

		return $rc;
	}

};
