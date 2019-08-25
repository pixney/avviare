<?php

namespace Pixney\AvviareExtension;

use Pixney\AvviareExtension\Command\Create;
use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

class AvviareExtensionServiceProvider extends AddonServiceProvider
{
    /**
     * The addon Artisan commands.
     *
     * @type array|null
     */
    protected $commands = [
        Create::class
    ];
}
