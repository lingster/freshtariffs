<?php
/**
 * File: UserTransformer.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class UserTransformer extends \League\Fractal\TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->user_id,
            'name' => $user->username,
            'emails' => [
                'primary' => $user->email,
                'cc' => empty($user->email_cc) ? null : $user->email_cc,
                'bcc' => empty($user->email_bcc) ? null : $user->email_bcc
            ],
            'role' => $user->role,
            'created_at' => $user->created_at
        ];
    }
}