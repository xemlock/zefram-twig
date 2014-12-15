<?php

class Zefram_Twig_Function_ViewHelper extends Zwig_Function_ViewHelper
{
    /**
     * @var array
     */
    protected $safe;

    /**
     * @param  string $name
     * @param  object $helper
     * @param  string|array $safe
     * @throws InvalidArgumentException
     */
    public function __construct($name, $helper, $safe = null)
    {
        if (!is_object($helper) || !method_exists($helper, $name)) {
            throw new InvalidArgumentException('The object provided cannot be used as a view helper');
        }

        $this->name = $name;
        $this->helper = $helper;

        if (null !== $safe) {
            $this->safe = (array) $safe;
        }
    }

    /**
     * @return array
     */
    public function getSafe(Twig_Node $functionArgs)
    {
        if (null === $this->safe) {
            $safe = parent::getSafe($functionArgs);
        } else {
            $safe = $this->safe;
        }
        return (array) $safe;
    }
}
