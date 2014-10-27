<?php
namespace Jamm\Tester;

class TestsRunner
{
  public function runInFolder($folder, $suffix = '', $prefix = '')
  {
    if (!$folder) {
      trigger_error('No folder specified', E_USER_WARNING);
      return false;
    }
    $tests          = array();
    $classes_before = get_declared_classes();
    if (!$this->includeFolder($folder, $prefix, $suffix)) {
      return false;
    }
    $classes_after = get_declared_classes();
    $classes       = array_diff($classes_after, $classes_before);
    if (!empty($classes)) {
      foreach ($classes as $class) {
        try {
          /** @var ClassTest $Test */
          $Test = new $class();
        }
        catch (\Exception $E) {
          continue;
        }
        if (($Test instanceof ClassTest) && get_class($Test) !== 'Jamm\\Tester\\ClassTest') {
          $Test->RunTests();
          $tests = array_merge($tests, $Test->getTests());
        }
      }
    }
    return $tests;
  }

  protected function includeFolder($folder, $prefix, $suffix)
  {
    $files = scandir($folder);
    if (empty($files)) {
      trigger_error('Can\'t scan folder '.$folder, E_USER_WARNING);
      return false;
    }
    if ($files[0] === '.') {
      unset($files[0]);
    }
    if (!empty($files[1]) && $files[1] === '..') {
      unset($files[1]);
    }
    if (empty($files)) {
      trigger_error('No files in folder '.$folder, E_USER_WARNING);
      return false;
    }
    foreach ($files as $file) {
      $path = realpath($folder).'/'.$file;
      if (!file_exists($path) || !is_readable($path)) {
        continue;
      }
      if (is_dir($path) && $file != '.' && $file != '..' && $file != './') {
        $this->includeFolder($path, $prefix, $suffix);
        continue;
      }

      if ($prefix) {
        if (stripos($file, $prefix) !== 0) {
          continue;
        }
      }
      if ($suffix) {
        if (stripos($file, $suffix) !== (strlen($file) - strlen($suffix))) {
          continue;
        }
      }
      include_once $path;
    }
    return true;
  }
}
