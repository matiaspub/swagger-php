<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Annotations as OA;
use OpenApi\Generator;

class ScratchTest extends OpenApiTestCase
{
    public function scratchTests(): iterable
    {
        foreach (glob($this->fixture('Scratch/*.php')) as $fixture) {
            $name = pathinfo($fixture, PATHINFO_FILENAME);

            if (0 === strpos($name, 'Abstract')) {
                continue;
            }

            yield $name => [
                OA\OpenApi::VERSION_3_0_0,
                $this->fixture("Scratch/$name.php"),
                $this->fixture("Scratch/$name.yaml"),
                [],
            ];
        }
    }

    /**
     * Test scratch fixtures.
     *
     * @dataProvider scratchTests
     *
     * @requires     PHP 8.1
     */
    public function testScratch(string $version, string $scratch, string $spec, array $expectedLog): void
    {
        foreach ($expectedLog as $logLine) {
            $this->assertOpenApiLogEntryContains($logLine);
        }

        require_once $scratch;

        $openapi = (new Generator($this->getTrackingLogger()))
            ->setVersion($version)
            ->generate([$scratch]);
        // file_put_contents($spec, $openapi->toYaml());
        $this->assertSpecEquals($openapi, file_get_contents($spec));
    }
}
