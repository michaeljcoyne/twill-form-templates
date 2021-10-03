<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use A17\Twill\TwillServiceProvider;
use PBoivin\TwillFormTemplates\TwillFormTemplatesServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use A17\Twill\Models\User;

class TestCase extends BaseTestCase
{
    public $superAdmin;

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            TwillServiceProvider::class,
            TwillFormTemplatesServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cleanDirectories();

        $this->artisan('twill:update');

        $this->beforeMigrate();

        $this->artisan('migrate');

        $this->afterSetup();
    }

    protected function beforeMigrate()
    {
    }

    protected function afterSetup()
    {
    }

    protected function cleanDirectories()
    {
        File::cleanDirectory(base_path('routes'));
    }

    protected function copyStubs($filesMap)
    {
        foreach ($filesMap as $source => $destination) {
            File::ensureDirectoryExists(dirname($destination));

            File::copy(__DIR__ . '/Stubs/' . $source, $destination);
        }
    }

    protected function createSuperAdmin()
    {
        $user = User::make([
            'name' => 'Admin',
            'email' => 'admin@test.test',
            'role' => 'SUPERADMIN',
            'published' => true,
        ]);

        $user->password = Hash::make('password');

        $user->save();

        return $user;
    }

    protected function loginSuperAdmin()
    {
        if (! $this->superAdmin) {
            $this->superAdmin = $this->createSuperAdmin();
        }

        $this->post('/admin/login', [
            'email' => 'admin@test.test',
            'password' => 'password',
        ]);
    }
}
