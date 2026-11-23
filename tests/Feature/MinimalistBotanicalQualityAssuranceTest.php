<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MinimalistBotanicalQualityAssuranceTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_theme_asset_is_referenced_and_documented_with_zero_cdns_or_emojis(): void
    {
        $themeRoot = public_path('themes/minimalist-botanical');
        $usageDoc = (string) File::get($themeRoot.'/ASSET-USAGE.md');

        $corpus = '';
        foreach (File::allFiles(resource_path('views/themes/minimalist-botanical')) as $file) {
            $corpus .= "\n".File::get($file->getPathname());
        }
        foreach (File::allFiles($themeRoot) as $file) {
            if (in_array($file->getExtension(), ['css', 'js'], true)) {
                $corpus .= "\n".File::get($file->getPathname());
            }
        }

        $this->assertDoesNotMatchRegularExpression(
            '/fonts\.googleapis\.com|fonts\.gstatic\.com|unpkg\.com|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com/i',
            $corpus
        );

        foreach (File::allFiles($themeRoot) as $file) {
            $filename = $file->getFilename();
            if ($filename === 'ASSET-USAGE.md') {
                continue;
            }

            $searchToken = $file->getExtension() === 'svg'
                ? pathinfo($filename, PATHINFO_FILENAME)
                : $filename;

            $this->assertStringContainsString($searchToken, $corpus, "Aset tidak terpakai: {$filename}");
            $this->assertStringContainsString($filename, $usageDoc, "Aset belum tercatat di ASSET-USAGE.md: {$filename}");
        }

        $this->assertDoesNotMatchRegularExpression('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]/u', $corpus);
    }
}
