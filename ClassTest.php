<?php
namespace Jamm\Tester;
/**
 * extend this class
 */
class ClassTest
{
  /** @var Test */
  private $current_test;
  /** @var Test[] */
  private $tests = array();
  private $test_method_prefix = 'test';
  private $error_expected = false;
  private $exception_expected = false;
  private $skip_tests = array();

  public function RunTests()
  {
    $this->setUpBeforeClass();
    $this->RunAllTestsOfClass();
    $this->tearDownAfterClass();
  }

  public function setUpBeforeClass()
  {
  }

  public function tearDownAfterClass()
  {
  }

  public function skipTest($method_names)
  {
    if (!is_array($method_names)) {
      $method_names = array($method_names);
    }
    foreach ($method_names as $method_name) {
      $this->skip_tests[$method_name] = 1;
    }
  }

  /**
   * @param string|array $method_names
   */
  public function skipAllExcept($method_names)
  {
    if (!is_array($method_names)) {
      $method_names = array($method_names);
    }
    $methods          = get_class_methods($this);
    $this->skip_tests = array();
    foreach ($methods as $method) {
      if (in_array($method, $method_names)) {
        continue;
      }
      if (strpos($method, $this->test_method_prefix) === 0) {
        $this->skip_tests[$method] = 1;
      }
    }
  }

  public function skipAllExceptLast()
  {
    $methods          = get_class_methods($this);
    $this->skip_tests = array();
    foreach ($methods as $method) {
      if (strpos($method, $this->test_method_prefix) === 0) {
        $this->skip_tests[$method] = 1;
      }
    }
    array_pop($this->skip_tests);
  }

  public function assertEquals($tested_value, $expected_value, $strict = false)
  {
    if ($strict) {
      $assertion = $this->assert($tested_value === $expected_value);
    }
    else {
      $assertion = $this->assert($tested_value == $expected_value);
    }
    $assertion->setExpectedResult($expected_value);
    $assertion->setActualResult($tested_value);
    return $assertion;
  }

  public function assertTrue($variable)
  {
    $assertion = $this->assert($variable == true);
    $assertion->setActualResult($variable);
    $assertion->setExpectedResult(true);
    return $assertion;
  }

  public function assertTrueStrict($variable)
  {
    $assertion = $this->assert($variable === true);
    $assertion->setActualResult($variable);
    $assertion->setExpectedResult(true);
    return $assertion;
  }

  public function assertInstanceOf($tested_object, $expected_class_name)
  {
    $assertion = $this->assert(is_a($tested_object, $expected_class_name));
    $assertion->setExpectedResult($expected_class_name);
    $assertion->setActualResult(get_class($tested_object));
    return $assertion;
  }

  public function assertArrayHasKey(&$array, $key)
  {
    if (!is_array($array)) {
      return $this->assertIsArray($array);
    }
    $assertion = $this->assert(array_key_exists($key, $array));
    $assertion->setExpectedResult(true);
    $assertion->setActualResult(array_key_exists($key, $array));
    return $assertion;
  }

  public function assertArrayHasKeys(&$array, $keys)
  {
    if (!is_array($array)) {
      return $this->assertIsArray($array);
    }
    if (!is_array($keys)) {
      return $this->assertIsArray($keys);
    }
    $all_successful = true;
    $not_found      = [];
    if (!empty($keys)) {
      foreach ($keys as $key) {
        $a = $this->assertArrayHasKey($array, $key);
        if (!$a->isSuccessful()) {
          $all_successful = false;
          $not_found[]    = $key;
        }
      }
    }

    $assertion = $this->assert($all_successful);
    $assertion->setExpectedResult(true);
    $assertion->setActualResult($all_successful);
    if (!empty($not_found)) {
      $assertion->addCommentary('not found keys: '.implode(', ', $not_found));
    }
    return $assertion;
  }

  public function assertArrayHasSameKeys(&$array, $array_with_expected_keys)
  {
    return $this->assertArrayHasKeys($array, array_keys($array_with_expected_keys));
  }

  public function assertIsArray(&$array)
  {
    $assertion = $this->assert(is_array($array));
    $assertion->setExpectedResult('array');
    $assertion->setActualResult(gettype($array));
    return $assertion;
  }

  public function assertGreaterThan($value, $greater_than)
  {
    $assertion = $this->assert($value > $greater_than);
    $assertion->setExpectedResult('> '.$greater_than);
    $assertion->setActualResult($value);
    return $assertion;
  }

  public function assertGreaterOrEqualThan($value, $greater_than)
  {
    $assertion = $this->assert($value >= $greater_than);
    $assertion->setExpectedResult('>= '.$greater_than);
    $assertion->setActualResult($value);
    return $assertion;
  }

  public function assertLessThan($value, $less_than)
  {
    $assertion = $this->assert($value < $less_than);
    $assertion->setExpectedResult('< '.$less_than);
    $assertion->setActualResult($value);
    return $assertion;
  }

  public function assertLessOrEqualThan($value, $less_than)
  {
    $assertion = $this->assert($value <= $less_than);
    $assertion->setExpectedResult('<= '.$less_than);
    $assertion->setActualResult($value);
    return $assertion;
  }

  public function assertIsNumeric($numeric)
  {
    $assertion = $this->assert(is_numeric($numeric));
    $assertion->setExpectedResult(true);
    $assertion->setActualResult(gettype($numeric));
    return $assertion;
  }

  public function assertIsEmpty($value)
  {
    $assertion = $this->assert(empty($value));
    $assertion->setExpectedResult(true);
    $assertion->setActualResult(print_r($value, 1));
    return $assertion;
  }

  public function assertIsNotEmpty(&$value)
  {
    $assertion = $this->assert(!empty($value));
    $assertion->setExpectedResult(true);
    $assertion->setActualResult(print_r($value, 1));
    return $assertion;
  }

  public function assertIsNull(&$value)
  {
    $assertion = $this->assert(is_null($value));
    $assertion->setExpectedResult(true);
    $assertion->setActualResult(print_r($value, 1));
    return $assertion;
  }

  public function assertIsNotNull(&$value)
  {
    $assertion = $this->assert(!is_null($value));
    $assertion->setExpectedResult(true);
    $assertion->setActualResult(print_r($value, 1));
    return $assertion;
  }

  public function assertIsValueOfType(&$value, $type)
  {
    $assertion = $this->assert($type === gettype($value));
    $assertion->setExpectedResult($type);
    $assertion->setActualResult(gettype($value));
    return $assertion;
  }

  public function assertIsCallable($value)
  {
    $assertion = $this->assert(is_callable($value));
    $assertion->setExpectedResult(true);
    $assertion->setActualResult(gettype($value));
    return $assertion;
  }

  public function assertIsResource($value)
  {
    $assertion = $this->assert(is_resource($value));
    $assertion->setExpectedResult(true);
    $assertion->setActualResult(gettype($value));
    return $assertion;
  }

  public function assertIsObject($value)
  {
    $assertion = $this->assert(is_object($value));
    $assertion->setExpectedResult('object');
    $assertion->setActualResult(gettype($value));
    return $assertion;
  }

  public function assertIsScalar($value)
  {
    $assertion = $this->assert(is_scalar($value));
    $assertion->setExpectedResult('scalar');
    $assertion->setActualResult(gettype($value));
    return $assertion;
  }

  public function getTests()
  {
    return $this->tests;
  }

  public function setTestMethodPrefix($test_method_prefix)
  {
    $this->test_method_prefix = $test_method_prefix;
  }

  protected function RunAllTestsOfClass()
  {
    $methods = get_class_methods($this);
    foreach ($methods as $method) {
      if (!empty($this->skip_tests[$method])) {
        continue;
      }
      if (strpos($method, $this->test_method_prefix) === 0) $this->RunTestMethod($method);
    }
  }

  /**
   * @return ErrorCatcher
   */
  protected function getErrorCatcherObject()
  {
    return new ErrorCatcher();
  }

  /**
   * @return Test
   */
  protected function getNewTestObject()
  {
    return new Test();
  }

  protected function setUp()
  {
  }

  protected function assertPreConditions()
  {
  }

  protected function assertPostConditions()
  {
  }

  protected function tearDown()
  {
  }

  protected function onNotSuccessfulTest()
  {
  }

  protected function getNewAssertionObject()
  {
    return new Assertion();
  }

  protected function setErrorExpected($value)
  {
    $this->error_expected = $value;
  }

  protected function setExceptionExpected($value)
  {
    $this->exception_expected = $value;
  }

  private function RunTestMethod($test_method_name)
  {
    $error_catcher = $this->getErrorCatcherObject();
    $error_catcher->setUp();
    $this->resetErrorsExpectations();
    $test = $this->start_new_test($test_method_name);
    $this->setUp();
    $this->assertPreConditions();
    $exception_generated = false;
    try {
      $this->$test_method_name();
    }
    catch (\Exception $exception) {
      $exception_generated = true;
      if (!$this->exception_expected) {
        $test->setException($exception);
      }
    }
    if ($this->exception_expected && !$exception_generated) {
      $test->setSuccessful(false);
    }
    if ($error_catcher->hasErrors()) {
      if (!$this->error_expected) {
        $test->setErrors($error_catcher->getErrors());
      }
    }
    else {
      if ($this->error_expected) {
        $test->setSuccessful(false);
        $Error = new Error();
        $Error->setMessage("Error is expected but wasn't generated");
        $test->setErrors(array($Error));
      }
    }
    $this->assertPostConditions();
    $this->tearDown();
    if (!$test->isSuccessful()) $this->onNotSuccessfulTest();
  }

  private function resetErrorsExpectations()
  {
    $this->error_expected     = false;
    $this->exception_expected = false;
  }

  private function start_new_test($name)
  {
    $this->current_test = $this->getNewTestObject();
    $this->current_test->setName($name);
    $this->tests[] = $this->current_test;
    return $this->current_test;
  }

  /**
   * Each assert* method should call this function
   * @param $expression
   * @return Assertion
   */
  private function assert($expression)
  {
    $assertion = $this->getNewAssertionObject();
    $assertion->Assert($expression);
    $this->addAssertionToCurrentTest($assertion);
    return $assertion;
  }

  private function addAssertionToCurrentTest(Assertion $assertion)
  {
    if (empty($this->current_test)) $this->start_new_test($assertion->getName());
    $this->current_test->addAssertion($assertion);
  }
}
