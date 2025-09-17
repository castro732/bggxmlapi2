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
}
