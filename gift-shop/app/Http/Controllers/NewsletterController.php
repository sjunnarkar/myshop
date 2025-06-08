<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterVerification;
use App\Mail\NewsletterWelcome;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::create([
            'email' => $validated['email'],
            'name' => $validated['name'],
            'user_id' => auth()->id(),
        ]);

        $subscriber->generateVerificationToken();

        Mail::to($subscriber->email)
            ->send(new NewsletterVerification($subscriber));

        return back()->with('success', 'Please check your email to verify your subscription.');
    }

    public function verify($token)
    {
        $subscriber = NewsletterSubscriber::where('verification_token', $token)
            ->whereNull('verified_at')
            ->firstOrFail();

        $subscriber->verify();

        Mail::to($subscriber->email)
            ->send(new NewsletterWelcome($subscriber));

        return redirect()
            ->route('home')
            ->with('success', 'Thank you for verifying your newsletter subscription!');
    }

    public function unsubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('verification_token', $token)
            ->firstOrFail();

        $subscriber->unsubscribe();

        return redirect()
            ->route('home')
            ->with('success', 'You have been successfully unsubscribed from our newsletter.');
    }

    public function updatePreferences(Request $request, NewsletterSubscriber $subscriber)
    {
        $validated = $request->validate([
            'preferences' => ['required', 'array'],
            'preferences.*' => ['boolean'],
        ]);

        $subscriber->updatePreferences($validated['preferences']);

        return back()->with('success', 'Newsletter preferences updated successfully.');
    }

    public function preferences(NewsletterSubscriber $subscriber)
    {
        if ($subscriber->isUnsubscribed()) {
            return redirect()
                ->route('home')
                ->with('error', 'This subscription is no longer active.');
        }

        return view('newsletter.preferences', compact('subscriber'));
    }
} 