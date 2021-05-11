<?php declare(strict_types=1);

namespace Somnambulist\EntityAudit\Tests\Stubs;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Class Http
 *
 * @package    Somnambulist\EntityAudit\Tests\Stubs
 * @subpackage Somnambulist\EntityAudit\Tests\Stubs\Http
 */
class Http extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [

    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
    ];
}
