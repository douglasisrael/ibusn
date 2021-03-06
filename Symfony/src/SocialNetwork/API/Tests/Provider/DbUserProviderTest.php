<?php

namespace SocialNetwork\API\Tests\Provider;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase,
    SocialNetwork\API\Provider\DbUserProvider,
    SocialNetwork\API\Provider\Criteria;

class DbUserProviderTest extends WebTestCase
{
    private $uProvider;
    private $user;
    private $criteria;

    protected function setUp()
    {
        $client = static::createClient();
        $this->uProvider = new DbUserProvider( $client->getContainer(), array() );

        $this->user = array(
            'name' => 'USERTESTECASE',
            'uid' => 'USERTESTECASE',
            'password' => 'USERTESTECASE'
        );

        $this->criteria = new Criteria();
    }

    protected function tearDown()
    {
        $this->uProvider = null;
    }

    function getUser($uid){

        $this->criteria->add('uid', $uid);
        $user = $this->uProvider->find( $this->criteria );
        return $user[0];
    }

    public function testCreate()
    {
        $this->user['id'] = $this->uProvider->create(
            $this->user['name'],
            $this->user['uid'],
            $this->user['password']
        );

        $this->assertInternalType('int', $this->user['id']);
    }

    public function testFind()
    {
        $this->criteria->add('uid','USERTESTECASE');
        $user = $this->uProvider->find( $this->criteria );

        $this->assertCount(1, $user);
        $user = $user[0];

        $this->assertEquals($user['name'], $this->user['name']);
        $this->assertEquals($user['uid'], $this->user['uid']);
        $this->assertEquals($user['password'], '{md5}' . base64_encode(pack("H*",md5($this->user['password']))));
    }

    public function testGet()
    {
        $user = $this->getUser('USERTESTECASE');

        $user = $this->uProvider->get( $user['id'] );
        $this->assertEquals($user['name'], $this->user['name']);
        $this->assertEquals($user['uid'], $this->user['uid']);
        $this->assertEquals($user['password'],'{md5}' . base64_encode(pack("H*",md5($this->user['password']))));
    }

    public function testGetByUid()
    {
        $user = $this->uProvider->getByUid('USERTESTECASE');

        $this->assertEquals($user['name'], $this->user['name']);
        $this->assertEquals($user['uid'], $this->user['uid']);
        $this->assertEquals($user['password'], '{md5}' . base64_encode(pack("H*",md5($this->user['password']))));
    }

    public function testUpdate()
    {
        $user = $this->getUser('USERTESTECASE');

        $this->user['name'] = $this->user['name'].'ALTER';
        $this->user['uid'] = $this->user['uid'].'ALTER';
        $this->user['password'] = $this->user['password'].'ALTER';

        $alter = $this->uProvider->update(
            $user['id'],
            $this->user['name'],
            $this->user['uid'],
            $this->user['password']
        );

        $this->assertTrue( $alter );

        $user = $this->uProvider->get( $user['id'] );

        $this->assertEquals($user['name'], $this->user['name']);
        $this->assertEquals($user['uid'], $this->user['uid']);
        $this->assertEquals($user['password'],  '{md5}' . base64_encode(pack("H*",md5($this->user['password']))) );
    }

    public function testDelete()
    {
        $user = $this->getUser('USERTESTECASEALTER');
        $user = $this->uProvider->delete( $user['id'] );

        $this->assertInternalType('bool', $user);
    }
}