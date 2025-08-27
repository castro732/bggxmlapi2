<?php
namespace Nataniel\BoardGameGeek;

class Play
{
    /** @var \SimpleXMLElement */
    private $root;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->root = $xml;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->root['id'];
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return (string) $this->root['date'];
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return (int) $this->root['quantity'];
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return (int) $this->root['length'];
    }

    /**
     * @return bool
     */
    public function isIncomplete()
    {
        return $this->toBool($this->root['incomplete'] ?? null);
    }

    /**
     * @return bool
     */
    public function isNoWinStats()
    {
        return $this->toBool($this->root['nowinstats'] ?? null);
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return (string) $this->root['location'];
    }

    /**
     * @return string
     */
    public function getObjectType()
    {
        return (string) $this->root->item['objecttype'];
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return (int) $this->root->item['objectid'];
    }

    /**
     * @return string
     */
    public function getObjectName()
    {
        return (string) $this->root->item['name'];
    }

    /**
     * @return string[]
     */
    public function getSubtypes()
    {
        $subtypes = [];
        if (isset($this->root->item->subtypes)) {
            foreach ($this->root->item->subtypes->subtype as $subtype) {
                $subtypes[] = (string) $subtype['value'];
            }
        }
        return $subtypes;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return isset($this->root->comments) ? (string) $this->root->comments : '';
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array
    {
        $players = [];
        if (isset($this->root->players)) {
            foreach ($this->root->players->player as $player) {
                $players[] = new Player($player);
            }
        }
        return $players;
    }

    private function toBool($value): bool
    {
        $v = strtolower(trim((string)$value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true);
    }
}
