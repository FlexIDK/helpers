<?php

namespace One23\Helpers\Traits\ObjectUrl;

use One23\Helpers\Exceptions\ObjectUrl as Exception;

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
            'user' => $user ?: null,
            'pass' => (($user && $pass) ? $pass : null),
        ];
    }

    public function getAuth(): ?array
    {
        $user = $this->getComponent('user');
        $pass = $this->getComponent('pass');

        if ($user) {
            return [
                (string)$user,
                (string)$pass,
            ];
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
        $auth = $this->value2auth($user, $pass, $options);

        //

        $self = $this->self();
        $self
            ->setComponent('user', $auth['user'])
            ->setComponent('pass', $auth['pass']);

        return $self;
    }

    public function getUser(): ?string
    {
        $user = $this->getComponent('user');

        return $user
            ? (string)$user
            : null;
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

        $pass = $this->getComponent('pass');

        return $pass
            ? (string)$pass
            : null;
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
                rawurlencode((string)$components['user']) .
                (($components['pass'] ?? null)
                    ? ':' . rawurlencode((string)$components['pass'])
                    : '') .
                '@'
            )
            : '';
    }
}
