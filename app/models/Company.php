<?php
/**
 * File: Company.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class Company extends Eloquent {
    protected $table = 'companies';
    protected $primaryKey = 'company_id';

    const DATE_FORMAT_DDMMYYYY = 'd.m.Y';
    const DATE_FORMAT_MMDDYYYY = 'm/d/Y';
    const DATE_FORMAT_YYYYMMDD = 'Y-m-d';
    const DATE_FORMAT_MDY = 'M d, Y';
    const DATE_FORMAT_DMY = 'd-M-y';

    public function users() {
        return $this->hasMany('User', 'company_id', 'company_id');
    }

    public function deleteWithCleanup() {
        $users = $this->users;
        /** @var User $user */
        foreach ($users as $user) {
            $user->deleteWithCleanup();
        }
        $this->delete();
    }

    public function pricelistsInfo() {
        return $this->hasMany('PricelistInfo', 'company_id', 'company_id');
    }

    public static function getDateFormats() {
        $date = \Carbon\Carbon::now();
        return [
            Company::DATE_FORMAT_DDMMYYYY => $date->format(Company::DATE_FORMAT_DDMMYYYY),
            Company::DATE_FORMAT_MMDDYYYY => $date->format(Company::DATE_FORMAT_MMDDYYYY),
            Company::DATE_FORMAT_YYYYMMDD => $date->format(Company::DATE_FORMAT_YYYYMMDD),
            Company::DATE_FORMAT_MDY      => $date->format(Company::DATE_FORMAT_MDY),
            Company::DATE_FORMAT_DMY      => $date->format(Company::DATE_FORMAT_DMY)
        ];
    }

    public function generateAndSendEmail($userId, $filename = null) {
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            return NULL;
        }

        if (!$filename) {
            $filename = $user->latest_price_filename;
        }

        if (!$filename) {
            return NULL;
        }

        $date = \Carbon\Carbon::parse(Input::get('effective_date'))->format($this->date_format);
        $emailText = Utils::formatTemplate($this->email_template, [
            'username' => $user->username,
            'company' => $this->name,
            'phone' => $this->phone,
            'address' => nl2br($this->address),
            'date' => $date,
            'note' => Input::get('note')
        ]);

        $emailText .= Settings::getOption('footer_template'); // append administrators text to all letters

        $emailSubject = Utils::formatTemplate($this->email_subject, [
            'username' => $user->username,
            'company' => $this->name,
            'list-type' => Input::get('type'),
            'date' => $date
        ]);

        $company = $this;
        Mail::send('emails.email', ['body' => $emailText],
            function($message) use ($user, $filename, $emailSubject, $company)
            {
                $message->from(Config::get('mail.from.address'), $company->name);
                $message->to($user->email);
                if ($user->email_cc) {
                    $message->cc($user->email_cc);
                }

                if ($user->email_bcc) {
                    $message->bcc($user->email_bcc);
                }
                $message->subject($emailSubject);
                $message->replyTo($company->email_reply_to, $company->name);
                $message->attach(public_path('pricelists/') . $filename);
            }
        );
    }
}