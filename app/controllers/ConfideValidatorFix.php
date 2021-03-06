<?php

use Zizaco\Confide\ConfideUserInterface;

class ConfideValidatorFix extends Zizaco\Confide\UserValidator {

    public $rules = [
        'create' => [
            'username' => 'alpha_dash',
            'password' => 'required|min:8',
        ],
        'update' => [
            'username' => 'alpha_dash',
            'password' => 'required|min:8',
        ]
    ];
    
    /**
     * Validates if the given user is unique. If there is another
     * user with the same credentials but a different id, this
     * method will return false.
     *
     * @param ConfideUserInterface $user
     *
     * @return boolean True if user is unique.
     */
    public function validateIsUnique(ConfideUserInterface $user)
    {
        $identity = [
            'username'    => $user->username,
        ];

        foreach ($identity as $attribute => $value) {

            $similar = $this->repo->getUserByIdentity([$attribute => $value]);

            if (!$similar || $similar->getKey() == $user->getKey()) {
                unset($identity[$attribute]);
            } else {
                $this->attachErrorMsg(
                    $user,
                    'confide::confide.alerts.duplicated_credentials',
                    $attribute
                );
            }

        }

        if (!$identity) {
            return true;
        }

        return false;
    }

}

class ConfideUserFix extends Zizaco\Confide\EloquentRepository {

    public function getUserByEmailOrUsername($emailOrUsername) {
        $identity = [
            'username' => $emailOrUsername
        ];
        return $this->getUserByIdentity($identity);
    }

}