<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
class SwaggerDocScanner extends Command
{
    protected $signature = 'swaggerdoc:scan';
    public function handle()
    {
        $path = dirname(dirname(__DIR__));
        $outputPath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'public/swaggerdoc.json';
        $openApi = \OpenApi\Generator::scan([$path]);
        header('Content-Type: application/json');
        file_put_contents($outputPath, $openApi->toJson());
        $this->info('Output ' . $outputPath);
    }
}
