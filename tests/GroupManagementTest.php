<?php

use KlinkDMS\User;
use KlinkDMS\Group;
use Laracasts\TestDummy\DbTestCase;

class GroupManagementTest extends DbTestCase {

	private $service = null;

	private $user = null;

	public function setUp()
	{

		parent::setUp();

		$this->service = $this->app->make('Klink\DmsDocuments\DocumentsService');

		$this->seed('UserTableSeeder');

		$this->user = User::findByEmail('admin@klink.local');

	}

	/**
	 * |- f
	 * |  |- c
	 * |  |- g
	 * 
	 * |- a
	 * |  |- b
	 * |  |- c
	 * |  |  |- d
	 * |  |  |- e
	 */
	private function createTestGroupTree($user, $service)
	{
		$a = $service->createGroup($user, 'a');

		$b = $service->createGroup($user, 'b', null, $a);

		$c = $service->createGroup($user, 'c', null, $a);

		$d = $service->createGroup($user, 'd', null, $c);

		$e = $service->createGroup($user, 'e', null, $c);

		$f = $service->createGroup($user, 'f');

		$c2 = $service->createGroup($user, 'c', null, $f);

		$g = $service->createGroup($user, 'g', null, $f);

		return compact('a', 'b', 'c', 'd', 'e', 'f', 'c2', 'g');
	}

	public function treeExpectedString()
	{
		return  'f c g a b c d e';
	}

	public function treeToString()
	{
		$tree = Group::getTree();

		$toString = '';

		foreach ($tree as $first_level) {
			$toString .= $first_level->name . ' ';

			if($first_level->hasChildren()){
				foreach ($first_level->getChildren() as $second_level) {

					$toString .= $second_level->name . ' ';

					if($second_level->hasChildren()){
						foreach ($second_level->getChildren() as $third_level) {

							$toString .= $third_level->name . ' ';
							
						}
					}
					
				}
			}
		}

		return trim($toString);
	}

	private function echoTree($childs = null, $level = 0){

		// $tree = is_null($childs) ? Group::getRoots() : $childs;

		// if($level==0){
		// 	echo PHP_EOL;
		// }

		// foreach ($tree as $first_level) {

		// 	echo str_pad('', $level*3, " ", STR_PAD_RIGHT) . '|- ' . $first_level->name . PHP_EOL;

		// 	if($first_level->hasChildren()){
		// 		$this->echoTree($first_level->getChildren(), $level+1);
				
		// 	}
			
		// }

		// if($level==0){
		// 	echo PHP_EOL;
		// }
		
	}


	/**
	 * Test group creation
	 *
	 * @return void
	 */
	public function testGroupCreation()
	{

		/*
			  A            F
			B   C        C   G
			   D  E
		*/

		$tree = $this->createTestGroupTree($this->user, $this->service);

		$this->assertEquals(8, Group::all()->count());

		$a = $tree['a'];
		$b = $tree['b'];
		$c = $tree['c'];
		$d = $tree['d'];
		$e = $tree['e'];
		$f = $tree['f'];
		$g = $tree['g'];
		$c2 = $tree['c2'];

		$a = $tree['a'];


		$this->assertTrue($a->isRoot());

		$this->assertEquals(2, $a->getChildren()->count());
		$this->assertEquals(4, $a->countDescendants());

		// $b = $tree['b'];
		// $c = $tree['c'];
		// $d = $tree['d'];
		// $e = $tree['e'];
		
		$this->assertTrue($f->isRoot());
		$this->assertEquals(2, $f->getChildren()->count());
		$this->assertEquals(2, $f->countDescendants());


		// $g = $tree['g'];
		// $c2 = $tree['c2'];


		$this->assertEquals($this->treeExpectedString(), $this->treeToString());


	}

	public function testGroupUpdate()
	{

		$a = $this->service->createGroup($this->user, 'a');

		$b = $this->service->createGroup($this->user, 'b', null, $a);



		// change A name to Autumn

		$a = $this->service->updateGroup($this->user, $a, array('name' => 'Autumn'));

		$this->assertEquals('Autumn', $a->name);


		// change B name to Benny
		$b = $this->service->updateGroup($this->user, $b, array('name' => 'Benny'));

		$this->assertEquals('Benny', $b->name);

	}

	/**
	 * @expectedException KlinkDMS\Exceptions\ForbiddenException
	 */
	public function testGroupUpdateForbidden($value='')
	{
		$a = $this->service->createGroup($this->user, 'a');

		$b = $this->service->createGroup($this->user, 'b', null, $a);

		$c = $this->service->createGroup($this->user, 'c', null, $a);


		$b = $this->service->updateGroup($this->user, $b, array('name' => 'c'));
	}

	public function testGroupCreationFromFolder()
	{
		$group = $this->service->createGroupsFromFolderPath($this->user, '/pretty/path/for/example/');

		$this->assertEquals('example', $group->name);

		$this->assertEquals('for', $group->getParent()->name);

		$this->assertEquals('path', $group->getParent()->getParent()->name);

		$this->assertEquals('pretty', $group->getParent()->getParent()->getParent()->name);


		$group = $this->service->createGroupsFromFolderPath($this->user, '/pretty/path/for/example/');

	}

	public function testGroupCreationFromFolderTwice()
	{
		$group = $this->service->createGroupsFromFolderPath($this->user, '/pretty/path/for/example/');
		$group = $this->service->createGroupsFromFolderPath($this->user, '/pretty/path/for/example/');

		$this->echoTree();

		$this->assertEquals(1, Group::getRoots()->count(), 'more than one root');	

	}

	/**
	 * Tests for simple group and group with subgroups copy without the need to merge the groups
	 */
	public function testGroupCopyBaseCases()
	{

		$tree = $this->createTestGroupTree($this->user, $this->service);


		$this->echoTree();


		$f = $tree['f'];
		$b = $tree['b'];
		$g = $tree['g'];

		// copy B as new root
		$this->service->copyGroup($this->user, $b, null);

		$b->fresh();

		$this->echoTree();

		$this->assertTrue(Group::getRoots()->contains('name', 'b'));

		$this->assertEquals(2, Group::byName('b')->count());

		
		$this->service->copyGroup($this->user, $b, $g);

		$g->fresh();

		$this->assertTrue($g->getChildren()->contains('name', 'b'));

		$this->assertEquals(3, Group::byName('b')->count());

		$this->echoTree();

		$bRoot = Group::getRoots()->where('name', 'b')->first();

		$this->service->copyGroup($this->user, $f, $bRoot);

		$f->fresh();

		$this->echoTree();

		$this->assertTrue($bRoot->getChildren()->contains('name', 'f'), 'Children of B contains F');
		$this->assertEquals(1, $bRoot->countChildren(), 'Final B descendants count');

	}

	public function testGroupCopyAdvancedCases()
	{

		$tree = $this->createTestGroupTree($this->user, $this->service);


		$this->echoTree();


		$f = $tree['f'];
		$c = $tree['c'];

		// copy C under the F that has already a C child for the same this->user
		$this->service->copyGroup($this->user, $c, $f, true);

		$f->fresh();

		$this->echoTree();

		$this->assertTrue($f->getChildren()->contains('name', 'c'), 'Children of F contains C');
		
		$this->assertEquals(2, $f->getFirstChild()->countChildren(), 'Final F descendants count');

	}

	/**
	 * Test group move to new root and as sub group. Do not test the merge with existing groups
	 */
	public function testGroupMovesBaseCases()
	{

		$tree = $this->createTestGroupTree($this->user, $this->service);


		$this->echoTree();


		$b = $tree['b'];
		$e = $tree['e'];
		$d = $tree['d'];
		$g = $tree['g'];
		$c = $tree['c'];
		$f = $tree['f'];


		// move B as new root
		$this->service->moveGroup($this->user, $b, null);

		$b->fresh();

		$this->assertTrue($b->isRoot());

		$this->echoTree();

		// Move E under B

		$this->service->moveGroup($this->user, $e, $b);

		$e->fresh();

		$this->echoTree();

		// dd($e->getParent());

		$this->assertEquals('b', $e->getParent()->name);
		$this->assertTrue($b->getChildren()->contains('name', 'e'));

		$original_children_count = $f->countChildren();

		$this->service->moveGroup($this->user, $f, $e);

		$f->fresh();

		$this->echoTree();

		$this->assertEquals('e', $f->getParent()->name);
		$this->assertEquals($original_children_count, $f->countChildren());


	}

	/**
	 * Test group move that needs the merge option
	 */
	public function testGroupMovesAdvancedCases()
	{

		$tree = $this->createTestGroupTree($this->user, $this->service);

		$a = $tree['a'];
		$c = $tree['c'];
		$f = $tree['f'];

		// copy C under the F that has already a C child for the same this->user, so now we have the same tree in two different places
		$this->service->copyGroup($this->user, $c, $f, true);

		$f->fresh();

		$this->echoTree();

		// Move C under F (merge existing)

		$this->service->moveGroup($this->user, $c, $f, true);

		$a->fresh();
		$f->fresh();

		$this->echoTree();

		$this->assertEquals(2, $f->getFirstChild()->countChildren(), 'Final F descendants count');
		$this->assertTrue($f->getChildren()->contains('name', 'c'));
		$this->assertEquals(1, $a->countChildren());


	}

	/**
	 * Test the raise of forbidden exception in case of existing subtree in the new position
	 * @expectedException KlinkDMS\Exceptions\ForbiddenException
	 */
	public function testGroupMoveForbidden()
	{

		$tree = $this->createTestGroupTree($this->user, $this->service);
		
		$f = $tree['f'];
		$c = $tree['c'];

		$this->service->moveGroup($this->user, $c, $f);



	}

	/**
	 * @expectedException KlinkDMS\Exceptions\ForbiddenException
	 */
	public function testGroupCopyForbidden()
	{

		$tree = $this->createTestGroupTree($this->user, $this->service);
		
		$f = $tree['f'];
		$c = $tree['c'];

		$this->service->copyGroup($this->user, $c, $f);

	}


	public function testCanCopyOrMoveGroup()
	{

		$tree = $this->createTestGroupTree($this->user, $this->service);
		
		$f = $tree['f'];
		$c = $tree['c'];

		$val = $this->service->canCopyOrMoveGroup($this->user, $c, $f);

		$this->assertFalse($val);

	}


}