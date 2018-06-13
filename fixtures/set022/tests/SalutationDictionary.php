<?php

declare(strict_types=1);

namespace Set022;

final class SalutationDictionary implements Dictionary
{
    /**
     * @inheritdoc
     */
    public function provideWords(): array
    {
        return [
            'Hello',
            'Hi',
            'Salut',
            'Bonjour',
        ];
    }
}
