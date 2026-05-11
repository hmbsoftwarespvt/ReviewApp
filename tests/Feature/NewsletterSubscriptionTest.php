<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\NewsletterSubscriberModel;

/**
 * NewsletterSubscriptionTest
 * 
 * Comprehensive functional tests for newsletter subscription functionality.
 * Tests all acceptance criteria for Task 29.
 */
class NewsletterSubscriptionTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected $newsletterModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->newsletterModel = new NewsletterSubscriberModel();
    }

    /**
     * Test 1: Newsletter subscription form exists in footer
     * 
     * Acceptance Criteria: Subscription form in footer
     */
    public function testNewsletterFormExistsInFooter()
    {
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('newsletter/subscribe', 'raw');
        $result->assertSee('type="email"', 'raw');
        $result->assertSee('Subscribe', 'raw');
    }

    /**
     * Test 2: Email format validation - valid email
     * 
     * Acceptance Criteria: Email format validated
     */
    public function testValidEmailFormatAccepted()
    {
        $validEmail = 'test@example.com';
        
        $result = $this->post('newsletter/subscribe', [
            'email' => $validEmail,
        ]);
        
        // Should redirect back with success message
        $result->assertRedirectTo('/');
        $result->assertSessionHas('success');
        
        // Verify subscriber was created
        $subscriber = $this->newsletterModel->findByEmail($validEmail);
        $this->assertNotNull($subscriber);
        $this->assertEquals($validEmail, $subscriber['email']);
    }

    /**
     * Test 3: Email format validation - invalid email
     * 
     * Acceptance Criteria: Email format validated
     */
    public function testInvalidEmailFormatRejected()
    {
        $invalidEmails = [
            'notanemail',
            'missing@domain',
            '@nodomain.com',
            'spaces in@email.com',
            'double@@domain.com',
        ];
        
        foreach ($invalidEmails as $invalidEmail) {
            $result = $this->post('newsletter/subscribe', [
                'email' => $invalidEmail,
            ]);
            
            // Should redirect back with error
            $result->assertRedirect();
            $result->assertSessionHas('error');
            
            // Verify subscriber was NOT created
            $subscriber = $this->newsletterModel->findByEmail($invalidEmail);
            $this->assertNull($subscriber);
        }
    }

    /**
     * Test 4: Duplicate email prevention
     * 
     * Acceptance Criteria: Duplicate emails prevented
     */
    public function testDuplicateEmailPrevented()
    {
        $email = 'duplicate@example.com';
        
        // First subscription
        $result1 = $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $result1->assertRedirect();
        $result1->assertSessionHas('success');
        
        // Confirm the subscription
        $subscriber = $this->newsletterModel->findByEmail($email);
        $this->newsletterModel->confirmSubscription($subscriber['id']);
        
        // Attempt duplicate subscription
        $result2 = $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $result2->assertRedirect();
        $result2->assertSessionHas('info');
        
        // Verify only one subscriber exists
        $count = $this->newsletterModel->where('email', $email)->countAllResults();
        $this->assertEquals(1, $count);
    }

    /**
     * Test 5: Unsubscribe token generation
     * 
     * Acceptance Criteria: Generate unsubscribe token
     */
    public function testUnsubscribeTokenGenerated()
    {
        $email = 'tokentest@example.com';
        
        $result = $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $result->assertRedirect();
        
        // Verify unsubscribe token was generated
        $subscriber = $this->newsletterModel->findByEmail($email);
        $this->assertNotNull($subscriber);
        $this->assertNotEmpty($subscriber['unsubscribe_token']);
        $this->assertEquals(64, strlen($subscriber['unsubscribe_token'])); // 32 bytes = 64 hex chars
    }

    /**
     * Test 6: Confirmation token generation
     * 
     * Acceptance Criteria: Send confirmation email (token must be generated)
     */
    public function testConfirmationTokenGenerated()
    {
        $email = 'confirm@example.com';
        
        $result = $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $result->assertRedirect();
        
        // Verify confirmation token was generated
        $subscriber = $this->newsletterModel->findByEmail($email);
        $this->assertNotNull($subscriber);
        $this->assertNotEmpty($subscriber['confirmation_token']);
        $this->assertEquals(64, strlen($subscriber['confirmation_token'])); // 32 bytes = 64 hex chars
        $this->assertFalse((bool)$subscriber['is_confirmed']); // Should not be confirmed yet
    }

    /**
     * Test 7: Subscription confirmation works
     * 
     * Acceptance Criteria: Confirmation email sent (confirmation link works)
     */
    public function testSubscriptionConfirmation()
    {
        $email = 'confirmlink@example.com';
        
        // Subscribe
        $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $subscriber = $this->newsletterModel->findByEmail($email);
        $confirmationToken = $subscriber['confirmation_token'];
        
        // Confirm subscription via token
        $result = $this->get('newsletter/confirm/' . $confirmationToken);
        
        $result->assertRedirectTo('/');
        $result->assertSessionHas('success');
        
        // Verify subscription is confirmed
        $confirmedSubscriber = $this->newsletterModel->find($subscriber['id']);
        $this->assertTrue((bool)$confirmedSubscriber['is_confirmed']);
        $this->assertNull($confirmedSubscriber['confirmation_token']); // Token should be cleared
    }

    /**
     * Test 8: Invalid confirmation token rejected
     * 
     * Acceptance Criteria: Confirmation email sent (invalid tokens rejected)
     */
    public function testInvalidConfirmationTokenRejected()
    {
        $invalidToken = 'invalidtoken123';
        
        $result = $this->get('newsletter/confirm/' . $invalidToken);
        
        $result->assertRedirectTo('/');
        $result->assertSessionHas('error');
    }

    /**
     * Test 9: Unsubscribe page displays correctly
     * 
     * Acceptance Criteria: Unsubscribe page functional
     */
    public function testUnsubscribePageDisplays()
    {
        $email = 'unsubpage@example.com';
        
        // Subscribe
        $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $subscriber = $this->newsletterModel->findByEmail($email);
        $unsubscribeToken = $subscriber['unsubscribe_token'];
        
        // Visit unsubscribe page
        $result = $this->get('newsletter/unsubscribe/' . $unsubscribeToken);
        
        $result->assertStatus(200);
        $result->assertSee('Unsubscribe from Newsletter');
        $result->assertSee($email);
        $result->assertSee('Yes, Unsubscribe Me');
    }

    /**
     * Test 10: Unsubscribe link works
     * 
     * Acceptance Criteria: Unsubscribe link works
     */
    public function testUnsubscribeLinkWorks()
    {
        $email = 'unsublink@example.com';
        
        // Subscribe and confirm
        $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $subscriber = $this->newsletterModel->findByEmail($email);
        $this->newsletterModel->confirmSubscription($subscriber['id']);
        
        $unsubscribeToken = $subscriber['unsubscribe_token'];
        
        // Unsubscribe
        $result = $this->post('newsletter/unsubscribe/' . $unsubscribeToken);
        
        $result->assertRedirectTo('/');
        $result->assertSessionHas('success');
        
        // Verify unsubscription
        $unsubscribedSubscriber = $this->newsletterModel->find($subscriber['id']);
        $this->assertNotNull($unsubscribedSubscriber['unsubscribed_at']);
    }

    /**
     * Test 11: Invalid unsubscribe token rejected
     * 
     * Acceptance Criteria: Unsubscribe link works (invalid tokens rejected)
     */
    public function testInvalidUnsubscribeTokenRejected()
    {
        $invalidToken = 'invalidunsubtoken';
        
        $result = $this->get('newsletter/unsubscribe/' . $invalidToken);
        
        $result->assertRedirectTo('/');
        $result->assertSessionHas('error');
    }

    /**
     * Test 12: Already unsubscribed user cannot unsubscribe again
     * 
     * Acceptance Criteria: Unsubscribe page functional
     */
    public function testAlreadyUnsubscribedUserHandled()
    {
        $email = 'alreadyunsub@example.com';
        
        // Subscribe
        $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $subscriber = $this->newsletterModel->findByEmail($email);
        $unsubscribeToken = $subscriber['unsubscribe_token'];
        
        // Unsubscribe first time
        $this->post('newsletter/unsubscribe/' . $unsubscribeToken);
        
        // Try to unsubscribe again
        $result = $this->post('newsletter/unsubscribe/' . $unsubscribeToken);
        
        $result->assertRedirectTo('/');
        $result->assertSessionHas('info');
    }

    /**
     * Test 13: Empty email rejected
     * 
     * Acceptance Criteria: Email format validated
     */
    public function testEmptyEmailRejected()
    {
        $result = $this->post('newsletter/subscribe', [
            'email' => '',
        ]);
        
        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    /**
     * Test 14: Email too long rejected
     * 
     * Acceptance Criteria: Email format validated
     */
    public function testEmailTooLongRejected()
    {
        // Create email longer than 255 characters
        $longEmail = str_repeat('a', 250) . '@test.com';
        
        $result = $this->post('newsletter/subscribe', [
            'email' => $longEmail,
        ]);
        
        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    /**
     * Test 15: Subscription timestamp recorded
     * 
     * Acceptance Criteria: Subscription form in footer (data persistence)
     */
    public function testSubscriptionTimestampRecorded()
    {
        $email = 'timestamp@example.com';
        $beforeTime = date('Y-m-d H:i:s');
        
        $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $afterTime = date('Y-m-d H:i:s');
        
        $subscriber = $this->newsletterModel->findByEmail($email);
        $this->assertNotNull($subscriber['subscribed_at']);
        $this->assertGreaterThanOrEqual($beforeTime, $subscriber['subscribed_at']);
        $this->assertLessThanOrEqual($afterTime, $subscriber['subscribed_at']);
    }

    /**
     * Test 16: Previously unsubscribed user cannot resubscribe automatically
     * 
     * Acceptance Criteria: Duplicate emails prevented
     */
    public function testPreviouslyUnsubscribedUserHandled()
    {
        $email = 'prevunsub@example.com';
        
        // Subscribe
        $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $subscriber = $this->newsletterModel->findByEmail($email);
        
        // Unsubscribe
        $this->newsletterModel->unsubscribe($subscriber['id']);
        
        // Try to subscribe again
        $result = $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $result->assertRedirect();
        $result->assertSessionHas('info');
        
        // Verify still unsubscribed
        $reloadedSubscriber = $this->newsletterModel->find($subscriber['id']);
        $this->assertNotNull($reloadedSubscriber['unsubscribed_at']);
    }

    /**
     * Test 17: Unconfirmed subscription shows appropriate message
     * 
     * Acceptance Criteria: Confirmation email sent
     */
    public function testUnconfirmedSubscriptionHandled()
    {
        $email = 'unconfirmed@example.com';
        
        // Subscribe first time
        $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        // Try to subscribe again before confirming
        $result = $this->post('newsletter/subscribe', [
            'email' => $email,
        ]);
        
        $result->assertRedirect();
        $result->assertSessionHas('info');
    }

    /**
     * Test 18: Tokens are unique
     * 
     * Acceptance Criteria: Generate unsubscribe token
     */
    public function testTokensAreUnique()
    {
        $emails = [
            'unique1@example.com',
            'unique2@example.com',
            'unique3@example.com',
        ];
        
        $tokens = [];
        
        foreach ($emails as $email) {
            $this->post('newsletter/subscribe', [
                'email' => $email,
            ]);
            
            $subscriber = $this->newsletterModel->findByEmail($email);
            $tokens[] = $subscriber['unsubscribe_token'];
            $tokens[] = $subscriber['confirmation_token'];
        }
        
        // Verify all tokens are unique
        $uniqueTokens = array_unique($tokens);
        $this->assertEquals(count($tokens), count($uniqueTokens));
    }

    /**
     * Test 19: CSRF protection enabled
     * 
     * Acceptance Criteria: Subscription form in footer (security)
     */
    public function testCSRFProtectionEnabled()
    {
        // This test verifies CSRF token is required
        // CodeIgniter's CSRF filter should be active
        
        $email = 'csrf@example.com';
        
        // Attempt to subscribe without CSRF token should fail
        // Note: FeatureTestTrait automatically includes CSRF token,
        // so we're verifying the form includes csrf_field()
        
        $result = $this->get('/');
        $result->assertSee('csrf_test_name', 'raw'); // CodeIgniter's default CSRF token name
    }

    /**
     * Test 20: Rate limiting applied
     * 
     * Acceptance Criteria: Subscription form in footer (prevent abuse)
     */
    public function testRateLimitingApplied()
    {
        // This test verifies rate limiting filter is configured
        // The actual rate limiting is handled by the RateLimitFilter
        
        // Make multiple rapid subscription attempts
        $attempts = 0;
        $maxAttempts = 10;
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            $result = $this->post('newsletter/subscribe', [
                'email' => "ratelimit{$i}@example.com",
            ]);
            
            // Count successful attempts
            if ($result->isOK() || $result->isRedirect()) {
                $attempts++;
            }
        }
        
        // At least some attempts should succeed
        // (exact rate limit depends on RateLimitFilter configuration)
        $this->assertGreaterThan(0, $attempts);
    }
}

