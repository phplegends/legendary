<?php

namespace PHPLegends\Legendary;

use PHPLegends\View\PreprocessorInterface;

class PreProcessor implements PreprocessorInterface
{
    /**
     *
     * @var string
     * */
    protected $inputFilename;

    /**
     *
     * @var string
     * */
    protected $outputFilename;

    /**
     *
     * @var string
     * */
    protected $storageDirectory;


    /**
     * 
     * @return void
     */
    public function __construct()
    {
        $this->storageDirectory = sys_get_temp_dir();
    }

    /**
     *
     * @param string $filename
     * @return self
     * */
    public function setInputFilename($filename)
    {
        if (!file_exists($filename)) {
            throw new \InvalidArgumentException("The file '{$filename}' doesnt exists!");
        }

        $this->inputFilename = $filename;

        return $this;
    }


    public function getOutputFilename()
    {
        return $this->outputFilename;
    }

    /**
     * Gets the value of storageDirectory.
     *
     * @return mixed
     */
    public function getStorageDirectory()
    {
        return $this->storageDirectory;
    }
    /**
     * Sets the value of storageDirectory.
     *
     * @param string $storageDirectory the storage directory
     *
     * @return self
     */
    public function setStorageDirectory($storageDirectory)
    {
        $this->storageDirectory = $storageDirectory;

        return $this;
    }
    
    public function run()
    {
        $template = new TemplateParser(file_get_contents($this->inputFilename));

        $this->outputFilename = $this->buildOutputFilename();

        file_put_contents($this->outputFilename, $template->parse());
    }
}
