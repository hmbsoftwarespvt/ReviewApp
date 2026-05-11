<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NewsletterSubscriberModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * NewsletterController
 * 
 * Handles newsletter subscription and unsubscription functionality.
 */
class NewsletterController extends BaseController
{
    protected $newsletterModel;

    public function __construct()
    {
        $this->newsletterModel = new NewsletterSubscriberModel();
    }

    /**
     * Subscribe to newsletter
     * 
     * Validates email, checks for duplicates, generates tokens, and creates subscription.
     * TODO: Task 33 will implement actual email sending for confirmation.
     */
    public function subscribe()
    {
        // Validate email format
        $rules = [
            'email' => 'required|valid_email|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please provide a valid email address.');
        }

        $email = $this->request->getPost('email');

        // Check for duplicate subscription
        $existingSubscriber = $this->newsletterModel->findByEmail($email);

        if ($existingSubscriber) {
            // Check if already confirmed
            if ($existingSubscriber['is_confirmed'] && empty($existingSubscriber['unsubscribed_at'])) {
                return redirect()->back()
                    ->with('info', 'This email is already subscribed to our newsletter.');
            }

            // Check if previously unsubscribed
            if (!empty($existingSubscriber['unsubscribed_at'])) {
                return redirect()->back()
                    ->with('info', 'This email was previously unsubscribed. Please contact support to resubscribe.');
            }

            // If not confirmed, resend confirmation
            return redirect()->back()
                ->with('info', 'A confirmation email has already been sent to this address. Please check your inbox.');
        }

        // Generate unique unsubscribe token
        $unsubscribeToken = bin2hex(random_bytes(32));
        $confirmationToken = bin2hex(random_bytes(32));

        // Create newsletter subscriber record
        $data = [
            'email'              => $email,
            'unsubscribe_token'  => $unsubscribeToken,
            'confirmation_token' => $confirmationToken,
            'is_confirmed'       => false,
            'subscribed_at'      => date('Y-m-d H:i:s'),
        ];

        try {
            $subscriberId = $this->newsletterModel->insert($data);

            if ($subscriberId) {
                // TODO: Task 33 - Send confirmation email
                // $this->sendConfirmationEmail($email, $confirmationToken);
                
                return redirect()->back()
                    ->with('success', 'Thank you for subscribing! A confirmation email has been sent to your address.');
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to subscribe. Please try again later.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Newsletter subscription error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred. Please try again later.');
        }
    }

    /**
     * Confirm subscription via token
     * 
     * Confirms the subscription when user clicks the confirmation link in email.
     */
    public function confirm(string $token = null)
    {
        if (empty($token)) {
            return redirect()->to('/')
                ->with('error', 'Invalid confirmation link.');
        }

        $subscriber = $this->newsletterModel->findByConfirmationToken($token);

        if (!$subscriber) {
            return redirect()->to('/')
                ->with('error', 'Invalid or expired confirmation link.');
        }

        if ($subscriber['is_confirmed']) {
            return redirect()->to('/')
                ->with('info', 'Your subscription is already confirmed.');
        }

        // Confirm subscription
        $success = $this->newsletterModel->confirmSubscription($subscriber['id']);

        if ($success) {
            return redirect()->to('/')
                ->with('success', 'Your subscription has been confirmed! You will now receive scam alerts.');
        } else {
            return redirect()->to('/')
                ->with('error', 'Failed to confirm subscription. Please try again.');
        }
    }

    /**
     * Unsubscribe page
     * 
     * Displays unsubscribe confirmation page.
     */
    public function unsubscribePage(string $token = null)
    {
        if (empty($token)) {
            return redirect()->to('/')
                ->with('error', 'Invalid unsubscribe link.');
        }

        $subscriber = $this->newsletterModel->findByUnsubscribeToken($token);

        if (!$subscriber) {
            return redirect()->to('/')
                ->with('error', 'Invalid unsubscribe link.');
        }

        // Check if already unsubscribed
        if (!empty($subscriber['unsubscribed_at'])) {
            return redirect()->to('/')
                ->with('info', 'You have already unsubscribed from our newsletter.');
        }

        // Display unsubscribe confirmation page
        $data = [
            'title'      => 'Unsubscribe from Newsletter',
            'subscriber' => $subscriber,
            'token'      => $token,
        ];

        return view('newsletter/unsubscribe', $data);
    }

    /**
     * Process unsubscribe
     * 
     * Removes the newsletter subscription.
     */
    public function unsubscribe(string $token = null)
    {
        if (empty($token)) {
            return redirect()->to('/')
                ->with('error', 'Invalid unsubscribe link.');
        }

        $subscriber = $this->newsletterModel->findByUnsubscribeToken($token);

        if (!$subscriber) {
            return redirect()->to('/')
                ->with('error', 'Invalid unsubscribe link.');
        }

        // Check if already unsubscribed
        if (!empty($subscriber['unsubscribed_at'])) {
            return redirect()->to('/')
                ->with('info', 'You have already unsubscribed from our newsletter.');
        }

        // Unsubscribe
        $success = $this->newsletterModel->unsubscribe($subscriber['id']);

        if ($success) {
            return redirect()->to('/')
                ->with('success', 'You have been successfully unsubscribed from our newsletter.');
        } else {
            return redirect()->to('/')
                ->with('error', 'Failed to unsubscribe. Please try again.');
        }
    }

    /**
     * TODO: Task 33 - Implement email sending
     * 
     * This method will be implemented in Task 33 when the NotificationService is created.
     * 
     * @param string $email
     * @param string $confirmationToken
     */
    private function sendConfirmationEmail(string $email, string $confirmationToken): void
    {
        // Placeholder for Task 33
        // Will use NotificationService to send confirmation email
        // Email will contain:
        // - Welcome message
        // - Confirmation link: base_url('newsletter/confirm/' . $confirmationToken)
        // - Unsubscribe link will be added to all emails
    }
}

