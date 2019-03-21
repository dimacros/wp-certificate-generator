<?php 
namespace Dimacros;

use Dimacros\Exception\NotFoundDependencyException;

class App
{
    private $pluginName, $dependencies, $active_plugins, $callback;

    public function __construct($pluginName = 'NO_PLUGIN_NAME') 
    {
        $this->pluginName = $pluginName;
        $this->active_plugins = get_option('active_plugins');
    }

    public function getActivePlugins() 
    {
        return $this->active_plugins;
    }

    public function handle(callable $callback) 
    {
        $this->callback = $callback;

        return $this;
    }

    public function withDependencies(array $dependencies) 
    {
        $this->dependencies = $dependencies;

        return $this;
    }

    public function start() 
    {
        if( $this->hasDependencies() ) {
            $this->resolveDependencies();
        }
        
        return ($this->callback)();
    }

    private function hasDependencies() {
        
        if( empty($this->dependencies) ) {
            return false;
        }

        return true;
    }

    private function resolveDependencies() 
    {    
        for ($i=0; $i < count($this->dependencies); $i++) {

            if ( in_array($this->dependencies[$i], $this->active_plugins) ) {
                continue;
            }

            throw new NotFoundDependencyException(
                "El plugin `{$this->dependencies[$i]}` es requerido en {$this->pluginName}"
            );

        }
    }
}