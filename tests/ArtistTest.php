<?php

namespace App\Tests;

use App\Entity\{Artist, Release, Track, DiscogsVideo};
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ArtistTest extends TestCase
{
    public function testIsTrue(): void
    {
        $artist = new Artist();
        
        $releaseA = new Release();
        $releaseB = new Release();
        $releaseA->setDiscogsId(1);
        $releaseB->setDiscogsId(2);

        $trackA = new Track();
        $trackB = new Track();
        $trackA->setName('un');
        $trackB->setName('deux');
        $discogsVideoA = new DiscogsVideo();
        $discogsVideoB = new DiscogsVideo();
        $discogsVideoA->setUrl('un');
        $discogsVideoB->setUrl('deux');
        
        $artist->addRelease($releaseA)->addRelease($releaseB)
               ->addTrack($trackA)->addTrack($trackB)
               ->addDiscogsVideo($discogsVideoA)->addDiscogsVideo($discogsVideoB);       
        
        $this->assertTrue($artist->getReleases() == new ArrayCollection([$releaseA, $releaseB]));
        $this->assertTrue($artist->getTracks() == new ArrayCollection([$trackA, $trackB]));
        $this->assertTrue($artist->getTracks() == new ArrayCollection([$trackA, $trackB]));
    }

    public function testIsFalse(): void 
    {
        $artist = new Artist(); 

        $releaseA = new Release();
        $releaseB = new Release();
        $releaseC = new Release();
        $releaseD = new Release();
        $releaseA->setDiscogsId(1);
        $releaseB->setDiscogsId(2);
        $releaseC->setDiscogsId(3);
        $releaseD->setDiscogsId(4);

        $trackA = new Track();
        $trackB = new Track();
        $trackC = new Track();
        $trackD = new Track();
        $trackA->setName('un');
        $trackB->setName('deux');
        $trackC->setName('trois');
        $trackD->setName('quatre');

        $discogsVideoA = new DiscogsVideo();
        $discogsVideoB = new DiscogsVideo();
        $discogsVideoC = new DiscogsVideo();
        $discogsVideoD = new DiscogsVideo();
        $discogsVideoA->setUrl('un');
        $discogsVideoB->setUrl('deux');
        $discogsVideoC->setUrl('trois');
        $discogsVideoD->setUrl('quatre');

        $artist->addRelease($releaseA)->addRelease($releaseB)
               ->addTrack($trackA)->addTrack($trackB)
               ->addDiscogsVideo($discogsVideoA)->addDiscogsVideo($discogsVideoB);      

        $this->assertFalse($artist->getReleases() == new ArrayCollection([$releaseC, $releaseD]));
        $this->assertFalse($artist->getTracks() == new ArrayCollection([$trackC, $trackD]));
        $this->assertFalse($artist->getTracks() == new ArrayCollection([$trackC, $trackD]));
    }

    public function testIsEmpty(): void
    {
        $artist = new Artist(); 

        $releaseA = new Release();
        $releaseA->setDiscogsId(1);

        $trackA = new Track();
        $trackA->setName('un');

        $discogsVideoA = new DiscogsVideo();
        $discogsVideoA->setUrl('un');

        $artist->addRelease($releaseA)->removeRelease($releaseA)
               ->addTrack($trackA)->removeTrack($trackA)
               ->addDiscogsVideo($discogsVideoA)->removeDiscogsVideo($discogsVideoA); 

        $this->assertEmpty($artist->getReleases());
        $this->assertEmpty($artist->getReleases());
        $this->assertEmpty($artist->getReleases());
    }
}
