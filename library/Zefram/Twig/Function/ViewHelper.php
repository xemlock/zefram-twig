<?php

class Zefram_Twig_Function_ViewHelper extends Zwig_Function_ViewHelper
{
    /**
     * @var array
     */
    protected $safe;

    /**
     * @param string $name
     * @param Zend_View_Helper_Interface $helper
     * @param string|array $safe
     */
    public function __construct($name, Zend_View_Helper_Interface $helper, $safe = null)
    {
        parent::__construct($name, $helper);

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
