<?php

namespace Espo\Core\Formula;

use \Espo\Core\Exceptions\Error;

class Evaluator
{
    private $functionFactory;

    private $formula;

    private $parser;

    private $attributeFetcher;

    private $parsedHash;

    public function __construct($container = null, array $functionClassNameMap = [], array $parsedHash = [])
    {
        $this->attributeFetcher = new AttributeFetcher();
        $this->functionFactory = new FunctionFactory($container, $this->attributeFetcher, $functionClassNameMap);
        $this->formula = new Formula($this->functionFactory);
        $this->parser = new Parser();
        $this->parsedHash = [];
    }

    public function process($expression, $entity = null, $variables = null)
    {
        if (!array_key_exists($expression, $this->parsedHash)) {
            $item = $this->parser->parse($expression);
            $this->parsedHash[$expression] = $item;
        } else {
            $item = $this->parsedHash[$expression];
        }

        if (!$item || !($item instanceof \StdClass)) {
            throw new Error();
        }

        $result = $this->formula->process($item, $entity, $variables);

        $this->attributeFetcher->resetRuntimeCache();

        return $result;
    }
}
