<?php

require_once('lib/folder_iterator_factory.php');

use symphony\extensions\static_asset_versioning\FolderIteratorFactory as FolderIteratorFactory;

class extension_static_asset_versioning extends Extension
{
    public function getSubscribedDelegates()
    {
        return array(
            array(
                'page' => '/frontend/',
                'delegate' => 'FrontendOutputPreGenerate',
                'callback' => 'addVersionData'
            ),
        );
    }

    public function addVersionData($context)
    {
        $start = precision_timer();
        //var_dump($context['xml']);
        $factory = new FolderIteratorFactory;

        $watch_dirs = array(
            'js' => WORKSPACE . '/js',
            'css' => WORKSPACE . '/css',
        );

        $extensions = array(
            'css',
            'js'
        );

        $watch_dirs = array_map('realpath', $watch_dirs);
        $directories = $factory->build(
            $watch_dirs,
            $extensions
        );

        $data = array();

        foreach ($directories as $file) {
            $folder_name = array_search(dirname($file->getRealPath()), $watch_dirs);
            if (!is_string($folder_name)) {
                throw new Exception('Please set a name for each directory I need to track');
            }
            $data[$folder_name][] = array(
                $file->getBaseName()
                =>
                substr(
                    sha1_file(
                        $file->getRealPath()
                    ),
                    0,
                    9
                )
            );
        }
        //die();
        $assets = new XMLElement('assets');

        foreach ($data as $dir => $files) {
            $directory = new XMLElement($dir);
            foreach ($files as $file) {
                $file_element = new XMLElement(key($file), current($file));
                $directory->appendChild($file_element);
            }
            $assets->appendChild($directory);
        }
        $context['xml'] = str_replace('</params>', '</params>' . "\n" . $assets->generate(true, 1), $context['xml']);
        Symphony::Profiler()->seed($start);
        Symphony::Profiler()->sample('Asset Hashes Generated', PROFILE_LAP);
    }
}
