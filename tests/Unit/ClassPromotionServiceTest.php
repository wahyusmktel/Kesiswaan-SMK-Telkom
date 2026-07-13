<?php

namespace Tests\Unit;

use App\Services\ClassPromotionService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ClassPromotionServiceTest extends TestCase
{
    #[DataProvider('classLevelProvider')]
    public function test_it_recognizes_class_levels(string $name, ?int $expected): void
    {
        $this->assertSame($expected, (new ClassPromotionService())->classLevel($name));
    }

    public static function classLevelProvider(): array
    {
        return [
            ['X TJKT 1', 10],
            ['XI TJKT 1', 11],
            ['XII TJKT 1', 12],
            ['X-TKJ-2', 10],
            ['Kelas X TKJ', null],
        ];
    }

    #[DataProvider('promotionNameProvider')]
    public function test_it_builds_the_next_class_name(string $source, string $expected): void
    {
        $this->assertSame($expected, (new ClassPromotionService())->promotedClassName($source));
    }

    public static function promotionNameProvider(): array
    {
        return [
            ['X TJKT 1', 'XI TJKT 1'],
            ['XI TJKT 1', 'XII TJKT 1'],
            ['X-TKJ-2', 'XI-TKJ-2'],
        ];
    }
}
