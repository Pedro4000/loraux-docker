<?php

namespace App\Tests;

use App\Entity\{DiscogsVideo, Artist, Release, Label};
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    public function testIsTrue(): void
    {
        $label = new Label();

        $releaseA = new Release();
        $releaseB = new Release();
        $releaseA->setName('a');
        $releaseB->setName('b');

        $discogsVideoA = new DiscogsVideo();
        $discogsVideoB = new DiscogsVideo();
        $discogsVideoA->setUrl('a');
        $discogsVideoB->setUrl('b');

        $label->addRelease($releaseA)->addRelease($releaseB)
              ->addDiscogsVideo($discogsVideoA)->addDiscogsVideo($discogsVideoB);

        $this->assertTrue($label->getReleases() == new ArrayCollection([$releaseA, $releaseB]));
        $this->assertTrue($label->getDiscogsVideos() == new ArrayCollection([$discogsVideoA, $discogsVideoB]));

    }

    public function testIsFalse(): void
    {
        $label = new Label();

        $releaseA = new Release();
        $releaseB = new Release();
        $releaseC = new Release();
        $releaseD = new Release();
        $releaseA->setName('a');
        $releaseB->setName('b');
        $releaseC->setName('c');
        $releaseD->setName('d');

        $discogsVideoA = new DiscogsVideo();
        $discogsVideoB = new DiscogsVideo();
        $discogsVideoC = new DiscogsVideo();
        $discogsVideoD = new DiscogsVideo();
        $discogsVideoA->setUrl('a');
        $discogsVideoB->setUrl('b');
        $discogsVideoC->setUrl('C');
        $discogsVideoD->setUrl('d');

        $label->addRelease($releaseA)->addRelease($releaseB)
              ->addDiscogsVideo($discogsVideoA)->addDiscogsVideo($discogsVideoB);

        $this->assertFalse($label->getReleases() == new ArrayCollection([$releaseC, $releaseD]));
        $this->assertFalse($label->getDiscogsVideos() == new ArrayCollection([$discogsVideoC, $discogsVideoD]));
    }
    
    public function testIsEmpty(): void
    {
        $label = new Label();

        $releaseA = new Release();
        $releaseA->setName('a');

        $discogsVideoA = new DiscogsVideo();
        $discogsVideoA->setUrl('a');

        $label->addRelease($releaseA)->removeRelease($releaseA)
              ->addDiscogsVideo($discogsVideoA)->removeDiscogsVideo($discogsVideoA);

        $this->assertEmpty($label->getReleases());
        $this->assertEmpty($label->getDiscogsVideos());
    }
}
