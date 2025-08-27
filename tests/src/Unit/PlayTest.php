<?php
namespace Nataniel\BoardGameGeekTest\Unit;

use Nataniel\BoardGameGeek\Play;
use Nataniel\BoardGameGeek\Player;
use PHPUnit\Framework\TestCase;

class PlayTest extends TestCase
{
    /** @var Play */
    private $play;

    protected function setUp(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../files/play.xml');
        $this->play = new Play($xml->play);
    }

    public function testBasicAttributes()
    {
        $this->assertSame(102838714, $this->play->getId());
        $this->assertSame('2025-08-19', $this->play->getDate());
        $this->assertSame(1, $this->play->getQuantity());
        $this->assertSame(0, $this->play->getLength());
        $this->assertFalse($this->play->isIncomplete());
        $this->assertFalse($this->play->isNoWinStats());
        $this->assertSame('Home', $this->play->getLocation());
    }

    public function testItemInfo()
    {
        $this->assertSame('thing', $this->play->getObjectType());
        $this->assertSame(155987, $this->play->getObjectId());
        $this->assertSame('Abyss', $this->play->getObjectName());
        $this->assertSame(['boardgame'], $this->play->getSubtypes());
    }

    public function testComments()
    {
        $this->assertSame('Played with expansions: - [thing=232197]Abyss: Leviathan[/thing]', $this->play->getComments());
    }

    public function testPlayers()
    {
        $players = $this->play->getPlayers();
        $this->assertCount(4, $players);

        $p0 = $players[0];
        $this->assertInstanceOf(Player::class, $p0);
        $this->assertSame('andiballone', $p0->getUsername());
        $this->assertSame(2919673, $p0->getUserid());
        $this->assertSame('Andi', $p0->getName());
        $this->assertSame(50, $p0->getScore());
        $this->assertFalse($p0->isWin());

        $winner = $players[1];
        $this->assertInstanceOf(Player::class, $winner);
        $this->assertTrue($winner->isWin());
        $this->assertSame(82, $winner->getScore());
    }
}
