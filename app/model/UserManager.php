<?php

namespace App\Model;

use http\Exception\BadUrlException;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;

/**
 * CMS UserManager
 * @package App\Model
 */
class UserManager extends BaseManager implements IAuthenticator
{
    const
        TABLE_NAME = 'user',
        COLUMN_ID = 'user_id',
        COLUMN_NAME = 'username',
        COLUMN_PASSWORD_HASH = 'password',
        COLUMN_ROLE = 'role';

    /**
     * CMS User login
     * @param array $credentials credentials
     * @return Identity identity for next manipulation
     * @throws AuthenticationException Throws if error occurred during login p.g. bad password or nickname
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials; //extraction of credentials

        $user = $this->context->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();

        // User verification
        if (!$user) {
            // Throws exception if user does not exists
            throw new AuthenticationException('This username does not exists.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $user[self::COLUMN_PASSWORD_HASH])) {
            //Throws exception if password is incorrect
            throw new AuthenticationException('This password is not correct.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($user[self::COLUMN_PASSWORD_HASH])) { // Check if there is need to rehash the password
            // Rehash the password
            $user->update(array(self::COLUMN_PASSWORD_HASH => Passwords::hash($password)));
        }

        $userData = $user->toArray(); // User data extract
        unset($userData[self::COLUMN_PASSWORD_HASH]); // Removes password from user data due to security

        // Return a new identity of user
        return new Identity($user[self::COLUMN_ID], $user[self::COLUMN_ROLE], $userData);
    }

    /**
     * Sign up a new user
     * @param string $username username
     * @param string $password password
     * @throws DuplicateNameException If user with that name already exists
     */
    public function register($username, $password)
    {
        try {
            // User DB insertion
            $this->context->table(self::TABLE_NAME)->insert(array(
                self::COLUMN_NAME => $username,
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
            ));
        } catch (UniqueConstraintViolationException $e) {
            // Throws exception if the user with that name already exists
            throw new DuplicateNameException;
        }
    }

    public function findUser(int $id)
    {
        try{
            return $this->context->table(self::TABLE_NAME)
                ->select('username, user_id, profile_pic, description, created_at, role')
                ->where('user_id', $id)->fetch();
        }catch (BadUrlException $e){
            throw new BadUrlException;
        }
    }
}

/**
 * Exception for the name duplicity
 * @package App\Model
 */
class DuplicateNameException extends AuthenticationException
{
    /** Redefine the error message */
    public function __construct()
    {
        parent::__construct();
        $this->message = 'User with that name already exists';
    }
}