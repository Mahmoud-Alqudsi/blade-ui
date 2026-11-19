<?php

namespace BladeUi\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'blade-ui:install
                            {--css= : Path to the main CSS file (defaults to resources/css/app.css)}';

    protected $description = 'Connect Blade UI to the main Tailwind stylesheet and show the remaining setup steps';

    /** npm packages required by the library. */
    protected array $nodePackages = ['tailwindcss', '@tailwindcss/vite', 'alpinejs', '@alpinejs/focus'];

    public function handle(): int
    {
        $this->wireCss($this->option('css') ?: resource_path('css/app.css'));
        $this->checkNodePackages();
        $this->printNextSteps();

        return self::SUCCESS;
    }

    protected function wireCss(string $cssPath): void
    {
        $import = "@import '{$this->relativeImportPath($cssPath)}';";

        if (! is_file($cssPath)) {
            $this->components->warn("لم يُعثر على {$cssPath}. أضف السطر التالي يدويًا بعد استيراد tailwindcss:");
            $this->line("  {$import}");

            return;
        }

        $css = (string) file_get_contents($cssPath);

        if (str_contains($css, 'blade-ui.css')) {
            $this->components->info('ملف CSS مربوط بالحزمة مسبقًا.');

            return;
        }

        $count = 0;
        $updated = preg_replace_callback(
            '/^@import\s+(["\'])tailwindcss\1[^\n]*(?:\n|$)/m',
            function (array $match) use ($import) {
                return $match[0].(str_ends_with($match[0], "\n") ? '' : "\n").$import."\n";
            },
            $css,
            1,
            $count,
        );

        if ($count === 0 || $updated === null) {
            $this->components->warn('لم أجد السطر @import "tailwindcss". أضف السطر التالي يدويًا بعده مباشرة:');
            $this->line("  {$import}");

            return;
        }

        file_put_contents($cssPath, $updated);
        $this->components->info("تمت إضافة استيراد الحزمة إلى {$cssPath}");
    }

    protected function checkNodePackages(): void
    {
        $file = base_path('package.json');
        $json = is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [];
        $declared = array_merge($json['dependencies'] ?? [], $json['devDependencies'] ?? []);

        $missing = array_values(array_filter($this->nodePackages, fn (string $name) => ! isset($declared[$name])));

        if ($missing === []) {
            $this->components->info('حزم npm المطلوبة موجودة في package.json.');

            return;
        }

        $this->components->warn('حزم npm ناقصة. نفّذ:');
        $this->line('  npm install '.implode(' ', $missing));
    }

    protected function printNextSteps(): void
    {
        $this->newLine();
        $this->components->info('الخطوة الأخيرة: سجّل إضافة Focus في resources/js/app.js (قبل Alpine.start()):');
        $this->line("  import focus from '@alpinejs/focus';");
        $this->line('  Alpine.plugin(focus);');
        $this->newLine();
        $this->line('ثم شغّل npm run dev. ولعرض دليل المكونات افتح /'.config('blade-ui.demo.path', 'components-demo').' (البيئة المحلية).');
    }

    /** Relative path from the CSS file directory to blade-ui.css in vendor. */
    protected function relativeImportPath(string $cssPath): string
    {
        $target = base_path('vendor/'.$this->packageName().'/resources/css/blade-ui.css');

        $split = fn (string $path) => array_values(array_filter(explode('/', str_replace('\\', '/', $path)), 'strlen'));

        $from = $split(dirname($cssPath));
        $to = $split($target);

        while ($from !== [] && $to !== [] && $from[0] === $to[0]) {
            array_shift($from);
            array_shift($to);
        }

        return str_repeat('../', count($from)).implode('/', $to);
    }

    protected function packageName(): string
    {
        $composer = json_decode((string) file_get_contents(dirname(__DIR__, 2).'/composer.json'), true);

        return $composer['name'] ?? 'mahmoud-alqudsi/blade-ui';
    }
}
