<?php

namespace symphony\extensions\static_asset_versioning;

class FoldersIterator extends \AppendIterator
{
	public function append(FileFilterIterator $iterator)
	{
		parent::append($iterator);
	}
}