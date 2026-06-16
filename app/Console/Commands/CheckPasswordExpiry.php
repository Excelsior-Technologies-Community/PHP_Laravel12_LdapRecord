<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LocalLdapUser;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class CheckPasswordExpiry extends Command
{
    protected $signature = 'ldap:check-password-expiry';
    protected $description = 'Check LDAP user password expiry and notify them';

    public function handle()
    {
        $users = LocalLdapUser::all();

        foreach ($users as $user) {
            $ldapUser = LdapUser::findByGuid($user->guid);

            if ($ldapUser && $ldapUser->hasAttribute('pwdLastSet')) {
                $pwdLastSet = $ldapUser->getFirstAttribute('pwdLastSet');
                
                $lastSetTimestamp = \LdapRecord\Models\ActiveDirectory\Entry::convertWindowsTimeToDateTime($pwdLastSet);
                $expiryDate = $lastSetTimestamp->addDays(90);

                if ($expiryDate->isPast()) {
                    $this->info("User {$user->username} password expired!");
                } elseif ($expiryDate->diffInDays(now()) <= 7) {
                    $this->info("User {$user->username} password expiring in 7 days.");
                }
            }
        }
    }
}