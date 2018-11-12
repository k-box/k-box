<?php

namespace KBox\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * The privacy policy content has been changed.
 *
 * When the privacy policy content changes, the user is required to give again the explicit consent
 */
class PrivacyPolicyUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
