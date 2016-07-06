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
use Somnambulist\EntityAudit\EventListener\CreateSchemaListener;
use Somnambulist\EntityAudit\EventListener\LogRevisionsListener;
use Somnambulist\EntityAudit\Metadata\MetadataFactory;

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
        $this->registerDoctrineEventListeners($config);
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
        $this->app->singleton(MetadataFactory::class, function ($app) use ($config) {
            return new MetadataFactory($config->get('entity_audit.audited_entities', []));
        });

        $this->app->singleton(AuditConfiguration::class, function ($app) use ($config) {
            $auditConfig = new AuditConfiguration(
                new UserResolver(
                    $app[Guard::class],
                    $config->get('entity_audit.username_for.unknown_authenticated_user'),
                    $config->get('entity_audit.username_for.unknown_unauthenticated_user')
                )
            );
            $auditConfig
                ->setAuditedEntityClasses($config->get('entity_audit.audited_entities', []))
                ->setGlobalIgnoreColumns($config->get('entity_audit.global_ignore_columns', []))
                ->setRevisionFieldName($config->get('entity_audit.revision_field_name', 'rev'))
                ->setRevisionTableName($config->get('entity_audit.revision_table_name', 'revisions'))
                ->setRevisionIdFieldType($config->get('entity_audit.revision_id_field_type', 'integer'))
                ->setRevisionTypeFieldName($config->get('entity_audit.revision_type_field_name', 'revtype'))
                ->setTablePrefix($config->get('entity_audit.table_prefix', ''))
                ->setTableSuffix($config->get('entity_audit.table_suffix', '_audit'))
            ;

            return $auditConfig;
        });

        $this->app->singleton(AuditReader::class, function ($app) {
            return new AuditReader($app['em'], $app[AuditConfiguration::class], $app[MetadataFactory::class]);
        });

        $this->app->singleton(AuditManager::class, function ($app) {
            return new AuditManager(
                $app[AuditConfiguration::class],
                $app[MetadataFactory::class],
                $app[AuditReader::class]
            );
        });

        $this->app->alias(AuditConfiguration::class, 'entity_audit.config');
        $this->app->alias(AuditManager::class,       'entity_audit.manager');
        $this->app->alias(AuditReader::class,        'entity_audit.reader');
        $this->app->alias(MetadataFactory::class,    'entity_audit.meta_data');
    }

    /**
     * Registers the additional event listeners that will generate revisions
     *
     * @param Repository $config
     */
    protected function registerDoctrineEventListeners(Repository $config)
    {
        $this->app->afterResolving('em', function ($em) {
            $am = $this->app->make(AuditManager::class);

            /** @var EntityManager $em */
            $em->getEventManager()->addEventSubscriber(new CreateSchemaListener($am));
            $em->getEventManager()->addEventSubscriber(new LogRevisionsListener($am));
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
            'entity_audit.config',
            'entity_audit.manager',
            'entity_audit.meta_data',
            'entity_audit.reader',
        ];
    }
}
