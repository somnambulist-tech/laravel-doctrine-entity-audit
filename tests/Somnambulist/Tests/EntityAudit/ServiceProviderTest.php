<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL-2.1 license.
 */

namespace Somnambulist\EntityAudit\Tests;

use ArrayAccess;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use LaravelDoctrine\ORM\EntityManagerFactory;
use LaravelDoctrine\ORM\IlluminateRegistry;
use Mockery;
use Illuminate\Contracts\Foundation\Application as ApplicationInterface;
use Somnambulist\EntityAudit\AuditRegistry;
use Somnambulist\EntityAudit\ServiceProvider;
use Somnambulist\EntityAudit\Tests\Fixtures\TestableServiceProvider;

/**
 * Class ServiceProviderTest
 *
 * @package    Somnambulist\Tests\EntityAudit
 * @subpackage Somnambulist\Tests\EntityAudit\ServiceProviderTest
 * @author     Dave Redfern
 */
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mockery\MockInterface|ApplicationInterface
     */
    private $app;

    /**
     * @var Mockery\MockInterface
     */
    private $config;

    /**
     * @var AuditRegistry
     */
    private $registry;

    /**
     * @var IlluminateRegistry
     */
    private $doctrine;

    /**
     * @var ServiceProvider
     */
    private $provider;

    protected function setUp()
    {
        parent::setUp();
//        $this->config   = new Repository();
//        $this->app      = Mockery::mock(Container::class);
//        $this->registry = new AuditRegistry();
//        $this->doctrine = new IlluminateRegistry($this->app, Mockery::mock(EntityManagerFactory::class));
//        $this->doctrine->addManager('default');
//
//        /** @noinspection PhpMethodParametersCountMismatchInspection */
//        $this->app->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('path.config')->andReturn('/some/config/path');
//        /** @noinspection PhpMethodParametersCountMismatchInspection */
//        $this->app->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('config')->andReturn($this->config);
//        $this->app->shouldReceive('make')->zeroOrMoreTimes()->with('config')->andReturn($this->config);
//        $this->app->shouldReceive('make')->zeroOrMoreTimes()->with(AuditRegistry::class)->andReturn($this->registry);
//        $this->app->shouldReceive('alias')->zeroOrMoreTimes()->andReturnUndefined();
//        $this->app->shouldReceive('singleton')->with(AuditRegistry::class)->andReturn($this->registry);
//        $this->app->shouldReceive('singleton')->withAnyArgs()->andReturnUndefined();
//        $this->app->shouldReceive('afterResolving')->once()->andReturn($this->doctrine);
//
//        $this->provider = new TestableServiceProvider($this->app);
    }

    /**
     * Test register provider.
     *
     * @group service-provider
     */
    public function testRegister()
    {
//        /** @noinspection PhpMethodParametersCountMismatchInspection */
//        $this->config->shouldReceive('get')->withAnyArgs()->once()->andReturn([]);
//        /** @noinspection PhpMethodParametersCountMismatchInspection */
//        $this->config->shouldReceive('set')->withAnyArgs()->once()->andReturnUndefined();
//        /** @noinspection PhpMethodParametersCountMismatchInspection */
//        $this->app->shouldReceive('singleton')->withAnyArgs()->once()->andReturnUndefined();
//        $this->provider->register();
    }
}
