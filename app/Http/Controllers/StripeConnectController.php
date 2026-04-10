<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;

class StripeConnectController extends Controller
{
    /**
     * Step 1: Create Stripe Connected Account
     */
    public function createAccount()
{
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    $user = auth()->user();

    // Strong check
    if (!empty($user->stripe_account_id)) {
        return $this->generateOnboardingLink($user->stripe_account_id);
    }

    // Create account
    $account = \Stripe\Account::create([
        'type' => 'express',
        'country' => 'US',
        'email' => $user->email,
    ]);

    // FORCE SAVE (IMPORTANT)
    $user->stripe_account_id = $account->id;
    $user->save();

    // dd($user->stripe_account_id);

    return $this->generateOnboardingLink($account->id);
}
    /**
     * Step 2: Generate Onboarding Link
     */
    public function generateOnboardingLink($accountId)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $link = AccountLink::create([
            'account' => $accountId,
            'refresh_url' => route('admin.stripe.retry'),
            'return_url' => route('admin.stripe.return'),
            'type' => 'account_onboarding',
        ]);

        return redirect($link->url);
    }

    /**
     * Step 3: Return URL (After onboarding complete)
     */
    public function return()
    {
        return redirect()->route('admin.dashboard')->with('success', 'Stripe onboarding completed!');
    }

    /**
     * Step 4: Retry onboarding if incomplete
     */
    public function retry()
    {
        $user = auth()->user();

        if (!$user->stripe_account_id) {
            return redirect()->route('admin.dashboard')->with('error', 'No Stripe account found');
        }

        return $this->generateOnboardingLink($user->stripe_account_id);
    }

    /**
     * Step 5: Check Account Status (IMPORTANT)
     */
    public function status()    
{
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    $user = auth()->user();

    //  Not connected
    if (!$user->stripe_account_id) {
        return response()->json([
            'status' => 'not_connected'
        ]);
    }

    $account = \Stripe\Account::retrieve($user->stripe_account_id);

    return response()->json([
        'status' => 'connected', //  ADD THIS LINE
        'charges_enabled' => $account->charges_enabled,
        'payouts_enabled' => $account->payouts_enabled,
        'details_submitted' => $account->details_submitted,
    ]);
}
}