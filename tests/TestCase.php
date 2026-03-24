<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $compiledViewPath = storage_path('framework/testing/views');

        if (! is_dir($compiledViewPath)) {
            mkdir($compiledViewPath, 0777, true);
        }

        config()->set('view.compiled', $compiledViewPath);
    }
}
