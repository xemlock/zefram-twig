<?php

class Zefram_Twig_Environment extends Zwig_Environment
{
    /**
     * @var array
     */
    protected $viewHelpers = array();

    public function __construct(Zwig_View $view, Twig_LoaderInterface $loader = null, array $options = array())
    {
        parent::__construct($view, $loader, $options);

        if (isset($options['view_helpers']) && is_array($options['view_helpers'])) {
            // empty safe values are saved as arrays to avoid lookups in
            // Euhit_Zwig_Function_ViewHelper::getSafe() triggered by null value
            foreach ($options['view_helpers'] as $name => $safe) {
                $this->viewHelpers[$name] = empty($safe) ? array() : $safe;
            }
        }
    }

    public function getFunction($name)
    {
        // When loading function do not user Zwig_Enviroment::getFunction() as
        // it is incompatibile with extension initialization check introduced
        // to Twig_Environment::getFunction() in commit 44873875ff 
        // (Nov 30, 2012).
        if (false !== $function = Twig_Environment::getFunction($name)) {
            return $function;
        }

        $helper = $this->view->getHelper($name);
        if (null === $helper) {
            return false;
        }

        // get safeness for view helper found
        $className = get_class($helper);
        $safe = isset($this->viewHelpers[$className]) ? $this->viewHelpers[$className] : null;

        $function = new Zefram_Twig_Function_ViewHelper($name, $helper, $safe);
        // $function = new Zwig_Function_ViewHelper($name, $helper);

        $initialized = $this->extensionInitialized;
        $this->extensionInitialized = false;

        try {
            $this->addFunction($name, $function);
        } catch (Exception $e) {
            $this->extensionInitialized = $initialized;
            throw $e;
        }

        $this->extensionInitialized = $initialized;

        return $function;
    }
}
