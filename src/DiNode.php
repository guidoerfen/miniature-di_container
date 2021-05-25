<?php

namespace Miniature\DiContainer;

/**
 * Class DiNode
 *
 * The nodes build a tree-structure. With every instantiation the tree is bubbled up
 * to the ancestors in order to prevent deadlocks caused by the DI-wiring.
 *
 * @package Miniature\DiContainer
 * @author Guido Erfen <sourcecode@erfen.de>
 * @see https://github.com/guidoerfen/miniature-component
 */
class DiNode
{
    private ?string $name;
    private ?DiNode $parent;
    private array $children = [];


    public function __construct(string $name, DiNode $parent = null)
    {
        $this->name   = $name;
        $this->parent = $parent;
        if ($parent instanceof DiNode) {
            $this->parent->setChild($name, $this);
        }
    }

    public function setChild(string $name, object $child)
    {
        $this->checkDeadlock($name);
        $this->children[$name] = $child;
    }

    private function checkDeadlock($name)
    {
        if ($name == $this->name) {
            $rootName = $this->getRootName();
            throw new \RuntimeException(
                "Error in the dependency injection wiring proved while trying to instantiate by the key of '$rootName'. ".
                "A dependency with the key of '$name' happens to be an previous instantiator. ".
                'This would lead to a deadlock. '
            );
        }
        if ($this->parent instanceof DiNode) {
            $this->parent->checkDeadlock($name);
        }
    }

    public function getRootName()
    {
        if ($this->parent instanceof DiNode) {
            return $this->parent->getRootName();
        }
        return $this->name;
    }
}