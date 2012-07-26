<?php
namespace Jamm\Tester;
/**
 * extend me
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

	public function RunTests()
	{
		$this->setUpBeforeClass();
		$this->RunAllTestsOfClass();
		$this->tearDownAfterClass();
	}

	protected function RunAllTestsOfClass()
	{
		$methods = get_class_methods($this);
		foreach ($methods as $method)
		{
			if (strpos($method, $this->test_method_prefix)===0) $this->RunTestMethod($method);
		}
	}

	private function start_new_test($name)
	{
		$this->current_test = $this->getNewTestObject();
		$this->current_test->setName($name);
		$this->tests[] = $this->current_test;
		return $this->current_test;
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
		try
		{
			$this->$test_method_name();
		}
		catch (\Exception $exception)
		{
			$exception_generated = true;
			if (!$this->exception_expected)
			{
				$test->setException($exception);
			}
		}
		if ($this->exception_expected && !$exception_generated)
		{
			$test->setSuccessful(false);
		}
		if ($error_catcher->hasErrors())
		{
			if (!$this->error_expected)
			{
				$test->setErrors($error_catcher->getErrors());
			}
		}
		else
		{
			if ($this->error_expected)
			{
				$test->setSuccessful(false);
				$Error = new Error();
				$Error->setMessage("Error is expected but wasn't generated");
				$test->setErrors([$Error]);
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

	public function assertEquals($tested_value, $expected_value)
	{
		$assertion = $this->assert($tested_value===$expected_value);
		$assertion->setExpectedResult($expected_value);
		$assertion->setActualResult($tested_value);
		return $assertion;
	}

	public function assertTrue($variable)
	{
		$assertion = $this->assert($variable==true);
		$assertion->setActualResult($variable);
		$assertion->setExpectedResult(true);
		return $assertion;
	}

	public function assertTrueStrict($variable)
	{
		$assertion = $this->assert($variable===true);
		$assertion->setActualResult($variable);
		$assertion->setExpectedResult(true);
		return $assertion;
	}

	public function assertInstanceOf($tested_object, $expected_class_name)
	{
		$assertion = $this->assert(is_a($tested_object, $expected_class_name));
		$assertion->setExpectedResult($expected_class_name);
		$assertion->setActualResult(gettype($tested_object));
		return $assertion;
	}

	public function assertIsArray($array)
	{
		$assertion = $this->assert(is_array($array));
		$assertion->setExpectedResult('array');
		$assertion->setActualResult(gettype($array));
		return $assertion;
	}

	protected function getNewAssertionObject()
	{
		return new Assertion();
	}

	/**
	 * @return Test
	 */
	protected function getNewTestObject()
	{
		return new Test();
	}

	/**
	 * @return ErrorCatcher
	 */
	protected function getErrorCatcherObject()
	{
		return new ErrorCatcher();
	}

	public function setUpBeforeClass()
	{ }

	protected function setUp()
	{ }

	protected function assertPreConditions()
	{ }

	protected function setErrorExpected($value)
	{
		$this->error_expected = $value;
	}

	protected function setExceptionExpected($value)
	{
		$this->exception_expected = $value;
	}

	protected function assertPostConditions()
	{ }

	protected function tearDown()
	{ }

	public function tearDownAfterClass()
	{ }

	protected function onNotSuccessfulTest()
	{ }

	public function getTests()
	{
		return $this->tests;
	}

	public function setTestMethodPrefix($test_method_prefix)
	{
		$this->test_method_prefix = $test_method_prefix;
	}
}
