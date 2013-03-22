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

class PluginTemplate extends PluginComponent {

  protected $context;
  protected $extension;

  public function __construct(Plugin $plugin, $template_name) {
    parent::__construct($plugin);
    $this->setComponentName($template_name);
    $this->context = array();
  }

  public function getFileName() {
    return $this->getPlugin()->getPluginDirectoryAsFullPath("template") . "/"
      . $this->getComponentName() . "." . $this->extension;
  }

  public function withExtension($extension) {
    $this->extension = $extension;
    return $this;
  }

  public function withContext($context) {
    $this->context = $context;
    return $this;
  }

  public function load() {
    foreach ($this->context as $key => $value) {
      $$key = $value;
    }

    include $this->getFileName();
  }

  public function get() {
    return file_get_contents($this->getFileName());    
  }

  public function render($retval = false) {
    if ($this->extension == "html") { 
      $code = $this->get();

      foreach ($this->context as $token => $value) {
        $code = str_replace("{" . $token . "}", $value, $code);
      }

      if ($retval) return $code; else echo $code;
    } else if ($this->extension == "php") {
      $this->load();
    }
  }

}