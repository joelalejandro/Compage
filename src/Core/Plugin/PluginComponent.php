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

abstract class PluginComponent {

  protected $__plugin;

  protected $__name;

  protected $__type;

  public function __construct(Plugin $plugin) {
    $this->__plugin = $plugin;
  }

  public function getComponentName() {
    return $this->__name;
  }

  public function setComponentName($name) {
    $this->__name = $name;
    return $this;
  }

  public function getComponentType() {
    return $this->__type;
  }

  public function setComponentType($type) {
    $this->__type = $type;
    return $this;
  }

  public function getPlugin() {
    return $this->__plugin;    
  }

}