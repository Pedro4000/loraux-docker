<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\User;

class UserTest extends TestCase
{
    public function testIsTrue(): void
    {
        $user = new User();
        $now = new \DateTimeImmutable('now');

        $user->setFirstName('pierre')
             ->setFamilyName('brickley')
             ->setEmailAddress('a@a.fr')
             ->setPassword('gngngng')
             ->setBirthDate($now)
             ->setSex('blorp')
             ->setIsMailAddressVerified(true);

        $this->assertTrue($user->getFirstName() == 'pierre');
        $this->assertTrue($user->getFamilyName() == 'brickley');
        $this->assertTrue($user->getEmailAddress() == 'a@a.fr');
        $this->assertTrue($user->getPassword() == 'gngngng');
        $this->assertTrue($user->getBirthDate() == $now);
        $this->assertTrue($user->getSex() == 'blorp');
        $this->assertTrue($user->getIsMailAddressVerified() == true);
    }

    public function testIsFalse(): void
    {
        $user = new User();
        $now = new \DateTimeImmutable('now');

        $user->setFirstName('pierre')
             ->setFamilyName('brickley')
             ->setEmailAddress('a@a.fr')
             ->setPassword('gngngng')
             ->setBirthDate($now)
             ->setSex('blorp')
             ->setIsMailAddressVerified(true);

        $this->assertFalse($user->getFirstName() == 'moi');
        $this->assertFalse($user->getFamilyName() == 'rosabrunetto');
        $this->assertFalse($user->getEmailAddress() == 'b@b.fr');
        $this->assertFalse($user->getPassword() == 'blblblbl');
        $this->assertFalse($user->getBirthDate() == new \DateTimeImmutable());
        $this->assertFalse($user->getSex() == 'qwink');
        $this->assertFalse($user->getIsMailAddressVerified() == false);
    }
    
    public function testIsEmpty(): void
    {
        $user = new User();
        $now = new \DateTimeImmutable('now');

        $this->assertFalse($user->getFirstName()); 
        $this->assertFalse($user->getFamilyName());
        $this->assertFalse($user->getEmailAddress());
        $this->assertFalse($user->getPassword());
        $this->assertFalse($user->getBirthDate());
        $this->assertFalse($user->getSex());
        $this->assertFalse($user->getIsMailAddressVerified());
    }
}
