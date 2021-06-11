<?php

namespace Miniature\DiContainer\Syntax;

use Miniature\DiContainer\Syntax\MapperAbstract;

class MapperNative extends MapperAbstract
{
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   KEYS
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    // Mapping-Categories
    protected string $diMappingKey     = 'di_mapping';
    protected string $parametersKey    = 'params';

    // Constructor-Data
    protected string $classKey         = 'class';
    protected string $argumentsKey     = 'args';
    protected string $staticMethodKey  = 'static';

    // Singleton and Stored Instance
    protected string $singletonFlagKey = 'singleton';
    protected string $instanceKey      = 'instance';

    // Accessebility via Component
    protected string $publicKey        = 'public';


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   CLASS-NAMES
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function isClassNotation(string $string) : bool
    {
        return substr($string, 0, 1) === '@';
    }

    public function getClassName(string $string) : ?string
    {
        if ($this->isClassNotation($string)) {
            return substr($string, 1);
        }
        return null;
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   PARAMETERS-NAMES
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function isParametersKey(string $string) : bool
    {
        return substr($string, 0, 1) === '%';
    }

    public function getParametersName(string $string) : ?string
    {
        if ($this->isParametersKey($string)) {
            if (substr($string, -1) === '%') {
                return substr($string, 1, -1);
            }
            return substr($string, 1);
        }
        return null;
    }
}