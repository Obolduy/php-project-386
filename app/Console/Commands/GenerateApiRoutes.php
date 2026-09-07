<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateApiRoutes extends Command
{
    protected $signature = 'contract:routes
        {--spec=contract/openapi/openapi.json : Путь к OpenAPI-спецификации}
        {--output=routes/api.php : Куда записать маршруты}';

    protected $description = 'Сгенерировать routes/api.php из OpenAPI-спецификации';

    public function handle(): int
    {
        $specPath = base_path((string) $this->option('spec'));

        if (! is_file($specPath)) {
            $this->error("Спецификация не найдена: {$specPath}");

            return self::FAILURE;
        }

        $spec = json_decode((string) file_get_contents($specPath), true);
        $controllers = $this->controllersByShortName();

        $lines = [];
        $skipped = [];

        foreach ($spec['paths'] as $path => $operations) {
            foreach ($operations as $method => $operation) {
                $operationId = $operation['operationId'];
                [$group, $action] = explode('_', $operationId, 2);
                $shortName = "{$group}Controller";

                if (! isset($controllers[$shortName]) || ! method_exists($controllers[$shortName], $action)) {
                    $skipped[] = "{$operationId} → {$shortName}::{$action}";

                    continue;
                }

                $lines[] = sprintf(
                    "Route::%s('%s', [%s::class, '%s'])->name('%s');",
                    $method,
                    $this->toLaravelPath($path),
                    $shortName,
                    $action,
                    $this->toRouteName($operationId),
                );
            }
        }

        $imports = collect($controllers)
            ->only(array_map(fn (string $line): string => explode('::', explode('[', $line)[1])[0], $lines))
            ->sort()
            ->map(fn (string $fqcn): string => "use {$fqcn};")
            ->implode("\n");

        sort($lines);

        file_put_contents(base_path((string) $this->option('output')), implode("\n", [
            '<?php',
            '',
            $imports,
            'use Illuminate\Support\Facades\Route;',
            '',
            ...$lines,
            '',
        ]));

        $this->info(sprintf('Сгенерировано маршрутов: %d', count($lines)));

        foreach ($skipped as $line) {
            $this->warn("Пропущено, контроллер не найден: {$line}");
        }

        return self::SUCCESS;
    }

    /** @return array<string, class-string> */
    private function controllersByShortName(): array
    {
        $found = [];

        foreach (glob(app_path('*/Infrastructure/*Controller.php')) as $file) {
            $relative = str_replace([app_path().'/', '.php'], '', $file);
            $fqcn = 'App\\'.str_replace('/', '\\', $relative);
            $found[class_basename($fqcn)] = $fqcn;
        }

        return $found;
    }

    private function toLaravelPath(string $path): string
    {
        return ltrim(preg_replace('/\{([^}]+)\}/', '{$1}', $path), '/');
    }

    private function toRouteName(string $operationId): string
    {
        return str(str_replace('_', '.', $operationId))->kebab()->replace('-.', '.')->toString();
    }
}
