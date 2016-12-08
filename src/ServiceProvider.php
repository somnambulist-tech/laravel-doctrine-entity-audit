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

namespace Somnambulist\EntityAudit;

use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelDoctrine\ORM\IlluminateRegistry;
use Somnambulist\EntityAudit\EventListener\CreateSchemaListener;
use Somnambulist\EntityAudit\EventListener\LogRevisionsListener;
use Somnambulist\EntityAudit\Metadata\MetadataFactory;
use Somnambulist\EntityAudit\Support\TableConfiguration;

/**
 * Class ServiceProvider
 *
 * @package    Somnambulist\EntityAudit
 * @subpackage Somnambulist\EntityAudit\ServiceProvider
 * @author     Dave Redfern
 */
class ServiceProvider extends BaseServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([ $this->getConfigPath() => config_path('entity_audit.php'), ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();

        /** @var Repository $config */
        $config = $this->app->make('config');

        $this->registerCoreServices($config);
        $this->registerEntityAuditConfigurations($config);
    }



    /**
     * Merge config
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'entity_audit');
    }

    /**
     * Registers the core Tenant services
     *
     * @param Repository $config
     *
     * @return void
     */
    protected function registerCoreServices(Repository $config)
    {
        $this->app->singleton(AuditRegistry::class, function () {
            return new AuditRegistry();
        });
        $this->app->alias(AuditRegistry::class, 'entity_audit.registry');
    }

    /**
     * Registers auditing to the specified entity managers
     *
     * @param Repository $config
     *
     * @return void
     */
    protected function registerEntityAuditConfigurations(Repository $config)
    {
        $this->app->afterResolving(IlluminateRegistry::class, function ($registry) use ($config) {
            $table         = $config->get('entity_audit.global.table');
            $users         = $config->get('entity_audit.global.username_for');
            $columns       = $config->get('entity_audit.global.ignore_columns');
            $auditRegistry = $this->app->make(AuditRegistry::class);

            foreach ($config->get('entity_audit.entity_managers', []) as $emName => $emConfig) {
                $users       = array_merge($users, data_get($emConfig, 'username_for', []));
                $metadata    = new MetadataFactory(data_get($emConfig, 'entities', []));
                $auditConfig = new AuditConfiguration(
                    new UserResolver(
                        $this->app->make(Guard::class),
                        $users['unknown_authenticated_user'],
                        $users['unknown_unauthenticated_user']
                    ),
                    new TableConfiguration(array_merge($table, data_get($emConfig, 'table', []))),
                    data_get($emConfig, 'ignore_columns', $columns)
                );
                /** @var IlluminateRegistry $registry */
                /** @var EntityManager $em */
                $em      = $registry->getManager($emName);
                $reader  = new AuditReader($em, $auditConfig, $metadata);
                $manager = new AuditManager($auditConfig, $metadata, $reader);

                $em->getEventManager()->addEventSubscriber(new CreateSchemaListener($manager));
                $em->getEventManager()->addEventSubscriber(new LogRevisionsListener($manager));

                $auditRegistry->add($emName, $manager);

                $this->app->alias($manager, sprintf('entity_audit.%s.manager', $emName));
                $this->app->alias($reader, sprintf('entity_audit.%s.reader', $emName));
            }
        });
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/../config/entity_audit.php';
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            'entity_audit.registry',
        ];
    }
}
