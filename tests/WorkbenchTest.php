<?php

namespace Outl1ne\NovaTableField\Tests;

use Orchestra\Testbench\TestCase;
use Outl1ne\NovaTableField\FieldServiceProvider;

class WorkbenchTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            FieldServiceProvider::class,
        ];
    }

    /** @test */
    public function it_can_load_service_provider()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function it_has_correct_package_configuration()
    {
        $providers = $this->app->getLoadedProviders();
        
        $this->assertArrayHasKey(FieldServiceProvider::class, $providers);
    }
}