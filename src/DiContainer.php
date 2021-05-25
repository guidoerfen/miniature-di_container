<?php

namespace Miniature\DiContainer;

use Miniature\Component\Reader\YamlParserDecoratorInterface;
use Miniature\DiContainer\DiNode;
use Miniature\DiContainer\Syntax\MapperAbstract as DiSyntaxMapperAbstract;
use Miniature\DiContainer\Syntax\MapperNative as DiSyntaxMapperNative;

/**
 * Class DiContainer
 *
 * A simple implementation of an Dependeny Injection Container
 *
 * @package Miniature\DiContainer
 * @author Guido Erfen <sourcecode@erfen.de>
 * @see https://github.com/guidoerfen/miniature-di_container
 * @see https://github.com/guidoerfen/miniature-component#the-di-mapping
 */
class DiContainer
{
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   OBJECTS
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    private DiSyntaxMapperAbstract $syntaxMapper;

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   MAPPINGS
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    private array $diMappings        = [];
    private array $params            = [];
    private array $registredAsPublic = [];

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   FLAGS
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public static bool $overrideExistingKeys = true;

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   ARRAY-KEYS ON MAPPINGS (Syntax)
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    protected string $diMappingKey;
    protected string $parametersKey;
    protected string $classKey;
    protected string $argumentsKey;
    protected string $staticMethodKey;
    protected string $instanceKey;
    protected string $singletonFlagKey;
    protected string $publicKey;

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                                 INIT
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function __construct(?DiSyntaxMapperAbstract $diSyntaxMapper)
    {
        if (! $diSyntaxMapper instanceof DiSyntaxMapperAbstract) {
            $diSyntaxMapper = static::autoInject();
        }

        $this->syntaxMapper      = $diSyntaxMapper;
        $this->diMappingKey      = $diSyntaxMapper->getDiMappingKey();
        $this->parametersKey     = $diSyntaxMapper->getParametersKey();
        $this->classKey          = $diSyntaxMapper->getClassKey();
        $this->instanceKey       = $diSyntaxMapper->getInstanceKey();
        $this->argumentsKey      = $diSyntaxMapper->getArgumentsKey();
        $this->singletonFlagKey  = $diSyntaxMapper->getSingletonFlagKey();
        $this->staticMethodKey   = $diSyntaxMapper->getStaticMethodKey();
        $this->publicKey         = $diSyntaxMapper->getPublicKey();

        $this->diMappings['miniature.di_container'] = [
            $this->instanceKey       => $this,
            $this->singletonFlagKey => true,
            $this->classKey          => 'Miniature\DiContainer\DiContainer',
        ];
    }

    protected static function autoInject() : DiSyntaxMapperAbstract
    {
        return new DiSyntaxMapperNative();
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                                 READ
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function readMappings(array $mappings) : self
    {
        if (isset($mappings[$this->diMappingKey]) && is_array($mappings[$this->diMappingKey])) {
            $this->addToDiMappings($mappings[$this->diMappingKey]);
        }
        if (isset($mappings[$this->parametersKey]) && is_array($mappings[$this->parametersKey])) {
            $this->addToParameters($mappings[$this->parametersKey]);
        };
        return $this;
    }

    protected function addToDiMappings(array $input)
    {
        foreach ($input as $offset => $data) {
            if (self::$overrideExistingKeys || !isset($this->diMappings[$offset])) {
                $this->diMappings[$offset] = $data;
                $this->registerPublic($offset, $data);
            }
            else {
                throw new \RuntimeException(
                    "Key '$offset' is already set! " .
                    "Make sure DiContainer::\$overrideExistingKeys is set to TRUE if you want to overrde settings."
                );
            }
        }
    }

    protected function addToParameters(array $input)
    {
        foreach ($input as $offset => $data) {
            if (self::$overrideExistingKeys || !isset($this->params[$offset])) {
                $this->params[$offset] = $data;
                $this->registerPublic($offset, $data);
            }
            else {
                throw new \RuntimeException(
                    "Key '$offset' is already set! " .
                    "Make sure DiContainer::\$overrideExistingKeys is set to TRUE if you want to overrde settings."
                );
            }
        }
    }

    private function registerPublic(string $offset, array $mapping)
    {
        if (isset($mapping[$this->publicKey])) {
            $value = $mapping[$this->publicKey];
            if ($value === true || $value == 1 || $value === 'true') {
                $this->registredAsPublic[$offset] = true;
            }
        }
    }





    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                                 PUBLIC GET
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    public function getFromPublic(string $offset) : ?object
    {
        if (isset($this->registredAsPublic[$offset])) {
            return $this->getInstance($offset);
        }
        return null;
    }

    public function get(string $offset, ?array $overrideArgumentList = null) : ?object
    {
        return $this->getInstance($offset, null, $overrideArgumentList);
    }

    private function getInstance(string $offset, DiNode $parent = null, $overrideArgumentList = null) : ?object
    {
        $instance     = null;
        $mapping      = $this->fetchDiMapping($offset, $overrideArgumentList);
        $instance     = $this->fetchStoredInstancefromMapping($mapping);
        if (is_object($instance)) {
            return $instance;
        }
        $className    = $mapping[$this->classKey];
        $diNode       = new DiNode($offset, $parent);
        $staticMethod = $this->fetchStaticGenerationMethodName($mapping);
        $args         = $this->getArgs($mapping, $diNode);
        $isSingleton  = $this->isSingleton($mapping);
        if ($staticMethod) {
            $instance = $className::$staticMethod(...$args);
        }
        else {
            $instance = new $className(...$args);
        }
        if ($isSingleton) {
            $this->storeInstanceToMapping($offset, $instance);
        }
        return $instance;
    }




    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                                 FETCH
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    private function fetchDiMapping(string $offset, ?array $overrideArgumentList = null) : array
    {
        if (! isset($this->diMappings[$offset])) {
            throw new \InvalidArgumentException("Key '$offset' not found in the mapping!");
        }

        $mapping = $this->diMappings[$offset];
        if (! isset($mapping[$this->classKey])) {
            throw new \InvalidArgumentException(
                "Entry with key '$offset': No declaration of 'class'. Cannont instantiate. "
            );
        }

        // $overrideArgumentList = injection "on-the-fly"
        if (isset($mapping[$this->argumentsKey]) &&
            ! empty($mapping[$this->argumentsKey]) &&
            ! empty($overrideArgumentList)
        ) {
            if ($this->isSingleton($mapping)) {
                throw new \RuntimeException(
                    "Detected injection on-the-fly on key '$offset'. \n".
                    "Injection on the fly not allowed for Singleton object.");
            }
            $argumentList = $mapping[$this->argumentsKey];
            foreach ($overrideArgumentList as $offset => $value) {
                if (isset($argumentList[$offset])) {
                    $mapping[$this->argumentsKey][$offset] = $value;
                }
            }
        }

        return $mapping;
    }


    private function fetchParamByOffset(string $offset)
    {
        if (! isset($this->params[$offset])) {
            throw new \InvalidArgumentException("Key '$offset' not found in the params-mapping!");
        }
        return $this->params[$offset];
    }



    private function  fetchStaticGenerationMethodName(array $mapping) : ?string
    {
        if (isset($mapping[$this->staticMethodKey]) &&
            is_string($mapping[$this->staticMethodKey]) &&
            ! empty($mapping[$this->staticMethodKey])
        ) {
            return $mapping[$this->staticMethodKey];
        }
        return null;
    }






    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                            STORED INSTANCE / SINGLETON
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    private function fetchStoredInstancefromMapping(array $mapping) : ?object
    {
        if (isset($mapping[$this->instanceKey]) && is_object($mapping[$this->instanceKey])) {
            return $mapping[$this->instanceKey];
        }
        return null;
    }

    private function isSingleton(array $mapping) : bool
    {
        if (isset($mapping[$this->singletonFlagKey])) {
            $value = $mapping[$this->singletonFlagKey];
            if ($value === true || $value == 1 || $value === 'true') {
                return true;
            }
        }
        return false;
    }

    private function storeInstanceToMapping(string $offset, object $instance)
    {
        $this->diMappings[$offset][$this->instanceKey] = $instance;
    }








    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                                 ARGUMENT-LIST
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    private function getArgs(array $mapping, DiNode $diNode) : array
    {
        $args = [];
        if (isset($mapping[$this->argumentsKey])) {
            if (! is_iterable($mapping[$this->argumentsKey])) {
                throw new \InvalidArgumentException(
                    sprintf("Args must be array/iterable! '%s' given istead. ", gettype($mapping[$this->argumentsKey]))
                );
            }
            $args = $this->instantiateArgumentList($mapping[$this->argumentsKey], $diNode);
        }
        return $args;
    }

    private function instantiateArgumentList(iterable $arglist, DiNode $diNode) : array
    {
        $ret = [];
        foreach ($arglist as $item) {
            $argument = null;
            if (is_string($item)) {
                $ret[] = $this->fetchByArgumentItemString($item, $diNode);
            }
            else {
                $ret[] = $item;
            }
        }
        return $ret;
    }

    private function fetchByArgumentItemString(string $item, DiNode $diNode)
    {
        $className = $this->syntaxMapper->getClassName($item);
        if (is_string($className)) {
            return $this->getInstance($className, $diNode);
        }
        $parametersArrayName = $this->syntaxMapper->getParametersName($item);
        if (is_string($parametersArrayName)) {
            return $this->fetchParamByOffset($parametersArrayName);
        }
        return $item;
    }
}