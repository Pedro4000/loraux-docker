<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\DiscogsClass;

class DiscogsClassTest extends TestCase
{
    public function testIsTrue(): void
    {
        $discogsClass = new DiscogsClass();
        $now = new \DateTimeImmutable('now');
        
        $discogsClass->setName('un')
                     ->setDiscogsId(1)
                     ->setFullyScrapped(false)
                     ->setCreatedAt($now);

        $this->assertTrue($discogsClass->getName() == 'un');
        $this->assertTrue($discogsClass->getDiscogsId() === 1);
        $this->assertTrue($discogsClass->isFullyScrapped() == false);
        $this->assertTrue($discogsClass->getCreatedAt() == $now);

    }

    public function testIsfalse(): void
    {
        $discogsClass = new DiscogsClass();
        $now = new \DateTimeImmutable('now');
        
        $discogsClass->setName('un')
                     ->setDiscogsId(1)
                     ->setFullyScrapped(false)
                     ->setCreatedAt($now);

        $this->assertFalse($discogsClass->getName() == 'deux');
        $this->assertFalse($discogsClass->getDiscogsId() === 2);
        $this->assertFalse($discogsClass->isFullyScrapped() == true);
        $this->assertFalse($discogsClass->getCreatedAt() == new \DateTimeImmutable());
    }

    public function testIsEmpty(): void
    {
        $discogsClass = new DiscogsClass();

        $this->assertEmpty($discogsClass->getName());
        $this->assertEmpty($discogsClass->getDiscogsId());
        $this->assertEmpty($discogsClass->isFullyScrapped());
        $this->assertEmpty($discogsClass->getCreatedAt());
    }
}
