<?php

namespace KapRequireJs\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class RequireJs extends AbstractHelper implements FactoryInterface
{
    protected $loadModules = array();
    protected $config = [];
    protected $buildConfigUrl;
    protected $paths = [];
    protected $requireJsUrl = 'vendor/requirejs/require.js';
    protected $configUrl = 'config.js';

    public function __invoke($module = null)
    {
        if ($module !== null) {
            return $this->loadModule($module);
        }
        return $this;
    }

    /**
     * Factory for itself
     * @param ServiceLocatorInterface $serviceLocator
     * @return \self
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->getServiceLocator()->get('Config');
        if (empty($config['requirejs'])) {
            return new self;
        }

        $options = $config['requirejs'];

        $helper = new self;
        $helper->setOptions($options);
        
        return $helper;
    }

    public function __toString()
    {
        return $this->render();
    }
    
    public function setOptions(array $options)
    {
        if(!empty($options['config_url'])) {
            $this->setConfigUrl($options['config_url']);
        }
        
        if(!empty($options['build_config_url'])) {
            $this->setBuildConfigUrl($options['build_config_url']);
        }

        if(!empty($options['config'])) {
            foreach($options['config'] as $module => $data) {
                $this->config($module, $data);
            }
        }

        if(!empty($options['paths'])) {
            $this->setPaths($options['paths']);
        }
    }

    /**
     * Add a module to the array of modules to be loaded
     * @param $module
     * @return self
     */
    public function loadModule($module)
    {
        $module = (array)$module;
        $this->setLoadModules(array_merge($this->loadModules, $module));
        return $this;
    }
    
    public function config($module, $data)
    {
        if(!isset($this->config[$module])) {
            $this->config[$module] = [];
        }

        $this->config[$module] = array_merge_recursive($this->config[$module], $data);
    }

    public function render()
    {
        return $this->getView()->partial(
            'kap-require-js/loader',
            array(
                'requireJsUrl' => $this->getRequireJsUrl(),
                'configUrl' => $this->getConfigUrl(),
                'config' => $this->config,
                'loadModules' => $this->getLoadModules(),
                'buildConfigUrl' => $this->getBuildConfigUrl(),
                'paths' => $this->getPaths(),
            )
        );
    }

    public function getLoadModules()
    {
        $loadModules = $this->loadModules;
        return $loadModules;
    }

    public function setLoadModules($loadModules)
    {
        $this->loadModules = $loadModules;
    }

    public function getBuildConfigUrl()
    {
        return $this->buildConfigUrl;
    }

    public function setBuildConfigUrl($buildConfigUrl)
    {
        $this->buildConfigUrl = $buildConfigUrl;
    }

    /**
     * @param string $requireJsUrl
     */
    public function setRequireJsUrl($requireJsUrl)
    {
        $this->requireJsUrl = $requireJsUrl;
    }

    /**
     * @return string
     */
    public function getRequireJsUrl()
    {
        return $this->requireJsUrl;
    }

    /**
     * @param string $configUrl
     */
    public function setConfigUrl($configUrl)
    {
        $this->configUrl = $configUrl;
    }

    /**
     * @return string
     */
    public function getConfigUrl()
    {
        return $this->configUrl;
    }

    /**
     * @param array $paths
     */
    public function setPaths(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }
}
