<?php

namespace symphony\extensions\static_asset_versioning;

require_once('iterators/file_filter_iterator.php');
require_once('iterators/folders_iterator.php');

class FolderIteratorFactory
{
	protected $iterator;

	public function __construct(\Iterator $iterator = null)
	{
		$this->iterator = $iterator;
	}

	public function build(array $folders, array $extensions)
	{
		$iterator_iterator = new FoldersIterator();
		foreach ($folders as $folder) {
			$folder_iterator = new \DirectoryIterator($folder);
			$file_iterator = new FileFilterIterator($folder_iterator, $extensions);
			$iterator_iterator->append($file_iterator);
		}
		$this->iterator = $iterator_iterator;
		return $this->iterator;
	}

	public function getIterator()
	{
		if (is_null($this->iterator)) throw new \Exception('No iterator set, please provide one or call the build() function.');
		return $this->iterator;
	}
}