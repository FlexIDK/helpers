<?php

namespace One23\Helpers\Traits\ObjectUrl;

use One23\Helpers\Exceptions\Url as Exception;

/**
 * @method getOptions
 *
 * @property array $components
 */
trait Auth
{
    /**
     * @param array{
     *     minHostLevel: int,
     *     acceptIp: ?bool
     * } $options
     */
    protected function value2auth(
        ?string $user = null,
        ?string $pass = null,
        array $options = [],
    ): array {
        $options = $this->getOptions($options);

        //

        if (is_bool($options['acceptAuth'] ?? null)) {
            if (
                $options['acceptAuth']
                && ! $user
            ) {
                throw new Exception('Undefined user', Exception::DENY_WITHOUT_USER);
            }

            if (
                ! $options['acceptAuth']
                && $user
            ) {
                throw new Exception('User is not allowed', Exception::DENY_WITH_USER);
            }
        }

        return [
            'user' => $user ? rawurldecode($user) : null,
            'pass' => ($user && $pass ? rawurldecode($pass) : null),
        ];
    }

    public function getAuth(): ?array
    {
        $user = $this->components['user'] ?? null;
        $pass = $this->components['pass'] ?? null;

        if ($user) {
            return [$user, $pass];
        }

        return null;
    }

    public function hasAuth(): bool
    {
        return ! is_null($this->getAuth());
    }

    public function setAuth(
        ?string $user = null,
        ?string $pass = null,
        array $options = [],
    ): static {
        $options = $this->getOptions($options);

        //

        $auth = $this->value2auth($user, $pass, $options);

        $this->components['user'] = $auth['user'];
        $this->components['pass'] = $auth['pass'];

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->components['user'] ?? null;
    }

    public function hasUser(): bool
    {
        return (bool)$this->getUser();
    }

    public function getPass(): ?string
    {
        if (! $this->getUser()) {
            return null;
        }

        return $this->components['pass'] ?? null;
    }

    public function hasPass(): bool
    {
        return (bool)$this->getPass();
    }

    protected function auth2build(
        array $components,
    ): string {
        return ($components['user'] ?? null)
            ? (
                rawurlencode($components['user']) .
                (($components['pass'] ?? null)
                    ? ':' . rawurlencode($components['pass'])
                    : '') .
                '@'
            )
            : '';
    }
}
