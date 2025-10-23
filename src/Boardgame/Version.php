<?php
namespace Nataniel\BoardGameGeek\Boardgame;

class Version extends Link
{
    public function getName(): string
    {
        // Handle both link nodes (with attribute 'value') and item nodes (<name value=...>)
        $valueAttr = (string)$this->root['value'];
        if ($valueAttr !== '') {
            return $valueAttr;
        }

        // Try to find primary name first
        if ($names = $this->root->xpath("name[@type='primary']")) {
            if (isset($names[0]['value']) && (string)$names[0]['value'] !== '') {
                return (string)$names[0]['value'];
            }
        }

        // Fallback to first name element
        if (isset($this->root->name['value'])) {
            return (string)$this->root->name['value'];
        }

        return '';
    }

    public function getImage(): ?string
    {
        return isset($this->root->image) ? (string) $this->root->image : null;
    }

    public function getThumbnail(): ?string
    {
        return isset($this->root->thumbnail) ? (string) $this->root->thumbnail : null;
    }

    public function getYearPublished(): int
    {
        return (int) $this->root->yearpublished['value'];
    }

    public function getCanonicalName(): string
    {
        return (string) $this->root->canonicalname['value'];
    }

}
