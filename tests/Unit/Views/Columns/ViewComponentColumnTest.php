<?php

namespace Rappasoft\LaravelLivewireTables\Tests\Unit\Views\Columns;

use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\Attributes\Group;
use Rappasoft\LaravelLivewireTables\Exceptions\DataTableConfigurationException;
use Rappasoft\LaravelLivewireTables\Tests\Http\TestComponent;
use Rappasoft\LaravelLivewireTables\Tests\Models\Pet;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ViewComponentColumn;

#[Group('Columns')]
final class ViewComponentColumnTest extends ColumnTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Blade::component('test-component', TestComponent::class);
        self::$columnInstance = ViewComponentColumn::make('Name');
    }

    public function test_can_have_component_view(): void
    {
        $column = ViewComponentColumn::make('Age 2', 'age')
            ->attributes(fn ($value, $row, Column $column) => [
                'age' => $row->age,
            ]);

        $this->assertFalse($column->hasComponentView());
        $column->component('livewire-tables-test::test');
        $this->assertTrue($column->hasComponentView());
    }

    public function test_can_not_omit_component(): void
    {
        $this->expectException(DataTableConfigurationException::class);

        $column = ViewComponentColumn::make('Age 2', 'age')
            ->attributes(fn ($value, $row, Column $column) => [
                'age' => $row->age,
            ]);
        $contents = $column->getContents(Pet::find(1));
        $this->assertSame('<div>2420</div>', $contents);

    }

    public function test_can_use_custom_component(): void
    {
        $column = ViewComponentColumn::make('Age 2', 'age')
            ->attributes(fn ($value, $row, Column $column) => [
                'age' => $row->age,
            ]);

        $this->assertFalse($column->hasCustomComponent());
        $column->customComponent(\Rappasoft\LaravelLivewireTables\Tests\Http\TestComponent::class);
        $contents = $column->getContents(Pet::find(1));
        $this->assertSame('<div>2420</div>', $contents);
        $this->assertTrue($column->hasCustomComponent());

    }

    /*public function test_can_render_component(): void
    {

        $column = ViewComponentColumn::make('Age 2', 'age')
            ->component('test-component')
            ->attributes(fn ($value, $row, Column $column) => [
                'age' => $row->age,
            ]);
        $contents = $column->getContents(Pet::find(1));
        $this->assertSame('<div>2420</div>', $contents);

    }*/

    public function test_can_not_use_as_label(): void
    {
        $this->expectException(DataTableConfigurationException::class);

        self::$columnInstance->label(fn () => 'My Label')->getContents(Pet::find(1));
    }

    public function test_can_not_return_invalid_attributes(): void
    {
        $this->expectException(DataTableConfigurationException::class);

        self::$columnInstance->component('test-component')->attributes(fn ($value, $row, Column $column) => 'test')->getContents(Pet::find(1));

    }
}
