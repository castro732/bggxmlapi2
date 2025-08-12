<?php
namespace App\Tests\src\Unit;

use Nataniel\BoardGameGeek\Collection\Item;
use Nataniel\BoardGameGeek\Collection\ItemStatus;
use PHPUnit\Framework\TestCase;

class ItemStatusTest extends TestCase
{
    public function testGetStatusReturnsItemStatusWithParsedValues(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-1.xml');
        $collectionItem = new Item($xml);

        $status = $collectionItem->getStatus();
        $this->assertInstanceOf(ItemStatus::class, $status);
        $this->assertTrue($status->isOwn());
        $this->assertFalse($status->isPrevOwned());
        $this->assertFalse($status->isForTrade());
        $this->assertFalse($status->isWant());
        $this->assertFalse($status->isWantToPlay());
        $this->assertFalse($status->isWantToBuy());
        $this->assertTrue($status->isWishlist());
        $this->assertSame(4, $status->getWishlistPriority());
        $this->assertFalse($status->isPreordered());
        $this->assertNotNull($status->getLastModified());
        $this->assertSame('2022-03-19 09:58:13', $status->getLastModified()->format('Y-m-d H:i:s'));
    }

    public function testMissingOrInvalidValuesAreHandled(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-4.xml');
        $collectionItem = new Item($xml);

        $status = $collectionItem->getStatus();
        $this->assertInstanceOf(ItemStatus::class, $status);
        // All flags default to false when missing
        $this->assertFalse($status->isOwn());
        $this->assertFalse($status->isPrevOwned());
        $this->assertFalse($status->isForTrade());
        $this->assertFalse($status->isWant());
        $this->assertFalse($status->isWantToPlay());
        $this->assertFalse($status->isWantToBuy());
        $this->assertFalse($status->isWishlist());
        $this->assertFalse($status->isPreordered());
        // Wishlist priority null when empty
        $this->assertNull($status->getWishlistPriority());
        // Invalid date becomes null
        $this->assertNull($status->getLastModified());
    }

    public function testCollectionItem2ParsesCorrectly(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-2.xml');
        $collectionItem = new Item($xml);

        $this->assertInstanceOf(Item::class, $collectionItem);

        $status = $collectionItem->getStatus();
        $this->assertInstanceOf(ItemStatus::class, $status);

        $this->assertNotNull($status);

        $this->assertIsBool($status->isOwn());
        $this->assertIsBool($status->isPrevOwned());
        $this->assertIsBool($status->isForTrade());
        $this->assertIsBool($status->isWant());
        $this->assertIsBool($status->isWantToPlay());
        $this->assertIsBool($status->isWantToBuy());
        $this->assertIsBool($status->isWishlist());
        $this->assertIsBool($status->isPreordered());
    }

    public function testCollectionItem3ParsesCorrectly(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/collection-item-3.xml');
        $collectionItem = new Item($xml);

        $this->assertInstanceOf(Item::class, $collectionItem);

        $status = $collectionItem->getStatus();
        $this->assertInstanceOf(ItemStatus::class, $status);

        $this->assertNotNull($status);

        $this->assertIsBool($status->isOwn());
        $this->assertIsBool($status->isPrevOwned());
        $this->assertIsBool($status->isForTrade());
        $this->assertIsBool($status->isWant());
        $this->assertIsBool($status->isWantToPlay());
        $this->assertIsBool($status->isWantToBuy());
        $this->assertIsBool($status->isWishlist());
        $this->assertIsBool($status->isPreordered());
    }

    public function testAllCollectionItemsHaveBasicStructure(): void
    {
        $testFiles = [
            'collection-item-1.xml',
            'collection-item-2.xml',
            'collection-item-3.xml',
            'collection-item-4.xml'
        ];

        foreach ($testFiles as $testFile) {
            $xml = simplexml_load_file(__DIR__ . '/../../files/' . $testFile);
            $collectionItem = new Item($xml);

            // Each item should have basic properties
            $this->assertInstanceOf(Item::class, $collectionItem, "Failed for file: $testFile");

            $status = $collectionItem->getStatus();
            $this->assertInstanceOf(ItemStatus::class, $status, "Failed status for file: $testFile");

            // Test that num plays can be retrieved (should be int or null)
            $numPlays = $collectionItem->getNumPlays();
            $this->assertTrue(is_int($numPlays) || is_null($numPlays), "NumPlays should be int or null for file: $testFile");
        }
    }

}
