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

namespace Compage\Extensions\Shortcode;

use Compage\Component\Component;
use Compage\Component\ComponentType;
use Compage\Component\View;

class Shortcode extends Component {

  protected $shortcodeKey;

  public function __construct($pluggable, $name) {
    parent::__construct($pluggable);
    $this->setComponentName($name);
    $this->setKeyword(strtolower(str_replace("Shortcode", "", $name)));
    $this->setComponentType(ComponentType::Shortcode);
  }

  public function setKeyword($key) {
    $this->shortcodeKey = $key;
    return $this;
  }

  public function getKeyword() {
    return $this->shortcodeKey;
  }

  public function render($attributes = array(), $content = "") {
    return $content;
  }

  public function instantiate() {
    $component = $this->getPluggable()->getFullyQualifiedName()
               . "\\" . $this->getPluggable()->getDirectory(ComponentType::Shortcode)
               . "\\" . $this->getComponentName();
    $class = new \ReflectionClass($component);
    return new $component($this->getPluggable(), $this->getComponentName());
  }

  public function useView($view) {
    return new View($this->getPluggable(), $view);
  }  

}