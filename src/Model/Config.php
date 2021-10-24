<?php

namespace Vinorcola\PrivateUserBundle\Model;

use LogicException;
use function array_keys;
use function key_exists;

class Config
{
    /**
     * @var array
     */
    private $userTypes;

    /**
     * @var string
     */
    private $defaultUserType;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->userTypes = $config['types'];
        $this->defaultUserType = $config['default_type'] ?? key($config['types']);
    }

    /**
     * @return string[]
     */
    public function getUserTypes(): array
    {
        return array_keys($this->userTypes);
    }

    /**
     * @param string|null $userType
     * @return string[]
     */
    public function getRoles(string $userType = null): array
    {
        $type = $userType ?? $this->defaultUserType;
        if (!key_exists($type, $this->userTypes)) {
            throw new LogicException('Unknown user type "' . $type . '".');
        }

        return $this->userTypes[$type]['roles'];
    }
}
