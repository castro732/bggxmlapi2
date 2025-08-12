<?php

namespace Nataniel\BoardGameGeek\Collection;

class ItemStatus
{
    private bool $own;
    private bool $prevOwned;
    private bool $forTrade;
    private bool $want;
    private bool $wantToPlay;
    private bool $wantToBuy;
    private bool $wishlist;
    private ?int $wishlistPriority;
    private bool $preordered;
    private ?\DateTimeImmutable $lastModified;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->own = self::toBool($xml['own'] ?? null);
        $this->prevOwned = self::toBool($xml['prevowned'] ?? null);
        $this->forTrade = self::toBool($xml['fortrade'] ?? null);
        $this->want = self::toBool($xml['want'] ?? null);
        $this->wantToPlay = self::toBool($xml['wanttoplay'] ?? null);
        $this->wantToBuy = self::toBool($xml['wanttobuy'] ?? null);
        $this->wishlist = self::toBool($xml['wishlist'] ?? null);
        $this->wishlistPriority = self::toNullableInt($xml['wishlistpriority'] ?? null);
        $this->preordered = self::toBool($xml['preordered'] ?? null);
        $this->lastModified = self::toDate((string)($xml['lastmodified'] ?? ''));
    }

    private static function toBool($value): bool
    {
        $v = strtolower(trim((string)$value));
        return in_array($v, ['1', 'true', 'yes', 'y'], true);
    }

    private static function toNullableInt($value): ?int
    {
        $s = trim((string)$value);
        if ($s === '' || !is_numeric($s)) {
            return null;
        }
        return (int)$s;
    }

    private static function toDate(string $value): ?\DateTimeImmutable
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function isOwn(): bool { return $this->own; }
    public function isPrevOwned(): bool { return $this->prevOwned; }
    public function isForTrade(): bool { return $this->forTrade; }
    public function isWant(): bool { return $this->want; }
    public function isWantToPlay(): bool { return $this->wantToPlay; }
    public function isWantToBuy(): bool { return $this->wantToBuy; }
    public function isWishlist(): bool { return $this->wishlist; }
    public function getWishlistPriority(): ?int { return $this->wishlistPriority; }
    public function isPreordered(): bool { return $this->preordered; }
    public function getLastModified(): ?\DateTimeImmutable { return $this->lastModified; }
}
