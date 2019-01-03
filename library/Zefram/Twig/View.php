<?php

class Zefram_Twig_View extends Zwig_View
{
    /**
     * Constructor.
     *
     * Twig environment will be configured using options passed in array
     * under 'twig' key. Other options will be passed to Zend_View_Abstract
     * constructor.
     *
     * @param  array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['twig']) && is_array($config['twig'])) {
            $twigConfig = $config['twig'];
            unset($config['twig']);
        } else {
            $twigConfig = null;
        }

        $loader = new Twig_Loader_Filesystem(array());
        $twig = new Zefram_Twig_Environment($this, $loader, (array) $twigConfig);

        if (isset($twigConfig['filters'])) {
            foreach ($twigConfig['filters'] as $name => $callback) {
                $twig->addFilter(new Twig_SimpleFilter($name, $callback));
            }
        }

        // enable dump() function if debug flag is on

        if (isset($twigConfig['debug']) && $twigConfig['debug']) {
            $twig->addExtension(new Twig_Extension_Debug);
        }

        $this->setEngine($twig);

        // Zend_View_Abstract constructor sucks a little, due to how the support
        // for different view engines is done. Initial variable assignment based
        // on config is performed before calling init(), which is where the view
        // initialization is expected to take place.

        parent::__construct($config);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getScriptPath($name)
    {
        // override _script as Zwig_View::_script performs no lookup.
        // Fortunately Zend_View_Abstract and Twig_Loader_Filesystem
        // both search directories in the same order.
        try {
            return Zend_View_Abstract::_script($name);
        } catch (Zend_View_Exception $e) {
        }
        return false;
    }

    public function _run()
    {
        // Override Zwig_View::_run() because Twig_Loader_Filesystem requires all
        // paths to exist, whereas Zend_View_Abstract does not. Before passing paths
        // to Twig loader filter out non-existent directories.
        $script = func_get_arg(0);
        if (!$this->_pathSet && method_exists($this->_zwig->getLoader(), 'setPaths')) {
            $this->_zwig->getLoader()->setPaths(array_filter($this->getScriptPaths(), 'file_exists'));
            $this->_pathSet = true;
        }
        $template = $this->_zwig->loadTemplate($script);
        $template->display(get_object_vars($this));
    }
}
