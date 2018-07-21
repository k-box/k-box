<?php

use KBox\Group;

use Tests\BrowserKitTestCase;
use Tests\Concerns\ClearDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GroupManagementTest extends BrowserKitTestCase
{
    use ClearDatabase;
    use DatabaseTransactions;

    private $service = null;

    private $user = null;
    
    public function setUp()
    {
        parent::setUp();

        $this->service = $this->app->make('Klink\DmsDocuments\DocumentsService');
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
            $toString .= $first_level->name.' ';

            if ($first_level->hasChildren()) {
                foreach ($first_level->getChildren() as $second_level) {
                    $toString .= $second_level->name.' ';

                    if ($second_level->hasChildren()) {
                        foreach ($second_level->getChildren() as $third_level) {
                            $toString .= $third_level->name.' ';
                        }
                    }
                }
            }
        }

        return trim($toString);
    }

    private function echoTree($childs = null, $level = 0)
    {

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
        $this->clearDatabase();
        /*
              A            F
            B   C        C   G
               D  E
        */
        $user = $this->createAdminUser();
        $tree = $this->createTestGroupTree($user, $this->service);

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
        $user = $this->createAdminUser();
        
        $a = $this->service->createGroup($user, 'a');

        $b = $this->service->createGroup($user, 'b', null, $a);

        // change A name to Autumn

        $a = $this->service->updateGroup($user, $a, ['name' => 'Autumn']);

        $this->assertEquals('Autumn', $a->name);

        // change B name to Benny
        $b = $this->service->updateGroup($user, $b, ['name' => 'Benny']);

        $this->assertEquals('Benny', $b->name);
    }

    /**
     * @expectedException KBox\Exceptions\ForbiddenException
     */
    public function testGroupUpdateForbidden($value='')
    {
        $user = $this->createAdminUser();
        
        $a = $this->service->createGroup($user, 'a');

        $b = $this->service->createGroup($user, 'b', null, $a);

        $c = $this->service->createGroup($user, 'c', null, $a);

        $b = $this->service->updateGroup($user, $b, ['name' => 'c']);
    }

    public function testGroupCreationFromFolder()
    {
        $user = $this->createAdminUser();
        
        $group = $this->service->createGroupsFromFolderPath($user, '/pretty/path/for/example/');

        $this->assertEquals('example', $group->name);

        $this->assertEquals('for', $group->getParent()->name);

        $this->assertEquals('path', $group->getParent()->getParent()->name);

        $this->assertEquals('pretty', $group->getParent()->getParent()->getParent()->name);
    }
    
    public function testGroupCreationFromFolderWithSingleFolder()
    {
        $user = $this->createAdminUser();
        
        $group = $this->service->createGroupsFromFolderPath($user, 'example/');

        $this->assertEquals('example', $group->name);
    }

    public function testGroupCreationFromFolderTwice()
    {
        $this->clearDatabase();

        $user = $this->createAdminUser();
        
        $group = $this->service->createGroupsFromFolderPath($user, '/pretty/path/for/example/');
        $group = $this->service->createGroupsFromFolderPath($user, '/pretty/path/for/example/');

        $this->echoTree();

        $this->assertEquals(1, Group::getRoots()->count(), 'more than one root');
    }

    /**
     * Tests for simple group and group with subgroups copy without the need to merge the groups
     */
    public function testGroupCopyBaseCases()
    {
        $this->clearDatabase();
        
        $user = $this->createAdminUser();

        $tree = $this->createTestGroupTree($user, $this->service);

        $this->echoTree();

        $f = $tree['f'];
        $b = $tree['b'];
        $g = $tree['g'];

        // copy B as new root
        $this->service->copyGroup($user, $b, null);

        $b->fresh();

        $this->echoTree();

        $this->assertTrue(Group::getRoots()->contains('name', 'b'));

        $this->assertEquals(2, Group::byName('b')->count());

        $this->service->copyGroup($user, $b, $g);

        $g->fresh();

        $this->assertTrue($g->getChildren()->contains('name', 'b'));

        $this->assertEquals(3, Group::byName('b')->count());

        $this->echoTree();

        $bRoot = Group::getRoots()->where('name', 'b')->first();

        $this->service->copyGroup($user, $f, $bRoot);

        $f->fresh();

        $this->echoTree();

        $this->assertTrue($bRoot->getChildren()->contains('name', 'f'), 'Children of B contains F');
        $this->assertEquals(1, $bRoot->countChildren(), 'Final B descendants count');
    }

    public function testGroupCopyAdvancedCases()
    {
        $user = $this->createAdminUser();
        
        $tree = $this->createTestGroupTree($user, $this->service);

        $this->echoTree();

        $f = $tree['f'];
        $c = $tree['c'];

        // copy C under the F that has already a C child for the same this->user
        $this->service->copyGroup($user, $c, $f, true);

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
        $user = $this->createAdminUser();
        
        $tree = $this->createTestGroupTree($user, $this->service);

        $this->echoTree();

        $b = $tree['b'];
        $e = $tree['e'];
        $d = $tree['d'];
        $g = $tree['g'];
        $c = $tree['c'];
        $f = $tree['f'];

        // move B as new root
        $this->service->moveGroup($user, $b, null);

        $b->fresh();

        $this->assertTrue($b->isRoot());

        $this->echoTree();

        // Move E under B

        $this->service->moveGroup($user, $e, $b);

        $e->fresh();

        $this->echoTree();

        $this->assertEquals('b', $e->getParent()->name);
        $this->assertTrue($b->getChildren()->contains('name', 'e'));

        $original_children_count = $f->countChildren();

        $this->service->moveGroup($user, $f, $e);

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
        $user = $this->createAdminUser();

        $tree = $this->createTestGroupTree($user, $this->service);

        $a = $tree['a'];
        $c = $tree['c'];
        $f = $tree['f'];

        // copy C under the F that has already a C child for the same this->user, so now we have the same tree in two different places
        $this->service->copyGroup($user, $c, $f, true);

        $f->fresh();

        $this->echoTree();

        // Move C under F (merge existing)

        $this->service->moveGroup($user, $c, $f, true);

        $a->fresh();
        $f->fresh();

        $this->echoTree();

        $this->assertEquals(2, $f->getFirstChild()->countChildren(), 'Final F descendants count');
        $this->assertTrue($f->getChildren()->contains('name', 'c'));
        $this->assertEquals(1, $a->countChildren());
    }

    /**
     * Test the raise of forbidden exception in case of existing subtree in the new position
     * @expectedException KBox\Exceptions\ForbiddenException
     */
    public function testGroupMoveForbidden()
    {
        $user = $this->createAdminUser();

        $tree = $this->createTestGroupTree($user, $this->service);
        
        $f = $tree['f'];
        $c = $tree['c'];

        $this->service->moveGroup($user, $c, $f);
    }

    /**
     * @expectedException KBox\Exceptions\ForbiddenException
     */
    public function testGroupCopyForbidden()
    {
        $user = $this->createAdminUser();

        $tree = $this->createTestGroupTree($user, $this->service);
        
        $f = $tree['f'];
        $c = $tree['c'];

        $this->service->copyGroup($user, $c, $f);
    }

    public function testCanCopyOrMoveGroup()
    {
        $user = $this->createAdminUser();

        $tree = $this->createTestGroupTree($user, $this->service);
        
        $f = $tree['f'];
        $c = $tree['c'];

        $val = $this->service->canCopyOrMoveGroup($user, $c, $f);

        $this->assertFalse($val);
    }
}
