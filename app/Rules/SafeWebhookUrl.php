<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeWebhookUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (self::resolveAddress((string) $value) === null) {
            $fail(trans('resources.webhook.url-not-allowed'));
        }
    }

    /**
     * Returns a public IP for an https URL, or null when the URL is not allowed (SSRF protection).
     *
     * shortcut: only resolves IPv4 (A records), pin the connection to the returned IP to make that safe.
     */
    public static function resolveAddress(string $url): ?string
    {
        $parts = parse_url($url);
        $host = trim($parts['host'] ?? '', '[]');

        if (($parts['scheme'] ?? null) !== 'https' || $host === '') {
            return null;
        }

        $addresses = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);

        foreach ($addresses as $address) {
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return null;
            }
        }

        return $addresses[0] ?? null;
    }
}
