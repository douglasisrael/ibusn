<?php

namespace SocialNetwork\API\Provider;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase,
    Symfony\Component\Yaml\Parser,
    SocialNetwork\API\Provider\DbRoleProvider,
    SocialNetwork\API\Provider\Criteria;

class DbRoleProviderTest extends WebTestCase
{
    private $rProvider;
    private $role;
    private $criteria;

    protected function setUp()
    {
        $yaml = new Parser();
        $map = $yaml->parse(file_get_contents(__DIR__ . '/../../../../../app/config/parameters.yml'));

        $client = static::createClient();
        $this->rProvider = new DbRoleProvider( $client->getContainer(), $map['parameters']['provider_role_map'] );

        $this->role = array(
            'name' => 'ROLETESTECASE',
            'uid' => 'ROLETESTECASE_UID',
            'description' => 'ROLETESTECASE',
            'members' => array( 'ROLETESTECASE' )
        );

        $this->criteria = new Criteria();
    }

    protected function tearDown()
    {
        $this->rProvider = null;
    }

    function getRole($name){

        $this->criteria->add('name', $name);
        $role = $this->rProvider->find( $this->criteria );
        return $role[0];
    }

    public function testCreate()
    {
        $role = $this->rProvider->create(
            $this->role['uid'],
            $this->role['name'],
            $this->role['description'],
            $this->role['members']
        );

        $this->assertInternalType('int', $role);
    }

    public function testGet()
    {
        $role = $this->getRole('ROLETESTECASE');

        $role = $this->rProvider->get( $role['id'] );
        $this->assertEquals($role['name'], $this->role['name']);
        $this->assertEquals($role['description'], $this->role['description']);
        $this->assertEquals($role['uid'], $this->role['uid']);
        $this->assertCount(1, $this->role['members']);
    }

    public function testGetByUid()
    {
        $role = $this->getRole('ROLETESTECASE');

        $role = $this->rProvider->getByUid( $role['uid'] );
        $this->assertEquals($role['name'], $this->role['name']);
        $this->assertEquals($role['description'], $this->role['description']);
        $this->assertEquals($role['uid'], $this->role['uid']);
        $this->assertCount(1, $this->role['members']);
    }

    public function testFind()
    {
        $this->criteria->add('name','ROLETESTECASE');
        $role = $this->rProvider->find( $this->criteria );

        $this->assertCount(1, $role);
        $role = $role[0];

        $this->assertEquals($role['name'], $this->role['name']);
        $this->assertEquals($role['description'], $this->role['description']);
        $this->assertCount(1, $this->role['members']);
    }

    public function testUpdate()
    {
        $role = $this->getRole('ROLETESTECASE');

        $this->role['name'] = $this->role['name'].'ALTER';
        $this->role['uid'] = $this->role['uid'].'ALTER';
        $this->role['description'] = $this->role['description'].'ALTER';
        $this->role['members'] = array( 'ROLETESTECASE', 'ROLETESTECASE2' );

        $alter = $this->rProvider->update(
            $role['id'],
            $this->role['uid'],
            $this->role['name'],
            $this->role['description'],
            $this->role['members']
        );

        $this->assertTrue( $alter );
    }

    public function testSetUserRole()
    {
        $role = $this->getRole('ROLETESTECASEALTER');
        $add  = $this->rProvider->setUserRole('USERTESTECASEADDTOROLE', $role['uid']);

        $this->assertTrue( $add );
    }

    public function testGetUserRoles()
    {
        $roles  = $this->rProvider->getUserRoles('USERTESTECASEADDTOROLE');
        $this->assertCount(1, $roles);
    }

    public function testDeleteUserRole()
    {
        $role = $this->getRole('ROLETESTECASEALTER');
        $remove  = $this->rProvider->deleteUserRole('USERTESTECASEADDTOROLE', $role['uid']);

        $this->assertTrue( $remove );
    }

    public function testDelete()
    {
        $role = $this->getRole('ROLETESTECASEALTER');
        $role = $this->rProvider->delete( $role['id'] );

        $this->assertInternalType('bool', $role);
    }
}