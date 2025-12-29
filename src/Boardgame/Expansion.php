<?php
namespace Nataniel\BoardGameGeek\Boardgame;

class Expansion extends Link
{
    public function isInbound(): bool
    {
        return ((string) $this->root['inbound']) === 'true';
    }
}
