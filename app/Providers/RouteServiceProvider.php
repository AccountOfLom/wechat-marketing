<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        // 授权
        $this->mapAuthRoutes();

        // 微信设置相关接口
        $this->mapWechatSettingRoutes();

        // 对接方服务器异步回调
        $this->mapNotifyRoutes();

        // 315 活动
        $this->mapIntegrity315Routes();

        //后台管理系统
        $this->mapAdminRoutes();

        //爆文系统
        $this->mapArticlesRoutes();
    }

    /**
     * 授权
     */
    protected function mapAuthRoutes()
    {
        Route::prefix('auth')
            ->namespace($this->namespace . '\Frontend\Auth')
            ->group(base_path('routes/auth.php'));
    }

    /**
     * 微信设置相关接口
     */
    protected function mapWechatSettingRoutes()
    {
        Route::prefix('WechatSetting')
            ->middleware('userToken')
            ->group(base_path('routes/WechatSetting.php'));
    }

    /**
     * 对接方服务器异步回调
     */
    protected function mapNotifyRoutes()
    {
        Route::prefix('notify')
            ->group(base_path('routes/notify.php'));
    }

    /**
     * 315 活动
     */
    protected function mapIntegrity315Routes()
    {
        Route::prefix('integrity315')
            ->namespace($this->namespace . '\Frontend\Integrity315')
            ->group(base_path('routes/integrity315.php'));
    }

    /**
     * 后台管理系统
     */
    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
        ->namespace($this->namespace . '\Backend')
        ->group(base_path('routes/admin.php'));
    }

    /**
     * 爆文系统
     */
    protected function mapArticlesRoutes()
    {
        Route::prefix('articles')
            ->namespace($this->namespace . '\Frontend\Articles')
            ->group(base_path('routes/articles.php'));
    }
}
