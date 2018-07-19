<?php

namespace PHPLegends\Legendary;

class TemplateParser
{

    const SPECIAL_ATTRIBUTE_PREFIX = 'php-';

    /**
     * @var \DomDocument
     */
    protected $domDocument;
    
    /**
     * @param string $content
     */

    public function __construct($content)
    {
        $dom = new \DomDocument();

        $dom->loadHtml($content);

        $this->domDocument = $dom;
    }
    
    /**
     * 
     * @return \DomDocument
     */
    public function getDomDocument()
	{	
		return $this->domDocument;
    }
    
    /**
     * Process special attributes for php transform code
     * 
     * @param \DomNamedNodeMap $attrs
     * @param \DomElement $node
     */
	protected function processAttributes(DOMNamedNodeMap $attrs, DomElement $node)
	{
		foreach($attrs as $attr) {

			if (strpos($attr->name, static::SPECIAL_ATTRIBUTE_PREFIX) !== 0) continue;
			// Processa as paradas do PHP aqui!
			
            $name = substr($attr->name, strlen(static::SPECIAL_ATTRIBUTE_PREFIX));
            
            $method = 'parseAttribute' . ucfirst($name);
            
			if (! method_exists($this, $method)) {
				throw new \RuntimeException("Method '{$name}' not yet implemented");
            }
            
            $node->removeAttribute($attr->name);
            
			$this->$method($node, $attr);
			
		}
    }
    
    /**
     * 
     * 
     */
	protected function recursiveNodeProcess(DomElement $nodes)
	{
		
		foreach ($nodes->childNodes as $node) {
			$node->hasChildNodes() && $this->recursiveNodeProcess($node);
			if ($node->nodeType === XML_ELEMENT_NODE){
				$this->processAttributes($node->attributes, $node);
			}
		}
    }
    

    /**
     * 
     */
	protected function parseAttributeBind(DomElement $node, DomAttr $attr)
	{
		$node->removeAttribute(static::SPECIAL_ATTRIBUTE_PREFIX . 'bind');
		$node->nodeValue = ''; // remove all childs!
		$expression = sprintf('<?= %s; ?>', $this->normalizePHPExpression($attr->value));
		$php = $this->getDomDocument()->createCDATASection($expression);
		$node->appendChild($php);
		
    }
    
	protected function parseAttributeForeach(DomElement $node, DomAttr $attr)
	{
        $dom = $this->getDomDocument();
        
		$expressionStart = sprintf('<?php foreach(%s): ?>', $this->normalizePHPExpression($attr->value));
		$expressionEnd = '<?php endforeach ?>';
	
		$node->insertBefore($dom->createCDATASection($expressionStart), $node->firstChild);
		$node->appendChild($dom->createCDATASection($expressionEnd));
		
    }
    
	protected function normalizePHPExpression($string)
	{
		return rtrim($string, ';');
    }


	public function parse()
	{
        $dom = $this->getDomDocument();
        
		$this->recursiveNodeProcess($dom->documentElement);
        
		return $dom->saveHtml();
		
	}
}