<?php
App::uses('ShimModel', 'Shim.Model');
App::uses('ShimTestCase', 'Shim.TestSuite');

class ShimModelTest extends ShimTestCase {

	public $Post;

	public $User;

	public $modelName = 'User';

	public $fixtures = ['core.user', 'core.post', 'core.author'];

	public function setUp() {
		parent::setUp();

		$this->Post = ClassRegistry::init('ShimAppModelPost');
		$this->User = ClassRegistry::init('ShimAppModelUser');

		Configure::delete('Shim');
	}

	public function tearDown() {
		Configure::delete('Shim');

		parent::tearDown();
	}

	public function testObject() {
		$this->Post = ClassRegistry::init('ShimModel');
		$this->assertTrue(is_object($this->Post));
		$this->assertInstanceOf('ShimModel', $this->Post);
	}

	/**
	 * ShimModelTest::testGet()
	 *
	 * @return void
	 */
	public function testMagicFind() {
		$res = $this->Post->findById(2);
		$this->assertNotEmpty($res);

		$res = $this->Post->findById(121212);
		$this->assertEmpty($res);
	}

	/**
	 * Test the better findById()
	 *
	 * @return void
	 */
	public function testGet() {
		$record = $this->Post->get(2);
		$this->assertEquals(2, $record['Post']['id']);

		$record = $this->Post->get(2, ['fields' => ['id', 'created']]);
		$this->assertEquals(2, count($record['Post']));

		$record = $this->Post->get(2, ['fields' => ['id', 'title', 'body'], 'contain' => ['Author']]);
		$this->assertEquals(3, count($record['Post']));
		$this->assertEquals(3, $record['Author']['id']);
	}

	/**
	 * ShimModelTest::testGetFail()
	 *
	 * @expectedException RECORDNOTFOUNDEXCEPTION
	 * @return void
	 */
	public function testGetFail() {
		$this->Post->get(2222);
	}

	/**
	 * Test the better findById()
	 *
	 * @return void
	 */
	public function testRecord() {
		$record = $this->Post->record(2);
		$this->assertEquals(2, $record['Post']['id']);

		$record = $this->Post->record(2, ['fields' => ['id', 'created']]);
		$this->assertEquals(2, count($record['Post']));

		$record = $this->Post->record(2, ['fields' => ['id', 'title', 'body'], 'contain' => ['Author']]);
		$this->assertEquals(3, count($record['Post']));
		$this->assertEquals(3, $record['Author']['id']);
	}

	/**
	 * ShimModelTest::testRecordFail()
	 *
	 * @return void
	 */
	public function testRecordFail() {
		$res = $this->Post->record(2222);
		$this->assertSame([], $res);
	}

	/**
	 * @return void
	 */
	public function testField() {
		Configure::write('Shim.warnAboutMissingContain', true);

		$is = $this->Post->field('title');
		$this->assertSame('First Post', $is);
	}

	/**
	 * @expectedException PHPUNIT_FRAMEWORK_ERROR_DEPRECATED
	 * @return void
	 */
	public function testFieldDeprecated() {
		Configure::write('Shim.warnAboutMissingContain', true);
		Configure::write('Shim.deprecateField', true);

		$this->Post->field('title');
	}

	/**
	 * @return void
	 */
	public function testFieldByConditions() {
		Configure::write('Shim.warnAboutMissingContain', true);

		$is = $this->Post->fieldByConditions('title', ['title LIKE' => 'S%']);
		$this->assertSame('Second Post', $is);

		$is = $this->Post->fieldByConditions('title', ['title LIKE' => '%'], ['order' => ['title' => 'DESC']]);
		$this->assertSame('Third Post', $is);
	}

	/**
	 * @return void
	 */
	public function testFieldImplicitId() {
		Configure::write('Shim.deprecateField', false);

		$this->Post->id = 2;
		$is = $this->Post->field('title');
		$this->assertSame('Second Post', $is);

		$is = $this->Post->fieldByConditions('Post.title', ['title LIKE' => '%'], ['order' => ['title' => 'DESC']]);
		$this->assertSame('Third Post', $is);
	}

	/**
	 * @expectedException PHPUNIT_FRAMEWORK_ERROR_DEPRECATED
	 * @return void
	 */
	public function testFieldImplicitIdWarning() {
		Configure::write('Shim.deprecateField', true);

		$this->Post->id = 2;
		$this->Post->field('title');
	}

	/**
	 * @expectedException PDOException
	 * @return void
	 */
	public function testFieldInvalid() {
		$this->Post->field('fooooo');
	}

	/**
	 * Testing missing contain warnings
	 *
	 * @return void
	 */
	public function testFind() {
		Configure::write('Shim.warnAboutMissingContain', true);

		$this->User->find('first');
	}

	/**
	 * Testing missing contain warnings
	 *
	 * @return void
	 */
	public function testFindRecursive() {
		Configure::write('Shim.warnAboutMissingContain', true);

		$this->User->recursive = 0;
		$this->User->find('first', ['contain' => []]);
	}

	/**
	 * Testing missing contain warnings
	 *
	 * @expectedException PHPUnit_Framework_Error_Warning
	 * @return void
	 */
	public function testFindWrongRecursive() {
		Configure::write('Shim.warnAboutMissingContain', true);

		$this->User->recursive = 0;
		$this->User->find('first');
	}

	/**
	 * Testing missing contain warnings
	 *
	 * @expectedException CakeException
	 * @return void
	 */
	public function testFindWrongRecursiveException() {
		Configure::write('Shim.warnAboutMissingContain', 'exception');

		$this->User->recursive = 0;
		$this->User->find('first');
	}

	/**
	 * ShimModelTest::testDeconstruct()
	 *
	 * @return void
	 */
	public function testDeconstruct() {
		$data = ['year' => '2010', 'month' => '10', 'day' => 11];
		$res = $this->User->deconstruct('User.dob', $data);
		$this->assertEquals('2010-10-11', $res);

		$res = $this->User->deconstruct('User.dob', $data, 'datetime');
		$this->assertEquals('2010-10-11 00:00:00', $res);
	}

	/**
	 * ShimModelTest::testUpdateAllJoinless()
	 *
	 * @return void
	 */
	public function testUpdateAllJoinless() {
		$db = ConnectionManager::getDataSource($this->Post->useDbConfig);
		$db->getLog();
		$postTable = $db->fullTableName($this->Post->table);
		$authorTable = $db->fullTableName($this->Post->Author->table);

		// Note that the $fields argument needs manual string escaping whereas the $conditions one doesn't!
		$result = $this->Post->updateAll(['title' => '"Foo"'], ['title !=' => 'Foo']);
		$this->assertTrue($result);

		$queries = $db->getLog();
		$expected = 'UPDATE ' . $postTable . ' AS `Post` LEFT JOIN ' . $authorTable . ' AS `Author` ON (`Post`.`author_id` = `Author`.`id`) SET `Post`.`title` = "Foo"  WHERE `title` != \'Foo\'';
		$this->assertSame($expected, $queries['log'][0]['query']);

		// Now joinless
		$result = $this->Post->updateAllJoinless(['title' => '"Foo"'], ['title !=' => 'Foo']);
		$this->assertTrue($result);

		$queries = $db->getLog();
		$expected = 'UPDATE ' . $postTable . ' AS `Post`  SET `Post`.`title` = "Foo"  WHERE `title` != \'Foo\'';
		$this->assertSame($expected, $queries['log'][0]['query']);
	}

	/**
	 * ShimModelTest::testDeleteAll()
	 *
	 * @return void
	 */
	public function testDeleteAll() {
		$db = ConnectionManager::getDataSource($this->Post->useDbConfig);
		$db->getLog();
		$postTable = $db->fullTableName($this->Post->table);
		$authorTable = $db->fullTableName($this->Post->Author->table);

		$result = $this->Post->deleteAll(['title !=' => 'Foo']);
		$this->assertTrue($result);

		$queries = $db->getLog();
		$expected = 'SELECT `Post`.`id` FROM ' . $postTable . ' AS `Post` LEFT JOIN ' . $authorTable . ' AS `Author` ON (`Post`.`author_id` = `Author`.`id`)  WHERE `title` != \'Foo\'  GROUP BY `Post`.`id`';
		$this->assertSame($expected, $queries['log'][0]['query']);

		$expected = 'DELETE `Post` FROM ' . $postTable . ' AS `Post`   WHERE `Post`.`id` IN';
		$this->assertContains($expected, $queries['log'][1]['query']);
	}

	/**
	 * ShimModelTest::testDeleteAllJoinless()
	 *
	 * @return void
	 */
	public function testDeleteAllJoinless() {
		// Now joinless
		$db = ConnectionManager::getDataSource($this->Post->useDbConfig);
		$db->getLog();
		$postTable = $db->fullTableName($this->Post->table);
		$authorTable = $db->fullTableName($this->Post->Author->table);

		$result = $this->Post->deleteAllJoinless(['title !=' => 'Foo']);
		$this->assertTrue($result);

		$queries = $db->getLog();
		$expected = 'SELECT `Post`.`id` FROM ' . $postTable . ' AS `Post`   WHERE `title` != \'Foo\'  GROUP BY `Post`.`id`';
		$this->assertSame($expected, $queries['log'][0]['query']);

		$expected = 'DELETE `Post` FROM ' . $postTable . ' AS `Post`   WHERE `Post`.`id` IN';
		$this->assertContains($expected, $queries['log'][1]['query']);
	}

	/**
	 * Test deleteAllRaw()
	 *
	 * @return void
	 */
	public function testDeleteAllRaw() {
		$result = $this->User->deleteAllRaw(['user !=' => 'foo', 'created <' => date('Y-m-d'), 'id >' => 1]);
		$this->assertTrue($result);
		$result = $this->User->getAffectedRows();
		$this->assertSame(3, $result);

		$result = $this->User->deleteAllRaw();
		$this->assertTrue($result);
		$result = $this->User->getAffectedRows();
		$this->assertSame(1, $result);
	}

	/**
	 * @return void
	 */
	public function testBehaviors() {
		$res = $this->User->behaviors();
		$this->assertInstanceOf('BehaviorCollection', $res);
	}

	/**
	 * @return void
	 */
	public function testBehaviorsAddRemove() {
		$result = $this->User->hasBehavior('Tree');
		$this->assertFalse($result);

		$res = $this->User->addBehavior('Tree');
		$this->assertTrue($res);

		$result = $this->User->hasBehavior('Tree');
		$this->assertTrue($result);

		$res = $this->User->removeBehavior('Tree');

		$result = $this->User->hasBehavior('Tree');
		$this->assertFalse($result);
	}

	/**
	 * Test that 2.x invalidates() can behave like 1.x invalidates()
	 * and that you are able to abort on single errors (similar to using last=>true)
	 *
	 * @return void
	 */
	public function testInvalidates() {
		$TestModel = new AppTestModel();

		$TestModel->validate = [
			'title' => [
				'tooShort' => [
					'rule' => ['minLength', 50],
					'last' => false
				],
				'onlyLetters' => ['rule' => '/^[a-z]+$/i']
			],
		];
		$data = [
			'title' => 'I am a short string'
		];
		$TestModel->create($data);
		$TestModel->invalidate('title', 'someCustomMessage');

		$result = $TestModel->validates();
		$this->assertFalse($result);

		$result = $TestModel->validationErrors;
		$expected = [
			'title' => ['someCustomMessage', 'tooShort', 'onlyLetters']
		];
		$this->assertEquals($expected, $result);
		$result = $TestModel->validationErrors;
		$this->assertEquals($expected, $result);

		// invalidate a field with 'last' => true and stop further validation for this field
		$TestModel->create($data);

		$TestModel->invalidate('title', 'someCustomMessage', true);

		$result = $TestModel->validates();
		$this->assertFalse($result);
		$result = $TestModel->validationErrors;
		$expected = [
			'title' => ['someCustomMessage']
		];
		$this->assertEquals($expected, $result);
		$result = $TestModel->validationErrors;
		$this->assertEquals($expected, $result);
	}

	/**
	 * ShimModelTest::testInvalidate()
	 *
	 * @return void
	 */
	public function testInvalidate() {
		$this->User->create();
		$this->User->invalidate('fieldx', sprintf('e %s f', 33));
		$res = $this->User->validationErrors;
		$this->out($res);
		$this->assertTrue(!empty($res));

		$this->User->create();
		$this->User->invalidate('Model.fieldy', sprintf('e %s f %s g', 33, 'xyz'));
		$res = $this->User->validationErrors;
		$this->out($res);
		$this->assertTrue(!empty($res) && $res['Model.fieldy'][0] === 'e 33 f xyz g');

		$this->User->create();
		$this->User->invalidate('fieldy', sprintf('e %s f %s g %s', true, 'xyz', 55));
		$res = $this->User->validationErrors;
		$this->out($res);
		$this->assertTrue(!empty($res) && $res['fieldy'][0] === 'e 1 f xyz g 55');

		$this->User->create();
		$this->User->invalidate('fieldy', ['valErrMandatoryField']);
		$res = $this->User->validationErrors;
		$this->out($res);
		$this->assertTrue(!empty($res));

		$this->User->create();
		$this->User->invalidate('fieldy', 'valErrMandatoryField');
		$res = $this->User->validationErrors;
		$this->out($res);
		$this->assertTrue(!empty($res));

		$this->User->create();
		$this->User->invalidate('fieldy', sprintf('a %s b %s c %s %s %s %s %s h %s', 1, 2, 3, 4, 5, 6, 7, 8));
		$res = $this->User->validationErrors;
		$this->out($res);
		$this->assertTrue(!empty($res) && $res['fieldy'][0] === 'a 1 b 2 c 3 4 5 6 7 h 8');
	}

}

class ShimAppModelPost extends ShimModel {

	public $name = 'Post';

	public $alias = 'Post';

	public $belongsTo = 'Author';

}

class ShimAppModelUser extends ShimModel {

	public $name = 'User';

	public $alias = 'User';

}

class AppTestModel extends ShimModel {

	public $useTable = false;

	protected $_schema = [
		'id' => [
			'type' => 'string',
			'null' => false,
			'default' => '',
			'length' => 36,
			'key' => 'primary',
			'collate' => 'utf8_unicode_ci',
			'charset' => 'utf8',
		],
		'foreign_id' => [
			'type' => 'integer',
			'null' => false,
			'default' => '0',
			'length' => 10,
		],
	];

	public static function x() {
		return ['1' => 'x', '2' => 'y', '3' => 'z'];
	}

}
