<?php
namespace App\Tests\src\Unit;

use Nataniel\BoardGameGeek\Collection;
use Nataniel\BoardGameGeek\Collection\Item;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /** @var Collection */
    private Collection $collection;

    protected function setUp(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection.xml');
        $this->collection = new Collection($xml);
    }

    public function testCountMatchesXmlAndIterator(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection.xml');
        $expectedTotal = (int) $xml['totalitems'];

        // count() should read from the XML attribute
        $this->assertSame($expectedTotal, $this->collection->count());

        // And number of parsed items should match as well
        $itemsFromIterator = iterator_to_array($this->collection);
        $this->assertCount($expectedTotal, $itemsFromIterator);

        foreach ($itemsFromIterator as $item) {
            $this->assertInstanceOf(Item::class, $item);
        }
    }

    public function testFirstItemFields(): void
    {
        $items = iterator_to_array($this->collection);
        $this->assertNotEmpty($items);
        /** @var Item $first */
        $first = $items[0];

        $this->assertSame('thing', $first->getObjectType());
        $this->assertSame(390092, $first->getObjectId());
        $this->assertSame('boardgame', $first->getSubtype());
        $this->assertSame(113685788, $first->getCollId());
        $this->assertSame('¡Aventureros al Tren! Legacy: Leyendas del Oeste', $first->getName());
        $this->assertSame(2023, $first->getYearPublished());
        $this->assertStringStartsWith('https://cf.geekdo-images.com/', $first->getImage());
        $this->assertStringStartsWith('https://cf.geekdo-images.com/', $first->getThumbnail());

        $status = $first->getStatus();
        $this->assertTrue($status->isOwn());
        $this->assertFalse($status->isPrevOwned());
        $this->assertFalse($status->isForTrade());
        $this->assertFalse($status->isWant());
        $this->assertFalse($status->isWantToPlay());
        $this->assertFalse($status->isWantToBuy());
        $this->assertFalse($status->isWishlist());
        $this->assertFalse($status->isPreordered());
        $this->assertNotNull($status->getLastModified());
        $this->assertSame('2023-12-18 14:21:07', $status->getLastModified()->format('Y-m-d H:i:s'));

        $this->assertSame(6, $first->getNumPlays());
    }

    public function testStatsAndRatingsAreNullWhenAbsent(): void
    {
        $items = iterator_to_array($this->collection);
        $this->assertNotEmpty($items);
        /** @var Item $any */
        $any = $items[0];

        $this->assertNull($any->getMinPlayers());
        $this->assertNull($any->getMaxPlayers());
        $this->assertNull($any->getPlayingTime());
        $this->assertNull($any->getMinPlayTime());
        $this->assertNull($any->getMaxPlayTime());
        $this->assertNull($any->getRatingAverage());
    }

    public function testPrevOwnedItemExistsAndParsed(): void
    {
        $targetId = 359871; // Arcs
        $found = null;
        foreach ($this->collection as $item) {
            if ($item->getObjectId() === $targetId) {
                $found = $item;
                break;
            }
        }
        $this->assertInstanceOf(Item::class, $found, 'Expected to find objectid=359871 in collection.xml');

        $status = $found->getStatus();
        $this->assertFalse($status->isOwn());
        $this->assertTrue($status->isPrevOwned());
        $this->assertSame(1, $found->getNumPlays());
        $this->assertNotNull($status->getLastModified());
        $this->assertSame('2025-07-13 15:49:07', $status->getLastModified()->format('Y-m-d H:i:s'));
    }
}
