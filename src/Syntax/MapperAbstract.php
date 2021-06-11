<?php

namespace Miniature\DiContainer\Syntax;

abstract class MapperAbstract
{
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   KEYS
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    // Mapping-Categories
    protected string $diMappingKey;
    protected string $parametersKey;

    // Constructor-Data
    protected string $classKey;
    protected string $argumentsKey;
    protected string $instanceKey;

    // Singleton and Stored Instance
    protected string $singletonFlagKey;
    protected string $staticMethodKey;

    // Accessebility via Component
    protected string $publicKey;



    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   SIMPLE GET
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function getDiMappingKey(): string
    {
        return $this->diMappingKey;
    }

    public function getParametersKey(): string
    {
        return $this->parametersKey;
    }

    public function getClassKey(): string
    {
        return $this->classKey;
    }

    public function getInstanceKey(): string
    {
        return $this->instanceKey;
    }

    public function getArgumentsKey(): string
    {
        return $this->argumentsKey;
    }

    public function getSingletonFlagKey(): string
    {
        return $this->singletonFlagKey;
    }

    public function getStaticMethodKey(): string
    {
        return $this->staticMethodKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   CLASS-NAMES
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public abstract function isClassNotation(string $string) : bool;

    public abstract function getClassName(string $string) : ?string;


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   PARAMETERS-NAMES
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public abstract function isParametersKey(string $string) : bool;

    public abstract function getParametersName(string $string) : ?string;

}