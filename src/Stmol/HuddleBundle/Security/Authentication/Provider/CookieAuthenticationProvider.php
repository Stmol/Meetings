<?php

namespace Stmol\HuddleBundle\Security\Authentication\Provider;

use Stmol\HuddleBundle\Security\Authentication\Token\CookieUserToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Stmol\HuddleBundle\Entity\Member;

/**
 * Class CookieAuthenticationProvider
 * @package Stmol\HuddleBundle\Security\Authentication\Provider
 * @author Yury [Stmol] Smidovich
 */
class CookieAuthenticationProvider implements AuthenticationProviderInterface
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return Boolean true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof CookieUserToken;
    }

    /**
     * @param TokenInterface $token
     * @return null|CookieUserToken
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        try {
            /** @var Member $user */
            $user = $this->userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            // TODO (Stmol) check this place!!!
            return new CookieUserToken();
        }

        $authenticatedToken = new CookieUserToken($user->getRoles());
        $authenticatedToken->setUser($user);

        return $authenticatedToken;
    }
}