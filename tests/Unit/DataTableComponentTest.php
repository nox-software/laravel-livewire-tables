<?php

namespace Rappasoft\LaravelLivewireTables\Tests\Unit;

use Rappasoft\LaravelLivewireTables\Tests\Http\Livewire\FailingTables\NoColumnsTable;
use Rappasoft\LaravelLivewireTables\Tests\Http\Livewire\FailingTables\NoPrimaryKeyTable;
use Rappasoft\LaravelLivewireTables\Tests\Http\Livewire\PetsTable;
use Rappasoft\LaravelLivewireTables\Tests\TestCase;

class DataTableComponentTest extends TestCase
{
    public function test_primary_key_can_be_set(): void
    {
        $this->assertSame('id', $this->basicTable->getPrimaryKey());

        $this->basicTable->setPrimaryKey('name');

        $this->assertSame('name', $this->basicTable->getPrimaryKey());
    }

    public function test_primary_key_can_be_checked_for_existence(): void
    {
        $this->assertTrue($this->basicTable->hasPrimaryKey());

        $this->basicTable->setPrimaryKey(null);

        $this->assertFalse($this->basicTable->hasPrimaryKey());
    }

    public function test_default_fingerprint_will_always_be_the_same_for_same_datatable(): void
    {
        $this->assertSame(
            [
                $this->basicTable->getDataTableFingerprint(),
                $this->basicTable->getDataTableFingerprint(),
                $this->basicTable->getDataTableFingerprint(),
            ],
            [
                $this->basicTable->getDataTableFingerprint(),
                $this->basicTable->getDataTableFingerprint(),
                $this->basicTable->getDataTableFingerprint(),
            ]
        );
        // Changed due to PHP 7.4
        $this->assertSame($this->basicTable->getDataTableFingerprint(), $this->defaultFingerprintingAlgo(PetsTable::class));

    }

    public function test_default_datatable_fingerprints_will_be_different_for_each_table(): void
    {
        $mockTable = new class extends PetsTable {};

        $this->assertNotSame($this->basicTable->getDataTableFingerprint(), $mockTable->getDataTableFingerprint());
    }

    public function test_default_fingerprint_will_be_url_friendy(): void
    {
        $mocks = [];
        for ($i = 0; $i < 9; $i++) {
            $mocks[$i] = new class extends PetsTable {};
            $this->assertFalse(filter_var('http://'.$mocks[$i]->getDataTableFingerprint().'.dev', FILTER_VALIDATE_URL) === false);
        }
        // control
        $this->assertTrue(filter_var('http://[9/$].dev', FILTER_VALIDATE_URL) === false);
    }

    public function test_minimum_one_column_expected(): void
    {
        $this->expectException(\Rappasoft\LaravelLivewireTables\Exceptions\NoColumnsException::class);
        $table = new NoColumnsTable;
        $table->mountHasFiltersCore();
        $table->boot();
        $table->bootedHasFiltersCore();
        $table->bootedComponentUtilities();
        $table->bootedWithColumns();
        $table->bootedWithColumnSelect();
        $table->booted();
        $table->renderingWithPagination($view, []);
        $table->render();

    }
}
