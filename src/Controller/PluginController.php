<?php
/*
Copyright (c) 2013 Joel A. Villarreal Bertoldi

Permission is hereby granted, free of charge, to any
person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the
Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice
shall be included in all copies or substantial portions of
the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

namespace Compage;

class PluginController extends PluginComponent {

  public function __construct(Plugin $plugin, $name = "") {
    parent::__construct($plugin);
    $this->setComponentType("controller");
    $this->setComponentName(basename($name));
  }

  public function instantiate() {
    $component = $this->getPlugin()->getFullyQualifiedName()
               . "\\" . $this->getPlugin()->getPluginDirectory("controller")
               . "\\" . $this->getComponentName();
    $class = new \ReflectionClass($component);
    if (!$class->isAbstract()) {
      return new $component($this->getPlugin(), $this->getComponentName());
    } else {
      $component::bindToPlugin($this->getPlugin());
    }
  }

  public function hook($callback) {
    if (is_string($callback) && substr($callback, 0, 2) == "::") {
      $controller = $this;
      return new PluginHook($this->getPlugin(), function() use ($controller, $callback) {
        $component = $controller->getComponentName();
        $callback = str_replace("::", "", $callback);
        $component::$callback();
      });
    } else {
      if ((is_object($callback) && ($callback instanceof \Closure))) {
        return new PluginHook($this->getPlugin(), $callback);  
      } else if (is_string($callback)) {
        return new PluginHook($this->getPlugin(), array($this, $callback));
      }
    }
  }

  public function useTemplate($template) {
    return new PluginTemplate($this->getPlugin(), $template);
  }

}