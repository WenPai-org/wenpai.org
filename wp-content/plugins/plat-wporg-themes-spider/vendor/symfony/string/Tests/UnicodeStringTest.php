<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\String\Tests;

use Symfony\Component\String\AbstractString;
use Symfony\Component\String\UnicodeString;

class UnicodeStringTest extends AbstractUnicodeTestCase
{
    protected static function createFromString(string $string): AbstractString
    {
        return new UnicodeString($string);
    }

    public static function provideWrap(): array
    {
        return array_merge(
            parent::provideWrap(),
            [
                [
                    ['Käse' => static::createFromString('köstlich'), 'fromage' => static::createFromString('délicieux')],
                    ["Ka\u{0308}se" => "ko\u{0308}stlich", 'fromage' => 'délicieux'],
                ],
                [
                    ['a' => 1, 'ä' => ['ö' => 2, 'ü' => 3]],
                    ['a' => 1, "a\u{0308}" => ["o\u{0308}" => 2, 'ü' => 3]],
                ],
            ]
        );
    }

    public static function provideLength(): array
    {
        return array_merge(
            parent::provideLength(),
            [
                // 5 letters + 3 combining marks
                [5, 'अनुच्छेद'],
            ]
        );
    }

    public static function provideSplit(): array
    {
        return array_merge(
            parent::provideSplit(),
            [
                [
                    'अ.नु.च्.छे.द',
                    '.',
                    [
                        static::createFromString('अ'),
                        static::createFromString('नु'),
                        static::createFromString('च्'),
                        static::createFromString('छे'),
                        static::createFromString('द'),
                    ],
                    null,
                ],
            ]
        );
    }

    public static function provideChunk(): array
    {
        return array_merge(
            parent::provideChunk(),
            [
                [
                    'अनुच्छेद',
                    [
                        static::createFromString('अ'),
                        static::createFromString('नु'),
                        static::createFromString('च्'),
                        static::createFromString('छे'),
                        static::createFromString('द'),
                    ],
                    1,
                ],
            ]
        );
    }

    public static function provideBytesAt(): array
    {
        return array_merge(
            parent::provideBytesAt(),
            [
                [[0xC3, 0xA4], "Spa\u{0308}ßchen", 2],
                [[0x61, 0xCC, 0x88], "Spa\u{0308}ßchen", 2, UnicodeString::NFD],
                [[0xE0, 0xA4, 0xB8, 0xE0, 0xA5, 0x8D], 'नमस्ते', 2],
            ]
        );
    }

    public static function provideCodePointsAt(): array
    {
        return array_merge(
            parent::provideCodePointsAt(),
            [
                [[0xE4], "Spa\u{0308}ßchen", 2],
                [[0x61, 0x0308], "Spa\u{0308}ßchen", 2, UnicodeString::NFD],
                [[0x0938, 0x094D], 'नमस्ते', 2],
            ]
        );
    }

    public static function provideLower(): array
    {
        return array_merge(
            parent::provideLower(),
            [
                // Hindi
                ['अनुच्छेद', 'अनुच्छेद'],
            ]
        );
    }

    public static function provideUpper(): array
    {
        return array_merge(
            parent::provideUpper(),
            [
                // Hindi
                ['अनुच्छेद', 'अनुच्छेद'],
            ]
        );
    }

    public static function provideAppend(): array
    {
        return array_merge(
            parent::provideAppend(),
            [
                [
                    'तद्भव देशज',
                    ['तद्भव', ' ', 'देशज'],
                ],
                [
                    'तद्भव देशज विदेशी',
                    ['तद्भव', ' देशज', ' विदेशी'],
                ],
            ]
        );
    }

    public static function providePrepend(): array
    {
        return array_merge(
            parent::providePrepend(),
            [
                [
                    'देशज तद्भव',
                    ['तद्भव', 'देशज '],
                ],
                [
                    'विदेशी देशज तद्भव',
                    ['तद्भव', 'देशज ', 'विदेशी '],
                ],
            ]
        );
    }

    public static function provideBeforeAfter(): array
    {
        return array_merge(
            parent::provideBeforeAfter(),
            [
                ['द foo अनुच्छेद', 'द', 'अनुच्छेद foo अनुच्छेद', 0, false],
                ['अनुच्छे', 'द', 'अनुच्छेद foo अनुच्छेद', 0, true],
            ]
        );
    }

    public static function provideBeforeAfterIgnoreCase(): array
    {
        return array_merge(
            parent::provideBeforeAfterIgnoreCase(),
            [
                ['दछेच्नुअ', 'छेछे', 'दछेच्नुअ', 0, false],
                ['दछेच्नुअ', 'छेछे', 'दछेच्नुअ', 0, true],
                ['छेच्नुअ', 'छे', 'दछेच्नुअ', 0, false],
                ['द', 'छे', 'दछेच्नुअ', 0, true],
            ]
        );
    }

    public static function provideBeforeAfterLast(): array
    {
        return array_merge(
            parent::provideBeforeAfterLast(),
            [
                ['दछेच्नुअ-दछेच्नु-अदछेच्नु', 'छेछे', 'दछेच्नुअ-दछेच्नु-अदछेच्नु', 0, false],
                ['दछेच्नुअ-दछेच्नु-अदछेच्नु', 'छेछे', 'दछेच्नुअ-दछेच्नु-अदछेच्नु', 0, true],
                ['-दछेच्नु', '-द', 'दछेच्नुअ-दछेच्नु-अद-दछेच्नु', 0, false],
                ['दछेच्नुअ-दछेच्नु-अद', '-द', 'दछेच्नुअ-दछेच्नु-अद-दछेच्नु', 0, true],
            ]
        );
    }

    public static function provideBeforeAfterLastIgnoreCase(): array
    {
        return array_merge(
            parent::provideBeforeAfterLastIgnoreCase(),
            [
                ['दछेच्नुअ-दछेच्नु-अदछेच्नु', 'छेछे', 'दछेच्नुअ-दछेच्नु-अदछेच्नु', 0, false],
                ['दछेच्नुअ-दछेच्नु-अदछेच्नु', 'छेछे', 'दछेच्नुअ-दछेच्नु-अदछेच्नु', 0, true],
                ['-दछेच्नु', '-द', 'दछेच्नुअ-दछेच्नु-अद-दछेच्नु', 0, false],
                ['दछेच्नुअ-दछेच्नु-अद', '-द', 'दछेच्नुअ-दछेच्नु-अद-दछेच्नु', 0, true],
            ]
        );
    }

    public static function provideReplace(): array
    {
        return array_merge(
            parent::provideReplace(),
            [
                ['Das Innenministerium', 1, 'Das Außenministerium', 'Auß', 'Inn'],
                ['दछेच्नुद-दछेच्नु-ददछेच्नु', 2, 'दछेच्नुअ-दछेच्नु-अदछेच्नु', 'अ', 'द'],
            ]
        );
    }

    public static function provideReplaceIgnoreCase(): array
    {
        return array_merge(
            parent::provideReplaceIgnoreCase(),
            [
                ['Das Aussenministerium', 1, 'Das Außenministerium', 'auß', 'Auss'],
                ['दछेच्नुद-दछेच्नु-ददछेच्नु', 2, 'दछेच्नुअ-दछेच्नु-अदछेच्नु', 'अ', 'द'],
            ]
        );
    }

    public static function provideStartsWith()
    {
        return array_merge(
            parent::provideStartsWith(),
            [
                [false, "cle\u{0301} prive\u{0301}e", 'cle', UnicodeString::NFD],
                [true, "cle\u{0301} prive\u{0301}e", 'clé', UnicodeString::NFD],
            ]
        );
    }

    public static function provideEndsWith()
    {
        return array_merge(
            parent::provideEndsWith(),
            [
                [false, "cle\u{0301} prive\u{0301}e", 'ee', UnicodeString::NFD],
                [true, "cle\u{0301} prive\u{0301}e", 'ée', UnicodeString::NFD],
            ]
        );
    }
}
