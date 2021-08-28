<?php

declare(strict_types=1);

namespace Webauthn\Bundle\Tests\Functional;

use function Safe\sprintf;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    public function __construct(private PublicKeyCredentialUserEntityRepository $userRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->userRepository->findOneByUsername($username);
        if (!$user instanceof User) {
            throw new UserNotFoundException(sprintf('The user with username "%s" cannot be found', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneByUserHandle($identifier);
        if (!$user instanceof User) {
            throw new UserNotFoundException(sprintf('The user with identifier "%s" cannot be found', $identifier));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}
