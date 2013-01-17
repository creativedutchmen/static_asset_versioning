<?php

namespace symphony\extensions\static_asset_versioning;

class FileFilterIterator extends \FilterIterator
{
    protected $extensions;

    public function __construct(\DirectoryIterator $iterator, array $extensions = array('js','css'))
    {
        $this->extensions = array_map('strtolower', $extensions);
        parent::__construct($iterator);
    }

    public function accept()
    {
        if ($this->getInnerIterator()->isFile()) {
            return in_array(strtolower($this->getInnerIterator()->getExtension()), $this->extensions);
        } else {
            return false;
        }
    }
}
