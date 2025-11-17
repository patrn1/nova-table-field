<?php

namespace Outl1ne\NovaTableField\Tests;

use Outl1ne\NovaTableField\Table;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Database\Eloquent\Model;

class TableFieldTest extends TestCase
{
    /** @test */
    public function it_can_create_table_field()
    {
        $field = Table::make('Test Table');
        
        $this->assertInstanceOf(Table::class, $field);
        $this->assertEquals('nova-table-field', $field->component);
        $this->assertFalse($field->showOnIndex);
        $this->assertTrue($field->canAdd);
        $this->assertTrue($field->canDelete);
    }

    /** @test */
    public function it_can_set_min_rows()
    {
        $field = Table::make('Test Table')->minRows(3);
        
        $this->assertEquals(3, $field->meta['minRows']);
    }

    /** @test */
    public function it_can_set_max_rows()
    {
        $field = Table::make('Test Table')->maxRows(10);
        
        $this->assertEquals(10, $field->meta['maxRows']);
    }

    /** @test */
    public function it_can_set_min_columns()
    {
        $field = Table::make('Test Table')->minColumns(2);
        
        $this->assertEquals(2, $field->meta['minColumns']);
    }

    /** @test */
    public function it_can_set_max_columns()
    {
        $field = Table::make('Test Table')->maxColumns(5);
        
        $this->assertEquals(5, $field->meta['maxColumns']);
    }

    /** @test */
    public function it_can_disable_adding()
    {
        $field = Table::make('Test Table')->disableAdding();
        
        $this->assertFalse($field->canAdd);
    }

    /** @test */
    public function it_can_disable_deleting()
    {
        $field = Table::make('Test Table')->disableDeleting();
        
        $this->assertFalse($field->canDelete);
    }

    /** @test */
    public function it_can_set_default_values()
    {
        $defaultValues = [['Cell 1', 'Cell 2'], ['Cell 3', 'Cell 4']];
        $field = Table::make('Test Table')->defaultValues($defaultValues);
        
        $this->assertEquals($defaultValues, $field->meta['defaultValues']);
    }

    /** @test */
    public function it_serializes_correctly()
    {
        $field = Table::make('Test Table')
            ->disableAdding()
            ->disableDeleting();
        
        $serialized = $field->jsonSerialize();
        
        $this->assertFalse($serialized['canAdd']);
        $this->assertFalse($serialized['canDelete']);
    }

    /** @test */
    public function it_fills_attribute_from_request()
    {
        $field = Table::make('test_table', 'test_table');
        $model = new TestModel();
        $request = NovaRequest::create('/', 'POST', [
            'test_table' => json_encode([['row1col1', 'row1col2'], ['row2col1', 'row2col2']])
        ]);
        
        // Use reflection to access protected method
        $reflection = new \ReflectionClass($field);
        $method = $reflection->getMethod('fillAttributeFromRequest');
        $method->setAccessible(true);
        $method->invoke($field, $request, 'test_table', $model, 'test_table');
        
        $expected = [['row1col1', 'row1col2'], ['row2col1', 'row2col2']];
        $this->assertEquals($expected, $model->test_table);
    }

    /** @test */
    public function it_handles_empty_request_data()
    {
        $field = Table::make('test_table', 'test_table');
        $model = new TestModel();
        $request = NovaRequest::create('/', 'POST', []);
        
        // Use reflection to access protected method
        $reflection = new \ReflectionClass($field);
        $method = $reflection->getMethod('fillAttributeFromRequest');
        $method->setAccessible(true);
        $method->invoke($field, $request, 'test_table', $model, 'test_table');
        
        $this->assertNull($model->test_table);
    }
}

class TestModel extends Model
{
    protected $fillable = ['test_table'];
    
    protected $casts = [
        'test_table' => 'array'
    ];
}