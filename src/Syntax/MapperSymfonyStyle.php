<?php

namespace Miniature\DiContainer\Syntax;

use Miniature\DiContainer\Syntax\MapperNative;

class MapperSymfonyStyle extends MapperNative
{
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   KEYS
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    // Mapping-Categories
    protected string $diMappingKey     = 'services';
    protected string $parametersKey    = 'parameters';

    // Constructor-Data
    protected string $argumentsKey     = 'arguments';

    // Singleton and Stored Instance
    protected string $singletonFlagKey = 'shared';





    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *   PARAMETERS-NAMES
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function isParametersKey($string) : bool
    {
        return substr($string, 0, 1) === '%' &&
               substr($string, -1) === '%';
    }

    public function getParametersName(string $string) : ?string
    {
        if ($this->isParametersKey($string)) {
            return substr($string, 1, -1);
        }
        return null;
    }
}