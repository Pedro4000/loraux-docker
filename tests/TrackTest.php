<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\{Track, Artist, Release, Label};
use Doctrine\Common\Collections\ArrayCollection;

class TrackTest extends TestCase
{
    public function testIsTrue(): void
    {
        $track = new Track();

        $releaseA = new Release();
        $releaseA->setName('a');

        $artistA = new Artist();
        $artistA->setName('a');

        $track->setName('bla');
        $track->addArtist($artistA)
              ->setRelease($releaseA);

        $this->assertTrue($track->getName() == ('bla'));
        $this->assertTrue($track->getArtists() == new ArrayCollection([$artistA]));
        $this->assertTrue($track->getRelease() == $releaseA);
    }


    public function testIsFalse(): void
    {
        $track = new Track();

        $releaseA = new Release();
        $releaseA->setName('a');
        $releaseB = new Release();

        $artistA = new Artist();
        $artistA->setName('a');
        $artistB = new Artist();

        $track->setName('bla');
        $track->addArtist($artistA)
              ->setRelease($releaseA);

        $this->assertFalse($track->getName() == ('blop'));
        $this->assertFalse($track->getArtists() == new ArrayCollection([$artistB]));
        $this->assertFalse($track->getRelease() == $releaseB);
    }
    
    public function testIsEmpty(): void
    {
        $track = new Track();

        $releaseA = new Release();
        $releaseA->setName('a');

        $artistA = new Artist();
        $artistA->setName('a');

        $track->addArtist($artistA)->removeArtist($artistA);

        $this->assertEmpty($track->getName());
        $this->assertEmpty($track->getArtists());
        $this->assertEmpty($track->getRelease());
    }
}
