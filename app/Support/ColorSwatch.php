<?php

namespace App\Support;

final class ColorSwatch
{
    public static function hex(?string $name): string
    {
        if ($name === null || trim($name) === '') {
            return '#c4b5a3';
        }

        return match (strtolower(trim($name))) {
            'black', 'noir' => '#2c2a28',
            'white', 'blanc' => '#f5f0ea',
            'beige' => '#d4c4b0',
            'brown', 'marron' => '#6b4f3a',
            'navy' => '#1e2a44',
            'blue', 'bleu' => '#3d4f6f',
            'red', 'rouge' => '#8b3a3a',
            'green', 'vert' => '#4a5c4a',
            'grey', 'gray', 'gris' => '#7a7570',
            'cream', 'ivory', 'ivoire' => '#ede8e0',
            'gold', 'or' => '#b8a06a',
            'camel' => '#c4a574',
            'olive' => '#6b6b4a',
            'sand' => '#cbb89a',
            'burgundy', 'bordeaux' => '#5c2e32',
            'pink', 'rose' => '#c9a9ad',
            'orange' => '#c47a4a',
            'yellow', 'jaune' => '#d4c07a',
            'purple', 'violet' => '#5c4a6b',
            default => '#c4b5a3',
        };
    }
}
