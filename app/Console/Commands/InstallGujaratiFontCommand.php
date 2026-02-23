<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallGujaratiFontCommand extends Command
{
    protected $signature = 'pdf:install-gujarati-font';

    protected $description = 'Download Noto Sans Gujarati TTF to public/fonts for PDF invoice rendering.';

    private const FONT_URL = 'https://github.com/google/fonts/raw/main/ofl/notosansgujarati/NotoSansGujarati-Regular.ttf';

    private const FONT_FILENAME = 'NotoSansGujarati-Regular.ttf';

    public function handle(): int
    {
        $dir = public_path('fonts');
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0755, true)) {
                $this->error('Could not create directory: ' . $dir);

                return self::FAILURE;
            }
        }

        $path = $dir . '/' . self::FONT_FILENAME;

        $this->info('Downloading Noto Sans Gujarati...');

        $context = stream_context_create([
            'http' => [
                'follow_location' => true,
                'user_agent' => 'Laravel-Inventory-System/1.0',
            ],
        ]);

        $content = @file_get_contents(self::FONT_URL, false, $context);
        if ($content === false || strlen($content) < 1000) {
            $this->warn('Download failed. Please add the font manually:');
            $this->line('  1. Open https://fonts.google.com/noto/specimen/Noto+Sans+Gujarati');
            $this->line('  2. Download the family and extract the ZIP');
            $this->line('  3. Copy NotoSansGujarati-Regular.ttf to: ' . $path);

            return self::FAILURE;
        }

        if (file_put_contents($path, $content) === false) {
            $this->error('Could not write file: ' . $path);

            return self::FAILURE;
        }

        $this->info('Font installed: ' . $path);

        return self::SUCCESS;
    }
}
