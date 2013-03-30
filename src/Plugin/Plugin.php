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

namespace Compage\Plugin;

use Compage\Essentials\Context;
use Compage\Essentials\Pluggable;

/* Handles plugin common tasks. */
abstract class Plugin extends Pluggable {

  /* An array of dependencies */
  protected $dependencies;

  public function __construct($plugin_base_file) {
    parent::__construct($plugin_base_file);
    $this->dependencies = array();
  }

  private function notify($msg, $type) {
    return new Notification($msg, $type);
  }

  public function info($msg) {
    return $this->notify($msg, "info");
  }

  public function alert($msg) {
    return $this->notify($msg, "error");
  }

  /* Register a dependency for the plugin. */
  public function requiresDependency($short_name) {
    $this->dependencies[] = $short_name;
  }

  public function checkDependencies() {
    $missing = array();
    foreach ($this->dependencies as $dependency) {
      if (Plugin::exists($dependency) === false) {
        $missing[] = $dependency;
      }
    }
    if (count($missing) > 0) {
      $this->alert("Error: {$this->getName()} requires: " . implode(", ", $missing));
      return false;
    } else {
      return true;
    }
  }

  static public function run($instance, $file) {
    $class = self::exists($instance);
    if ($class !== false) {
      $plugin = new $class($file);
      add_action("wp_loaded", function() use ($plugin) { 
        $plugin->checkDependencies(); 
      });
      Context::registerPluggable($plugin);
    }
  }
}