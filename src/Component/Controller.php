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

namespace Compage\Component;

class Controller extends Component {

  public function __construct($pluggable, $name = "") {
    parent::__construct($pluggable);
    $this->setComponentType(ComponentType::Controller);
    $this->setComponentName(basename($name));
  }

  public function instantiate() {
    $component = $this->getPluggable()->getFullyQualifiedName()
               . "\\" . $this->getPluggable()->getDirectory(ComponentType::Controller)
               . "\\" . $this->getComponentName();
    $class = new \ReflectionClass($component);
    return new $component($this->getPluggable(), $this->getComponentName());
  }

  public function hook($callback) {
    if (is_string($callback) && substr($callback, 0, 2) == "::") {
      $controller = $this;
      return new Hook($this->getPluggable(), function() use ($controller, $callback) {
        $component = $controller->getComponentName();
        $callback = str_replace("::", "", $callback);
        $component::$callback();
      });
    } else {
      if ((is_object($callback) && ($callback instanceof \Closure))) {
        return new Hook($this->getPluggable(), $callback);  
      } else if (is_string($callback)) {
        return new Hook($this->getPluggable(), array($this, $callback));
      }
    }
  }

  public function useView($view) {
    return new View($this->getPluggable(), $view);
  }

}