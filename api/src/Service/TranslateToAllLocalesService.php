<?php

namespace App\Service;

use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslateToAllLocalesService {
    public const VALIDATORS_DOMAIN = 'validators';

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly TranslatorBagInterface $translatorBag,
        private readonly array $translateToLocales
    ) {
    }

    public function translate(string $message, array $params): array {
        $result = [];
        foreach ($this->translateToLocales as $locale) {
            $catalogue = $this->translatorBag->getCatalogue($locale);
            if ($catalogue->defines($message, self::VALIDATORS_DOMAIN)) {
                $result[$locale] = $this->translator->trans($message, $params, self::VALIDATORS_DOMAIN, $locale);
            }
        }

        return $result;
    }
}
