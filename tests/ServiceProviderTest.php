<?php

namespace Somnambulist\EntityAudit\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Tools\ToolEvents;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\Request;
use LaravelDoctrine\ORM\IlluminateRegistry;
use Somnambulist\EntityAudit\Tests\Stubs\Http;

/**
 * Class ServiceProviderTest
 *
 * @package    Somnambulist\EntityAudit\Tests
 * @subpackage Somnambulist\EntityAudit\Tests\ServiceProviderTest
 */
class ServiceProviderTest extends TestCase
{

    public function createApplication(): Application
    {
        $app = new Application(__DIR__ . '/Stubs/laravel');
        $app->singleton(Kernel::class, Http::class);
        $app->singleton(ExceptionHandler::class, Handler::class);
        // hackety, hack, hack
        $app->singleton('request', function () { return Request::create('/'); });
        // more hacking
        $app->singleton(Guard::class, function () {
            return new class implements Guard {
                public function check() {}
                public function guest() {}
                public function user() {}
                public function id() {}
                public function validate(array $credentials = []) {}
                public function setUser(Authenticatable $user) {}
            };
        });
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function testListenersAreRegistered()
    {
        /** @var IlluminateRegistry $reg */
        $reg = $this->app->get(IlluminateRegistry::class);
        /** @var EntityManager $em */
        $em  = $reg->getManager();
        $evm = $em->getEventManager();

        $this->assertNotCount(0, $reg->getManager()->getEventManager()->getListeners());
        $this->assertCount(1, $evm->getListeners(ToolEvents::postGenerateSchema));
        $this->assertCount(1, $evm->getListeners(ToolEvents::postGenerateSchemaTable));
        $this->assertCount(1, $evm->getListeners(Events::onFlush));
        $this->assertCount(1, $evm->getListeners(Events::postPersist));
        $this->assertCount(1, $evm->getListeners(Events::postUpdate));
        $this->assertCount(1, $evm->getListeners(Events::postFlush));
    }
}
