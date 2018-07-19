<?php

namespace PHPLegends\Legendary;

use PHPLegends\Views\Factory as BaseFactory;

class Factory extends BaseFactory
{

    /**
     * @var PreProcessor
     */
    protected $preProcessor;
    
    public function __construct(Data $data = null)
    {
        $preProcessor = new PreProcessor();

        $finder = new Finder([
            'legendary.php' => $preProcessor,
            'lgd.php'       => $preProcessor,
        ]);

        $this->preProcessor = $preProcessor;

        parent::__construct($finder, $data);
    }


    public function getPreProcessor()
    {
        return $this->preprocessor;
    }
}
