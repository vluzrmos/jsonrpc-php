<?php

/**
 * Classe base para todos os testes
 * Fornece utilitários comuns e configurações para PHP 5.6
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{

    /**
     * Checks if integration tests should be run
     *
     * @return bool
     */
    protected function shouldRunIntegrationTests()
    {
        return getenv('RUN_INTEGRATION_TESTS') === '1' || getenv('RUN_INTEGRATION_TESTS') === 'true';
    }

    /**
     * Skips the test if integration tests are disabled
     *
     * @return void
     */
    protected function skipIfIntegrationDisabled()
    {
        if (!$this->shouldRunIntegrationTests()) {
            $this->markTestSkipped('Integration tests disabled. Use RUN_INTEGRATION_TESTS=1 to enable.');
        }
    }

    /**
     * Asserta que um array contém todas as chaves especificadas
     *
     * @param array $expectedKeys
     * @param array $array
     * @param string $message
     */
    protected function assertArrayHasKeys(array $expectedKeys, $array, $message = '')
    {
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $array, $message . " (key: $key)");
        }
    }

    /**
     * Asserts that a string is valid JSON
     *
     * @param string $string
     * @param string $message
     */
    protected function assertIsValidJson($string, $message = '')
    {
        json_decode($string);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error(), $message ?: 'String is not valid JSON');
    }
}
