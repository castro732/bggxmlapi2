<?php
namespace Nataniel\BoardGameGeek\Collection;

class Item
{
    /** @var \SimpleXMLElement */
    private $root;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->root = $xml;
    }

    public function getObjectType(): string
    {
        return (string)$this->root['objecttype'];
    }

    public function getObjectId(): int
    {
        return (int)$this->root['objectid'];
    }

    public function getSubtype(): string
    {
        return (string)$this->root['subtype'];
    }

    public function getCollId(): int
    {
        return (int)$this->root['collid'];
    }

    public function getName(): string
    {
        return (string)$this->root->name;
    }

    public function getYearPublished(): int
    {
        return (int)$this->root->yearpublished;
    }

    public function getImage(): string
    {
        return (string)$this->root->image;
    }

    public function getThumbnail(): string
    {
        return (string)$this->root->thumbnail;
    }

    public function getStatus(): \SimpleXMLElement
    {
        return $this->root->status;
    }

    public function getNumPlays(): int
    {
        return (int)$this->root->numplays;
    }

    private function getStats(): ?\SimpleXMLElement
    {
        return $this->root->stats;
    }

    public function getMinPlayers(): ?int
    {
        $stats = $this->getStats();
        return $stats ? (int) $stats['minplayers'] : null;
    }

    public function getMaxPlayers(): ?int
    {
        $stats = $this->getStats();
        return $stats ? (int) $stats['maxplayers'] : null;
    }

    public function getPlayingTime(): ?int
    {
        $stats = $this->getStats();
        return $stats ? (int) $stats['playingtime'] : null;
    }

    public function getMinPlayTime(): ?int
    {
        $stats = $this->getStats();
        return $stats ? (int) $stats['minplaytime'] : null;
    }

    public function getMaxPlayTime(): ?int
    {
        $stats = $this->getStats();
        return $stats ? (int) $stats['maxplaytime'] : null;
    }

    public function getRatingAverage(): ?float
    {
        $stats = $this->getStats();
        return $stats ? round((float) $stats->rating->average['value'], 1) : null;
    }
}
