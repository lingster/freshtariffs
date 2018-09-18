<?php

use Laravel\Cashier\BillableTrait;
use Laravel\Cashier\BillableInterface;

class User extends Eloquent implements BillableInterface {
    use BillableTrait;

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sys_users';

    protected $primaryKey = 'user_id';

    protected $dates = ['trial_ends_at', 'subscription_ends_at'];

    const ROLE_ADMIN = 'admin';
    const ROLE_COMPANY = 'company';
    const ROLE_CUSTOMER = 'customer';

    const SUBSCRIPTION_TYPE_FREE = 'free';
    const SUBSCRIPTION_TYPE_SMALL = 'small';
    const SUBSCRIPTION_TYPE_LARGE = 'large';

    public function company() {
        return $this->hasOne('Company', 'company_id', 'company_id');
    }

    public function deleteWithCleanup() {
        $pricelistInfos = PricelistInfo::where('user_id', $this->user_id)->groupBy('price_id')->get();
        $pricelistIdsArray = [];
        foreach ($pricelistInfos as $pricelistInfo) {
            unlink(public_path() . '/pricelists/' . $pricelistInfo->filename);
            $pricelistInfo->delete();
            $pricelistIdsArray[] = $pricelistInfo->price_id;
        }

        Pricelist::whereIn('price_id', $pricelistIdsArray)->delete();
        $this->delete();
    }

    public function sendRegisteredEmail($variables = []) {
        $company = $this->company;
        if (!$company) {
            return NULL;
        }

        $date = \Carbon\Carbon::today()->format($company->date_format);
        $emailText = Utils::formatTemplate(Settings::getOption('registered_email_template'), [
            'username' => isset($variables['username']) ? $variables['username'] : $this->username,
            'company' => isset($variables['company']) ? $variables['company'] : $company->name,
            'phone' => isset($variables['phone']) ? $variables['phone'] : $company->phone,
            'address' =>  isset($variables['address']) ? $variables['address'] : nl2br($company->address),
            'date' => isset($variables['date']) ? $variables['date'] : $date,
            'email' => isset($variables['email']) ? $variables['email'] : $this->email,
            'password' => isset($variables['password']) ? $variables['password'] : Input::get('password'),
            'url' => $company->subdomain ? ('http://' . Utils::getSubdomainURL($company->subdomain)) : Config::get('app.url')
        ]);

        $emailText .= Settings::getOption('footer_template'); // append administrators text to all letters

        $emailSubject = Utils::formatTemplate(Settings::getOption('registered_email_subject'), [
            'username' => $this->username,
            'company' => $company->name,
            'date' => $date
        ]);

        $user = $this;

        Mail::send('emails.email', ['body' => $emailText],
            function($message) use ($user, $emailSubject, $company)
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
            }
        );
    }

    public function getSubscriptionType() {
        // if not subscribed return free
        if (!$this->subscribed()) {
            return self::SUBSCRIPTION_TYPE_FREE;
        }

        if ($this->onPlan(self::SUBSCRIPTION_TYPE_SMALL)) {
            return self::SUBSCRIPTION_TYPE_SMALL;
        }

        if ($this->onPlan(self::SUBSCRIPTION_TYPE_LARGE)) {
            return self::SUBSCRIPTION_TYPE_LARGE;
        }

        // this happens immediately after ->subscription()->cancel()
        return self::SUBSCRIPTION_TYPE_FREE;
    }
}
