<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\{Label, PendingYoutubeTask, Release, Artist};
use Doctrine\Common\Collections\ArrayCollection;
class PendingYoutubeTaskTest extends TestCase
{
    public function testIsTrue(): void
    {
        $pendingYoutubeTask = new PendingYoutubeTask();

        $labelA = new Label();
        $labelA->setName('a');

        $releaseA = new Release();
        $releaseA->setName('a');

        $artistA = new Artist();
        $artistA->setName('a');

        $pendingYoutubeTask->setLabel($labelA)
                           ->setRelease($releaseA)
                           ->setArtist($artistA)
                           ->setPriority(1);


        $this->assertTrue($pendingYoutubeTask->getLabel() == $labelA);
        $this->assertTrue($pendingYoutubeTask->getRelease() == $releaseA);
        $this->assertTrue($pendingYoutubeTask->getArtist() == $artistA);
        $this->assertTrue($pendingYoutubeTask->getPriority() === 1);
    }

    public function testIsFalse(): void
    {
        $pendingYoutubeTask = new PendingYoutubeTask();

        $labelA = new Label();
        $labelB = new Label();
        $labelA->setName('a');
        $labelB->setName('b');

        $releaseA = new Release();
        $releaseB = new Release();
        $releaseA->setName('a');
        $releaseB->setName('b');

        $artistA = new Artist();
        $artistB = new Artist();
        $artistA->setName('a');
        $artistB->setName('b');

        $pendingYoutubeTask->setLabel($labelA)
                           ->setRelease($releaseA)
                           ->setArtist($artistA)
                           ->setPriority(1);


        $this->assertFalse($pendingYoutubeTask->getLabel() == $labelB);
        $this->assertFalse($pendingYoutubeTask->getRelease() == $releaseB);
        $this->assertFalse($pendingYoutubeTask->getArtist() == $artistB);
        $this->assertFalse($pendingYoutubeTask->getPriority() === 2);
    }
    
    public function testIsEmpty(): void
    {
        $pendingYoutubeTask = new PendingYoutubeTask();

        $this->assertEmpty($pendingYoutubeTask->getLabel());
        $this->assertEmpty($pendingYoutubeTask->getRelease());
        $this->assertEmpty($pendingYoutubeTask->getArtist());
        $this->assertEmpty($pendingYoutubeTask->getPriority());
    }
}
