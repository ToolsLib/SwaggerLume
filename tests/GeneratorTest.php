<?php

namespace Tests;

use SwaggerLume\Generator;

class GeneratorTest extends LumenTestCase
{
    /** @test */
    public function canGenerateApiJsonFile()
    {
        $this->setPaths();

        Generator::generateDocs();

        $this->assertTrue(file_exists($this->jsonDocsFile()));

        $response = $this->get(config('swagger-lume.routes.docs'));

        $this->assertResponseOk();

        $this->assertContains('Swagger Lume API', $response->response->getContent());
        $this->assertContains('my-default-host.com', $response->response->getContent());
    }

    /** @test */
    public function canGenerateApiJsonFileWithChangedBasePath()
    {
        $this->setPaths();

        $cfg = config('swagger-lume');
        $cfg['paths']['base'] = '/new_path/is/here';
        config(['swagger-lume' => $cfg]);

        Generator::generateDocs();

        $this->assertTrue(file_exists($this->jsonDocsFile()));

        $response = $this->get(config('swagger-lume.routes.docs'));

        $this->assertResponseOk();

        $this->assertContains('Swagger Lume API', $response->response->getContent());
        $this->assertContains('new_path', $response->response->getContent());
    }

    /** @test */
    public function canSetProxy()
    {
        $this->setPaths();

        $cfg = config('swagger-lume');
        $cfg['proxy'] = 'http://proxy.dev';
        config(['swagger-lume' => $cfg]);

        $this->get(config('swagger-lume.routes.api'));

        $this->assertResponseOk();

        $this->assertTrue(file_exists($this->jsonDocsFile()));
    }

    /** @test */
    public function canSetValidatorUrl()
    {
        $this->setPaths();

        $cfg = config('swagger-lume');
        $cfg['validator_url'] = 'http://validator-url.dev';
        config(['swagger-lume' => $cfg]);

        $response = $this->get(config('swagger-lume.routes.api'));

        $this->assertResponseOk();

        $this->assertContains('validator-url.dev', $response->response->getContent());

        $this->assertTrue(file_exists($this->jsonDocsFile()));
    }

    /** @test */
    public function canGenerateApiJsonFileWithSecurityDefinition()
    {
        $this->setPaths();

        $cfg = config('swagger-lume');
        $security = [
            'new_api_key_securitye' => [
                'type' => 'apiKey',
                'name' => 'api_key_name',
                'in' => 'query',
            ],
        ];
        $cfg['security'] = $security;
        config(['swagger-lume' => $cfg]);

        Generator::generateDocs();

        $this->assertTrue(file_exists($this->jsonDocsFile()));

        $response = $this->get(config('swagger-lume.routes.docs'));

        $this->assertResponseOk();

        $this->assertContains('new_api_key_securitye', $response->response->getContent());
        $this->seeJson($security);
    }
}
