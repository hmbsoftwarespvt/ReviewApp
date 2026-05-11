<?php

namespace App\Database\Factories;

use App\Models\ScamReportModel;

/**
 * ScamReportFactory
 * 
 * Generates test data for ScamReport model with risk levels.
 */
class ScamReportFactory extends BaseFactory
{
    /**
     * Generate scam report data
     */
    public function make(array $overrides = []): array
    {
        $riskLevel = $this->faker->randomElement(['low', 'medium', 'high']);
        
        $data = [
            'app_id'             => null, // Must be provided
            'user_id'            => null, // Must be provided
            'title'              => $this->generateTitle($riskLevel),
            'description'        => $this->generateDescription($riskLevel),
            'risk_level'         => $riskLevel,
            'evidence_urls'      => json_encode($this->generateEvidenceUrls()),
            'approval_status'    => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'verification_notes' => $this->faker->boolean(40) ? $this->faker->sentence(15) : null,
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate approved scam report
     */
    public function approved(array $overrides = []): array
    {
        return $this->make(array_merge(['approval_status' => 'approved'], $overrides));
    }

    /**
     * Generate pending scam report
     */
    public function pending(array $overrides = []): array
    {
        return $this->make(array_merge(['approval_status' => 'pending'], $overrides));
    }

    /**
     * Generate high risk scam report
     */
    public function highRisk(array $overrides = []): array
    {
        return $this->make(array_merge([
            'risk_level' => 'high',
            'title' => $this->generateTitle('high'),
            'description' => $this->generateDescription('high'),
        ], $overrides));
    }

    /**
     * Generate medium risk scam report
     */
    public function mediumRisk(array $overrides = []): array
    {
        return $this->make(array_merge([
            'risk_level' => 'medium',
            'title' => $this->generateTitle('medium'),
            'description' => $this->generateDescription('medium'),
        ], $overrides));
    }

    /**
     * Generate low risk scam report
     */
    public function lowRisk(array $overrides = []): array
    {
        return $this->make(array_merge([
            'risk_level' => 'low',
            'title' => $this->generateTitle('low'),
            'description' => $this->generateDescription('low'),
        ], $overrides));
    }

    /**
     * Generate verified scam report
     */
    public function verified(array $overrides = []): array
    {
        return $this->make(array_merge([
            'approval_status' => 'approved',
            'verification_notes' => 'Verified by admin team. Evidence confirmed.',
        ], $overrides));
    }

    /**
     * Generate scam report title based on risk level
     */
    protected function generateTitle(string $riskLevel): string
    {
        $highRiskTitles = [
            'Critical Security Vulnerability Detected',
            'Malware Found - Immediate Threat',
            'Data Theft Confirmed',
            'Financial Fraud Alert',
            'Identity Theft Risk',
            'Ransomware Detected',
        ];

        $mediumRiskTitles = [
            'Suspicious Permissions Requested',
            'Potential Privacy Concerns',
            'Misleading Advertising',
            'Unauthorized Data Collection',
            'Questionable Business Practices',
            'Hidden Subscription Charges',
        ];

        $lowRiskTitles = [
            'Minor Privacy Issue',
            'Unclear Terms of Service',
            'Excessive Ads',
            'Potential Tracking Concerns',
            'Unverified Developer',
            'Suspicious Update Behavior',
        ];

        if ($riskLevel === 'high') {
            return $this->faker->randomElement($highRiskTitles);
        } elseif ($riskLevel === 'medium') {
            return $this->faker->randomElement($mediumRiskTitles);
        } else {
            return $this->faker->randomElement($lowRiskTitles);
        }
    }

    /**
     * Generate scam report description (100-3000 chars)
     */
    protected function generateDescription(string $riskLevel): string
    {
        $highRiskDescriptions = [
            'This app contains malicious code that attempts to steal user credentials and financial information. ',
            'Critical security flaw allows unauthorized access to device storage and personal data. ',
            'App is confirmed to be part of a phishing scheme targeting banking information. ',
            'Ransomware detected that encrypts user files and demands payment. ',
        ];

        $mediumRiskDescriptions = [
            'This app requests excessive permissions that are not necessary for its stated functionality. ',
            'The app collects and transmits user data to third parties without proper disclosure. ',
            'Misleading advertising claims that do not match the actual app functionality. ',
            'Hidden subscription charges that are difficult to cancel. ',
        ];

        $lowRiskDescriptions = [
            'The app displays an excessive number of advertisements that disrupt user experience. ',
            'Terms of service are unclear about data collection practices. ',
            'Developer information is incomplete or unverifiable. ',
            'App behavior changed significantly after recent update without notification. ',
        ];

        if ($riskLevel === 'high') {
            $base = $this->faker->randomElement($highRiskDescriptions);
        } elseif ($riskLevel === 'medium') {
            $base = $this->faker->randomElement($mediumRiskDescriptions);
        } else {
            $base = $this->faker->randomElement($lowRiskDescriptions);
        }

        // Add detailed description to meet minimum 100 characters
        $text = $base . 'I discovered this issue while using the app on ' . $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d') . '. ';
        $text .= $this->faker->paragraphs($this->faker->numberBetween(2, 4), true);
        $text .= ' Users should be aware of these concerns before installing or continuing to use this application.';

        // Ensure it's within 100-3000 character range
        if (strlen($text) < 100) {
            $text .= ' ' . $this->faker->paragraph();
        }

        return substr($text, 0, 3000);
    }

    /**
     * Generate evidence URLs (0-5 URLs)
     */
    protected function generateEvidenceUrls(): array
    {
        $count = $this->faker->numberBetween(0, 5);
        $urls = [];

        for ($i = 0; $i < $count; $i++) {
            $urls[] = $this->faker->url();
        }

        return $urls;
    }

    protected function getModel()
    {
        return new ScamReportModel();
    }
}
