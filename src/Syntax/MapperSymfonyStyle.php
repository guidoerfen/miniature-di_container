<?php
declare(strict_types=1);

namespace Miniature\DiContainer\Syntax;

use Miniature\DiContainer\Syntax\MapperNative;

/**
 * Class MapperSymfonyStyle
 * @package Miniature\DiContainer\Syntax
 * @package Miniature\Component\Reader\Value
 * @author Guido Erfen <sourcecode@erfen.de>
 */
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
    public function isParametersKey(string $string) : bool
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