<?php

namespace LaszloKorte\Resource\Controllers;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;


final class UserProvider implements UserProviderInterface {

	public function __construct($db, $graph) {
		$this->db = $db;
		$this->graph = $graph;
	}

	public function loadUserByUsername($username)
    {

    	
    	$query = implode(' UNION ', array_map(function($auth) {
    		return sprintf('SELECT %s AS username, %s AS password FROM %s WHERE %s = :username', $auth->getLoginColumn(), $auth->getPasswordColumn(), $auth->getTable(), $auth->getLoginColumn());
    	}, $this->graph->getAuthenticators()));

        $stmt = $this->db->prepare($query);
        $stmt->execute([
        	'username' => strtolower($username),
        ]);

        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        return new User($user->username, $user->password, ['ROLE_ADMIN'], true, true, true, true);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
	
}