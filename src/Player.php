<?php
namespace Nataniel\BoardGameGeek;

class Player
{
    /** @var \SimpleXMLElement */
    private $root;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->root = $xml;
    }

    public function getUsername(): string
    {
        return (string) ($this->root['username'] ?? '');
    }

    public function getUserid(): ?int
    {
        $val = trim((string) ($this->root['userid'] ?? ''));
        if ($val === '' || !is_numeric($val)) {
            return null;
        }
        return (int)$val;
    }

    public function getName(): string
    {
        return (string) ($this->root['name'] ?? '');
    }

    public function getStartPosition(): string
    {
        return (string) ($this->root['startposition'] ?? '');
    }

    public function getColor(): string
    {
        return (string) ($this->root['color'] ?? '');
    }

    public function getScore(): ?int
    {
        $val = trim((string) ($this->root['score'] ?? ''));
        if ($val === '' || !is_numeric($val)) {
            return null;
        }
        return (int)$val;
    }

    public function isNew(): bool
    {
        return self::toBool($this->root['new'] ?? null);
    }

    public function getRating(): ?float
    {
        $val = trim((string) ($this->root['rating'] ?? ''));
        if ($val === '' || !is_numeric($val)) {
            return null;
        }
        return (float)$val;
    }

    public function isWin(): bool
    {
        return self::toBool($this->root['win'] ?? null);
    }

    private static function toBool($value): bool
    {
        $v = strtolower(trim((string)$value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true);
    }
}
