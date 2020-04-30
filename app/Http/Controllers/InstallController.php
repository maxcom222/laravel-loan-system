<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class InstallController extends Controller
{
    //
    public function index()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('key:generate');
        Artisan::call('view:clear');
        return view('install.start', compact(''));
    }

    public function requirements()
    {
        $requirements = [
            'PHP Version (>= 5.5.9)' => version_compare(phpversion(), '5.5.9', '>='),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PDO Extension' => extension_loaded('PDO'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'GD Extension' => extension_loaded('gd'),
            'Fileinfo Extension' => extension_loaded('fileinfo')
        ];
        $next = true;
        foreach ($requirements as $key) {
            if ($key == false) {
                $next = false;
            }
        }
        return view('install.requirements', compact('requirements', 'next'));
    }

    public function permissions()
    {
        $permissions = [
            'public/uploads' => is_writable(public_path('uploads')),
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework/cache' => is_writable(storage_path('framework/cache')),
            'storage/framework/sessions' => is_writable(storage_path('framework/sessions')),
            'storage/framework/views' => is_writable(storage_path('framework/views')),
            'storage/logs' => is_writable(storage_path('logs')),
            'storage' => is_writable(storage_path('')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env file' => is_writable(base_path('.env')),
        ];
        $next = true;
        foreach ($permissions as $key) {
            if ($key == false) {
                $next = false;
            }
        }
        return view('install.permissions', compact('permissions', 'next'));
    }

    public function database(Request $request)
    {
        if ($request->isMethod('post')) {
            $credentials = array();
            $credentials["host"] = $request->host;
            $credentials["username"] = $request->username;
            $credentials["password"] = $request->password;
            $credentials["name"] = $request->name;
            $credentials["port"] = $request->port;
            $default = config('database.default');

            config([
                "database.connections.{$default}.host" => $credentials['host'],
                "database.connections.{$default}.database" => $credentials['name'],
                "database.connections.{$default}.username" => $credentials['username'],
                "database.connections.{$default}.password" => $credentials['password'],
                "database.connections.{$default}.port" => $credentials['port']
            ]);

            $path = base_path('.env');
            $env = file($path);

            $env = str_replace('DB_HOST=' . env('DB_HOST'), 'DB_HOST=' . $credentials['host'], $env);
            $env = str_replace('DB_DATABASE=' . env('DB_DATABASE'), 'DB_DATABASE=' . $credentials['name'], $env);
            $env = str_replace('DB_USERNAME=' . env('DB_USERNAME'), 'DB_USERNAME=' . $credentials['username'], $env);
            $env = str_replace('DB_PASSWORD=' . env('DB_PASSWORD'), 'DB_PASSWORD=' . $credentials['password'], $env);
            $env = str_replace('DB_PORT=' . env('DB_PORT'), 'DB_PORT=' . $credentials['port'], $env);
            file_put_contents($path, $env);
            try {
                DB::statement("SHOW TABLES");
                //connection made,lets install database
                return redirect('install/installation');
            } catch (\Exception $e) {
                Log::info($e->getMessage());
                Flash::warning(trans('general.install_database_failed'));
                copy(base_path('.env.example'), base_path('.env'));
                return redirect()->back()->with(["error" => trans('general.install_database_failed')]);
            }

        }
        return view('install.database', compact(''));
    }

    public function installation(Request $request)
    {
        if ($request->isMethod('post')) {
            try {

                Artisan::call('view:clear');
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
                file_put_contents(storage_path('installed'), 'Welcome to ULM');
                return redirect('install/complete');

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Log::error($e->getTraceAsString());
                Flash::warning(trans('general.install_error'));
                return redirect()->back();
            }
        }
        return view('install.installation', compact(''));
    }

    public function complete()
    {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');

        return view('install.complete', compact(''));
    }
}
