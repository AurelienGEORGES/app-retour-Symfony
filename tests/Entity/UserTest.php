<?php
namespace App\Tests\Entity;

use App\Entity\User;
class UserTest extends \PHPUnit\Framework\TestCase
{
    public function testUser()
    {
        $username = 'user1';
        $roles = ['ROLE_USER'];
        $password = 'secret123';
        $name = 'user'; 
        $user = new User();
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setPassword($password);
        $user->setName($name);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($roles, $user->getRoles());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($name, $user->getName());
    }
}