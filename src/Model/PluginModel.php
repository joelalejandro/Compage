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

class PluginModel extends PluginComponent {

  protected $table_structure_file;
  protected $table_data_file;

  public function __construct(Plugin $plugin) {
    parent::__construct($plugin);
  }

  public function getTableStructureFile() {
    return $this->table_structure_file;
  }

  public function setTableStructureFile($file) {
    if (file_exists($this->getPlugin()->getPluginDirectoryAsFullPath("database") . "/" . $file)) {
      $this->table_structure_file = $file;
    }
    return $this;
  }

  public function getTableDataFile() {
    return $this->table_data_file;
  }

  public function setTableDataFile($file) {
    if (file_exists($this->getPlugin()->getPluginDirectoryAsFullPath("database") . "/" . $file)) {
      $this->table_data_file = $file;
    }    
    return $this;
  }

}