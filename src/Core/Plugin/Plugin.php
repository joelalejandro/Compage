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

/* Handles plugin common tasks. */
abstract class Plugin {

  /* The plugin root file name. */
  protected $file;

  /* An array of components */
  protected $components;

  /* An array of dependencies */
  protected $dependencies;

  /* The plugin's branding name. */
  protected $name;

  /* The plugin's Fully Qualified Name. */
  protected $fqn;

  /* Directory name containing the plugin's controller classes. */
  protected $controller_dir;

  /* Directory name containing the plugin's assets. */
  protected $asset_dir;

  /* Directory name containing the plugin's templates. */
  protected $template_dir;

  /* Directory name containing the plugin's log files. */
  protected $log_dir;

  /* Directory name containing the plugin's initial database files. */
  protected $database_dir;

  /* Directory name containing the plugin's model files. */
  protected $model_dir;

  /* Initialize the plugin */
  protected function initialize($plugin_root_file = null) {
    $this->file = $plugin_root_file;
    $this->controller_dir = "Controllers";
    $this->asset_dir = "Assets";
    $this->template_dir = "Templates";
    $this->log_dir = "Log";
    $this->database_dir = "Database";
    $this->model_dir = "Models";    
    $this->dependencies = array();
    return $this;
  }

  public function get($entity) {
    $com = null;
    foreach ($this->components as $component) {
      if (stripos($component->getComponentName(), $entity) !== false) {
        if ($component->getComponentType() == "controller") {
          return $component->instantiate();
        }
      }
    }
  }

  public function getPluginDirectory($type) {
    return $this->{$type."_dir"};
  }

  public function setPluginDirectory($type, $dir) {
    $this->{"$type_dir"} = $dir;
    return $this;
  }

  public function getPluginDirectoryAsFullPath($type) {
    return $this->getRootPath() . "/" . $this->{$type . "_dir"};
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  public function getFullyQualifiedName() {
    return $this->fqn;
  }

  public function setFullyQualifiedName($fqn) {
    $this->fqn = $fqn;
    return $this;
  }

  /* Based on the plugin root file, calculate its path. */

  public function getRootPath() {

    return WP_PLUGIN_DIR . "/". basename(dirname($this->file));

  }

  /* Based on the plugin root file, calculate the fully qualified path and filename. */

  public function getRootFile() {

    return $this->getRootPath() . "/" . basename($this->file);

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
  
    /*
    if (Plugin::exists($short_name) !== false) {
      return $this;
    } else {
      $this->alert("Error: {$this->getName()} requires {$short_name}.");
      return $this;
    }*/

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

  private function registerComponent($type, $name) {
    $component = null;
    if ($type == "controller") {
      $component = new PluginController($this, $name);
      $component->instantiate();
    } else if ($type == "model") {
      $component = new PluginModel($this);
      $component->setComponentName($name)
                ->setComponentType($type)
                ->setTableStructureFile($name . ".tbls.sql")
                ->setTableDataFile($name . ".tbld.sql");
    }
    $this->components[] = $component;
  }

  /* Attempts to load components, according to Base standards. */
  public function loadAll($type) {
    if (in_array($type, array("controller", "template", "model"))) {
      foreach (glob($this->getRootPath() . "/" . $this->getPluginDirectory($type) . "/*.php") as $component) {
        require_once $component;
        $this->registerComponent($type, basename($component, ".php"));
      }
    }
    return $this;
  }

  /* Loads a specific component. */
  public function load($type, $name) {
    if (in_array($type, array("controller", "template", "model"))) {
      $component = $this->getRootPath() . "/" . $this->getPluginDirectory($type) . "/$name.php";
      require_once $component;
      $this->registerComponent($type, basename($component, ".php"));
    } else if ($type == "table") {
      $component = $this->getRootPath() . "/" . $this->getPluginDirectory("database") . "/$name.tbls.sql";
      $this->registerComponent($type, basename($component, ".php"));
      return file_get_contents($component);
    } else if ($type == "table_data") {
      $component = $this->getRootPath() . "/" . $this->getPluginDirectory("database") . "/$name.tbld.sql";
      $this->registerComponent($type, basename($component, ".php"));
      return file_get_contents($component);
    }
  }

  public function log($msg) {
    file_put_contents($this->getPluginDirectoryAsFullPath("log") . "/" . date("d-m-Y") . ".log", $msg, FILE_APPEND);
  }

  static public function exists($class_name) {
    $found = false;
    foreach (get_declared_classes() as $instance) {
      if (in_array($class_name, explode("\\", $instance))) {
        $found = $instance;
        break;
      }
    }
    return $found;
  }

  static public function run($instance, $file) {
    $class = self::exists($instance);
    if ($class !== false) {
      $plugin = new $class($file);
      add_action("wp_loaded", function() use ($plugin) { $plugin->checkDependencies(); });
      Context::registerPlugin($plugin);
    }
  }
}