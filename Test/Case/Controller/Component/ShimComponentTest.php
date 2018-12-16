<?php
App::uses('Shim', 'Shim.Lib');
App::uses('ShimComponent', 'Shim.Controller/Component');
App::uses('ShimController', 'Shim.Controller');
App::uses('ShimTestCase', 'Shim.TestSuite');

class ShimComponentTest extends ShimTestCase {

	public $ShimController;

	public function setUp() {
		parent::setUp();

		App::build([
			'Controller' => [CakePlugin::path('Shim') . 'Test' . DS . 'test_app' . DS . 'Controller' . DS],
			'Model' => [CakePlugin::path('Shim') . 'Test' . DS . 'test_app' . DS . 'Model' . DS],
			'View' => [CakePlugin::path('Shim') . 'Test' . DS . 'test_app' . DS . 'View' . DS]
		], App::RESET);

		$this->request = $this->getMock('CakeRequest', ['referer']);
		$this->ShimController = new TestShimComponentController($this->request, new CakeResponse());
		$this->ShimController->constructClasses();
		$this->ShimController->startupProcess();

		Configure::write(Shim::NAMED_PARAMS, false);
		Configure::write(Shim::HANDLE_NAMED_PARAMS, false);
		Configure::write(Shim::CHECK_PATHS, false);
	}

	public function tearDown() {
		parent::tearDown();

		unset($this->ShimController);
		Configure::delete(Shim::NAMED_PARAMS);
	}

	/**
	 * testUrlQueryStrings()
	 *
	 * @return void
	 */
	public function testUrlQueryStrings() {
		Configure::write(Shim::NAMED_PARAMS, true);

		$this->ShimController = new TestShimComponentController(new CakeRequest('/foo/bar?page=3'), new CakeResponse());
		$this->ShimController->constructClasses();
		$this->ShimController->startupProcess();
	}

	/**
	 * @return void
	 */
	public function testCheckPaths() {
		Configure::write(Shim::CHECK_PATHS, true);

		$this->ShimController = new TestShimComponentController(new CakeRequest(), new CakeResponse());
		$this->ShimController->constructClasses();
		$this->ShimController->startupProcess();
	}

	/**
	 * @expectedException EXCEPTION
	 * @expectedExceptionMessage All paths need to have a trailing DS.
	 * @return void
	 */
	public function testCheckPathsMissingTrailingDs() {
		Configure::write('debug', 1);
		Configure::write(Shim::CHECK_PATHS, true);

		App::build(['View' => CakePlugin::path('Shim') . 'View' . DS . 'Foo']);

		$this->ShimController = new TestShimComponentController(new CakeRequest(), new CakeResponse());
		$this->ShimController->constructClasses();
		$this->ShimController->startupProcess();
	}

	/**
	 * @expectedException EXCEPTION
	 * @expectedExceptionMessage All paths need to have a DS as separator, not a hardcoded / or \ slash.
	 * @return void
	 */
	public function testCheckPathsError() {
		Configure::write('debug', 1);
		Configure::write(Shim::CHECK_PATHS, true);

		// Always use the "wrong" type of slash per OS
		$ds = DS === '\\' ? '/' : '\\';

		App::build(['View' => CakePlugin::path('Shim') . 'View' . $ds . 'Foo' . DS]);

		$this->ShimController = new TestShimComponentController(new CakeRequest(), new CakeResponse());
		$this->ShimController->constructClasses();
		$this->ShimController->startupProcess();
	}

	/**
	 * testUrlQueryStrings()
	 *
	 * @expectedException PHPUnit_Framework_Error_Deprecated
	 * @expectedExceptionMessage Named params {"x":"y"} - from /foobar
	 * @return void
	 */
	public function testUrlNamedParams() {
		Configure::write(Shim::NAMED_PARAMS, true);

		$referer = '/foobar';
		$this->request->expects($this->once())
			->method('referer')
			->with(true)
			->will($this->returnValue($referer));

		$this->ShimController = new TestShimComponentController($this->request, new CakeResponse());
		$this->ShimController->request->params['named'] = ['x' => 'y'];

		$this->ShimController->constructClasses();
		$this->ShimController->startupProcess();
	}

	/**
	 * @return void
	 */
	public function testRedirect() {
		Configure::write('debug', 1);
		Configure::write(Shim::HANDLE_NAMED_PARAMS, true);

		$request = new CakeRequest();
		$request->params['controller'] = 'my_controller';
		$request->params['action'] = 'my_action';
		$request->params['named'] = ['x' => 'y'];
		$request->query = ['a' => 'b'];

		$response = $this->getMock('CakeResponse', ['send']);
		$this->ShimController = new TestShimComponentController($request, $response);

		$this->ShimController->constructClasses();
		$this->ShimController->startupProcess();

		$header = $response->header();
		$expected = '/my_controller/my_action?a=b&x=y';
		$this->assertContains($expected, $header['Location']);
		$this->assertSame(302, $response->statusCode());
	}

}

class TestShimComponentController extends ShimController {

	public $uses = [];

	public $components = ['Shim.Shim'];

	/**
	 * Mock out
	 *
	 * @return void
	 */
	public function _stop($status = 0) {
	}

}