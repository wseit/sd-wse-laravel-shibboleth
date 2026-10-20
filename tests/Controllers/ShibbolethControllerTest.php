<?php

namespace Jhu\Wse\LaravelShibboleth\Tests\Controllers;

use ErrorException;
use Illuminate\Auth\GenericUser;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Jhu\Wse\LaravelShibboleth\Controllers\ShibbolethController;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class ShibbolethControllerTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_controller_loads_without_deprecations(): void
    {
        set_error_handler(function (int $severity, string $message, string $file, int $line): void {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_DEPRECATED);

        try {
            require_once __DIR__.'/../../src/Controllers/ShibbolethController.php';
        } finally {
            restore_error_handler();
        }

        $this->assertTrue(class_exists(ShibbolethController::class, false));
    }

    public function test_constructor_preserves_optional_user(): void
    {
        require_once __DIR__.'/../../src/Controllers/ShibbolethController.php';

        $originalContainer = Container::getInstance();
        $container = new Container;
        $container->instance('config', new Repository(['shibboleth' => ['emulate_idp' => false]]));
        Container::setInstance($container);

        try {
            $controller = new class extends ShibbolethController
            {
                public $user;
            };
            $this->assertNull($controller->user);

            $controller->__construct(null);
            $this->assertNull($controller->user);

            $user = new GenericUser(['id' => 1]);
            $controller->__construct($user);
            $this->assertSame($user, $controller->user);
        } finally {
            Container::setInstance($originalContainer);
        }
    }
}
