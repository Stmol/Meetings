<?php

namespace Stmol\HuddleBundle\Security\Firewall;

use Stmol\HuddleBundle\Security\Authentication\Token\CookieUserToken;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Class CookieAuthenticationListener
 * @package Stmol\HuddleBundle\Security\Firewall
 * @author Yury [Stmol] Smidovich
 */
class CookieAuthenticationListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $cookieName;

    /**
     * @param SecurityContextInterface $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     * @param string $cookieName
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        $cookieName
    ) {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->cookieName = $cookieName;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $cookies = $event->getRequest()->cookies;

        $token = new CookieUserToken();
        $this->securityContext->setToken($token);

        // TODO (Stmol) Define global var with value of cookie name
        if (false === $username = $cookies->get($this->cookieName, false)) {
            return;
        }

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof CookieUserToken && $token->isAuthenticated() && $token->getUsername() === $username) {
                return;
            }
        }

        $token->setUser($username);

        $authToken = $this->authenticationManager->authenticate($token);
        $this->securityContext->setToken($authToken);

        return;
    }
}