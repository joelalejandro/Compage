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

namespace Compage\Essentials;

use Compage\Component\ComponentType;
use Compage\Component\Component;
use Compage\Component\Controller;
use Compage\Component\Entity;
use Compage\Component\Hook;
use Compage\Component\View;
use Compage\Extensions\CustomPostType\CustomPostType;
use Compage\Extensions\CustomTaxonomy\CustomTaxonomy;
use Compage\Extensions\Shortcode\Shortcode;

abstract class Pluggable {

  protected $rootFile;

  protected $components;

  protected $name;

  protected $fullyQualifiedEntityName;

  protected $directories;

  protected $type;

  protected $customPostTypes;

  protected $customTaxonomies;

  protected $shortcodes;

  protected $locales;

  public function __construct($root_file = null) {
    $this->rootFile = $root_file;
    $this->directories = array(
      ComponentType::Controller => "Controllers",
      ComponentType::Asset => "Assets",
      ComponentType::View => "Views",
      ComponentType::Log => "Log",
      ComponentType::SqlStructure => "Tables",
      ComponentType::SqlData => "Tables/Data",
      ComponentType::Entity => "Entities",
      ComponentType::Shortcode => "Shortcodes",
      ComponentType::CustomPostType => "CustomPostTypes",
      ComponentType::CustomTaxonomy => "CustomTaxonomies",
      ComponentType::Locale => "Locale"
    );
    $this->type = PluggableType::Generic;
    $this->customPostTypes = array();
    $this->shortcodes = array();
    $this->customTaxonomies = array();
    $this->locales = array();
  }

  public function getDirectory($type) {
    return $this->directories[$type];
  }

  public function getDirectoryAsUri($type) {
    return $this->getRootUri() . "/" . $this->directories[$type];
  }

  public function setDirectory($type, $dir) {
    $this->directories[$type] = $dir;
    return $this;
  }

  public function getType() {
    return $this->type;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  public function getFullyQualifiedName() {
    return $this->fullyQualifiedEntityName;
  }

  public function setFullyQualifiedName($fqn) {
    $this->fullyQualifiedEntityName = $fqn;
    return $this;
  }

  public function getRootFile() {
    return $this->getRootPath() . "/" . basename($this->file);    
  }

  public function getRootPath() {
    $path = null;
    switch ($this->type) {
      case PluggableType::Plugin:
        $path = WP_PLUGIN_DIR . "/". basename(dirname($this->rootFile));
        break;
      case PluggableType::Theme:
        $path = get_template_directory();
        break;
      default:
        break;
    }
    return $path;
  }

  public function getRootUri() {
    $uri = null;
    switch ($this->type) {
      case PluggableType::Plugin:
        $uri = plugins_url("/", $this->rootFile);
        break;
      case PluggableType::Theme:
        $uri = get_stylesheet_directory_uri();
        break;
      default:
        $uri = "/";
        break;
    }
    return $uri;
  }

  public function getAbsoluteRootPath($type) {
    return $this->getRootPath() . "/" . $this->getDirectory($type);
  }

  protected function registerComponent($type, $name) {
    $component = null;
    switch ($type) {
      case ComponentType::Controller:
        $component = new Controller($this, $name);
        $component->instantiate();
        break;
      case ComponentType::View:
        break;
      case ComponentType::Entity:
        $component = new Entity($this);
        $component->setComponentName($name)
                  ->setTableStructureFile($name . ".tbls.sql")
                  ->setTableDataFile($name . ".tbld.sql");        
        break;
      case ComponentType::Shortcode:
        $component = new Shortcode($this, $name);        
        $this->shortcodes[] = $component->instantiate();
        break;
      case ComponentType::CustomPostType:
        $component = new CustomPostType($this, $name);        
        $this->customPostTypes[] = $component->instantiate();
        break;
      case ComponentType::CustomTaxonomy:
        $component = new CustomTaxonomy($this, $name);
        $this->customTaxonomies[] = $component->instantiate();
        break;
    }
    if ($type != ComponentType::Controller)
      $this->components[] = $component->instantiate();
    else
      $this->components[] = $component;
  }

  public function getComponent($type, $name) {
    $requiredComponent = null;
    foreach ($this->components as $component) {
      if ($component->getComponentType() == $type && $component->getComponentName() == $name) {
        $requiredComponent = $component;
        break;
      }
    }
    return $requiredComponent;
  }

  public function loadAll($type) {
    if (in_array($type, array(ComponentType::Controller,
      ComponentType::View, ComponentType::Entity, ComponentType::Shortcode,
      ComponentType::CustomPostType, ComponentType::CustomTaxonomy))) {
      foreach (glob($this->getRootPath() . "/" . $this->getDirectory($type) . "/*.php") as $component) {
        require_once $component;
        $this->registerComponent($type, basename($component, ".php"));
      }
    }
    return $this;
  }

  /* Loads a specific component. */
  public function load($type, $name) {
    if (in_array($type, array(ComponentType::Controller,
      ComponentType::View, ComponentType::Entity, ComponentType::Shortcode,
      ComponentType::CustomPostType, ComponentType::CustomTaxonomy))) {
      $component = $this->getRootPath() . "/" . $this->getDirectory($type) . "/$name.php";
      require_once $component;
      $this->registerComponent($type, basename($component, ".php"));
    } else if ($type == ComponentType::SqlStructure) {
      $component = $this->getRootPath() . "/" . $this->getDirectory($type) . "/$name.tbls.sql";
      $this->registerComponent($type, basename($component, ".php"));
      return file_get_contents($component);
    } else if ($type == ComponentType::SqlData) {
      $component = $this->getRootPath() . "/" . $this->getDirectory($type) . "/$name.tbld.sql";
      $this->registerComponent($type, basename($component, ".php"));
      return file_get_contents($component);
    }
  }

  public function log($msg) {
    file_put_contents($this->getAbsoluteRootPath(ComponentType::Log) . "/" . date("d-m-Y") . ".log", $msg, FILE_APPEND);
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


}