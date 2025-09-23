<?php

namespace Opscale\NovaAPI\Tests\Fixtures;

trait FixtureLoader
{
    /**
     * Load fixture data from a fixture file.
     *
     * @param string $fixture The fixture file name (without .php extension)
     * @param string|null $key Optional key to get specific fixture data
     * @return mixed
     */
    protected function loadFixture(string $fixture, ?string $key = null): mixed
    {
        $fixtureFile = __DIR__ . "/{$fixture}.php";

        if (! file_exists($fixtureFile)) {
            throw new \InvalidArgumentException("Fixture file '{$fixture}.php' not found");
        }

        $fixtures = include $fixtureFile;

        if ($key === null) {
            return $fixtures;
        }

        if (! array_key_exists($key, $fixtures)) {
            throw new \InvalidArgumentException("Fixture key '{$key}' not found in '{$fixture}.php'");
        }

        return $fixtures[$key];
    }

    /**
     * Load user fixture data.
     *
     * @param string $key
     * @return array
     */
    protected function getUserFixture(string $key = 'test_user'): array
    {
        return $this->loadFixture('users', $key);
    }

    /**
     * Load product fixture data.
     *
     * @param string $key
     * @return array
     */
    protected function getProductFixture(string $key = 'laptop'): array
    {
        return $this->loadFixture('products', $key);
    }

    /**
     * Load token fixture data.
     *
     * @param string $key
     * @return array
     */
    protected function getTokenFixture(string $key = 'read_only'): array
    {
        return $this->loadFixture('tokens', $key);
    }
}