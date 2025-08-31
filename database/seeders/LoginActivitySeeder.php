<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoginActivity;
use App\Models\User;

class LoginActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/119.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',
        ];

        $ipAddresses = [
            '192.168.1.100',
            '192.168.1.101',
            '192.168.1.102',
            '10.0.0.50',
            '172.16.0.25',
        ];

        $statuses = ['success', 'failed', 'success', 'success', 'failed'];

        foreach ($users as $user) {
            // Create 5-10 login activities per user
            $activityCount = rand(5, 10);
            
            for ($i = 0; $i < $activityCount; $i++) {
                $status = $statuses[array_rand($statuses)];
                $userAgent = $userAgents[array_rand($userAgents)];
                $ipAddress = $ipAddresses[array_rand($ipAddresses)];
                
                LoginActivity::create([
                    'user_id' => $user->id,
                    'status' => $status,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'location' => $this->getRandomLocation(),
                    'device_type' => $this->getDeviceType($userAgent),
                    'browser' => $this->getBrowser($userAgent),
                    'platform' => $this->getPlatform($userAgent),
                    'failure_reason' => $status === 'failed' ? 'Invalid credentials' : null,
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                ]);
            }
        }

        $this->command->info('Login activities seeded successfully!');
    }

    /**
     * Get a random location.
     */
    private function getRandomLocation(): string
    {
        $locations = [
            'New York, USA',
            'London, UK',
            'Tokyo, Japan',
            'Sydney, Australia',
            'Toronto, Canada',
            'Berlin, Germany',
            'Paris, France',
            'Dubai, UAE',
            'Singapore',
            'Mumbai, India',
        ];

        return $locations[array_rand($locations)];
    }

    /**
     * Get device type from user agent.
     */
    private function getDeviceType(string $userAgent): string
    {
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($userAgent))) {
            return 'tablet';
        }
        
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * Get browser from user agent.
     */
    private function getBrowser(string $userAgent): string
    {
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            return 'Opera';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            return 'Edge';
        }
        
        return 'Unknown';
    }

    /**
     * Get platform from user agent.
     */
    private function getPlatform(string $userAgent): string
    {
        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            return 'Mac';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iOS/i', $userAgent)) {
            return 'iOS';
        }
        
        return 'Unknown';
    }
}
