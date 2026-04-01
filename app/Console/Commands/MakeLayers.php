<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeLayers extends Command
{
    protected $signature = 'make:layers 
                            {name : Base name, example User}
                            {--model= : Model name if different}
                            {--force : Overwrite existing files}';

    protected $description = 'Generate Repository Interface, Eloquent Repository, Service, API Controller, and bind them in AppServiceProvider';

    public function handle(): int
    {
        $baseName = trim($this->argument('name'));
        $modelName = $this->option('model') ?: $baseName;

        $className = "{$baseName}Repository";
        $interfaceName = "{$className}Interface";
        $serviceName = "{$baseName}Service";
        $controllerName = "{$baseName}Controller";
        $storeRequestName = "Store{$baseName}Request";
        $updateRequestName = "Update{$baseName}Request";

        $interfaceDir = app_path('Repositories/Interfaces');
        $repositoryDir = app_path('Repositories/Eloquent');
        $serviceDir = app_path('Services');
        $controllerDir = app_path('Http/Controllers/Api');
        $RequestDir = app_path("Http/Requests/Api/{$baseName}");
        $providerPath = app_path('Providers/AppServiceProvider.php');

        $interfacePath = "{$interfaceDir}/{$interfaceName}.php";
        $repositoryPath = "{$repositoryDir}/{$className}.php";
        $servicePath = "{$serviceDir}/{$serviceName}.php";
        $controllerPath = "{$controllerDir}/{$controllerName}.php";
        $storeRequestPath = "{$RequestDir}/{$storeRequestName}.php";
        $updateRequestPath = "{$RequestDir}/{$updateRequestName}.php";

        File::ensureDirectoryExists($interfaceDir);
        File::ensureDirectoryExists($repositoryDir);
        File::ensureDirectoryExists($serviceDir);
        File::ensureDirectoryExists($controllerDir);
        File::ensureDirectoryExists($RequestDir);

        $this->writeFile(
            $interfacePath,
            $this->buildStub('repository-interface.stub', [
                'interfaceName' => $interfaceName,
                'modelName' => $modelName,
            ])
        );

        $this->writeFile(
            $repositoryPath,
            $this->buildStub('repository-eloquent.stub', [
                'className' => $className,
                'interfaceName' => $interfaceName,
                'modelName' => $modelName,
            ])
        );

        $this->writeFile(
            $servicePath,
            $this->buildStub('service.stub', [
                'serviceName' => $serviceName,
                'interfaceName' => $interfaceName,
            ])
        );

        // $this->writeFile(
        //     $storeRequestPath,
        //     $this->buildStub('storerequest.stub', [
        //         'storeRequestName' => $storeRequestName,
        //         'modelName' => $modelName,
        //     ])
        // );

        // $this->writeFile(
        //     $updateRequestPath,
        //     $this->buildStub('updaterequest.stub', [
        //         'updateRequestName' => $updateRequestName,
        //         'modelName' => $modelName,
        //     ])
        // );
        $this->writeFile(
            $controllerPath,
            $this->buildStub('controllerrepositoryapi.stub', [
                'controllerName' => $controllerName,
                'serviceName' => $serviceName,
                'modelName' => $modelName,
                'storeRequestName' => $storeRequestName,
                'updateRequestName' => $updateRequestName,
            ])
        );

        $this->bindInServiceProvider($providerPath, $interfaceName, $className);

        $this->call('optimize:clear');

        $this->info('Layers generated successfully.');

        return self::SUCCESS;
    }

    protected function writeFile(string $path, string $content): void
    {
        $exists = File::exists($path);

        if ($exists && ! $this->option('force')) {
            $this->warn("Skipped (already exists): {$path}");
            return;
        }

        File::put($path, $content);

        $this->info(($exists ? 'Updated' : 'Created') . ": {$path}");
    }

    protected function buildStub(string $stubName, array $replacements): string
    {
        $stubPath = base_path("stubs/{$stubName}");

        if (! File::exists($stubPath)) {
            $this->error("Stub not found: {$stubPath}");
            exit(self::FAILURE);
        }

        $stub = File::get($stubPath);

        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{' . $key . '}}', $value, $stub);
        }

        return $stub;
    }

    protected function bindInServiceProvider(string $providerPath, string $interfaceName, string $className): void
    {
        if (! File::exists($providerPath)) {
            $this->warn("AppServiceProvider not found: {$providerPath}");
            return;
        }

        $interfaceFqn = "App\\Repositories\\Interfaces\\{$interfaceName}";
        $repositoryFqn = "App\\Repositories\\Eloquent\\{$className}";

        $useInterface = "use {$interfaceFqn};";
        $useRepository = "use {$repositoryFqn};";
        $bindingLine = "\$this->app->bind({$interfaceName}::class, {$className}::class);";

        $content = File::get($providerPath);

        if (! str_contains($content, $useInterface)) {
            $content = preg_replace(
                '/^namespace\s+App\\\\Providers;\s*$/m',
                "namespace App\\Providers;\n\n{$useInterface}",
                $content,
                1
            );
        }

        if (! str_contains($content, $useRepository)) {
            $content = preg_replace(
                '/(use\s+App\\\\Repositories\\\\Interfaces\\\\' . preg_quote($interfaceName, '/') . ';\n?)/',
                "$1{$useRepository}\n",
                $content,
                1
            );
        }

        if (! str_contains($content, $bindingLine)) {
            $content = preg_replace(
                '/public function register\(\): void\s*\{\s*/',
                "public function register(): void\n    {\n        {$bindingLine}\n        ",
                $content,
                1
            );
        }

        File::put($providerPath, $content);

        $this->info('Binding added to AppServiceProvider.');
    }
}
