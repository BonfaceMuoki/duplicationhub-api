<?php

namespace App\Enums;

enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case JOINED = 'joined';
    case JOINING_LINK_SHARED = 'joining_link_shared';
    case ADVERTISEMENT_LINK_SHARED = 'advertisement_link_shared';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'New Lead',
            self::CONTACTED => 'Contacted',
            self::JOINED => 'Joined Platform',
            self::JOINING_LINK_SHARED => 'Joining Page Link Shared',
            self::ADVERTISEMENT_LINK_SHARED => 'Advertisement Page Link Shared',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NEW => 'blue',
            self::CONTACTED => 'yellow',
            self::JOINED => 'green',
            self::JOINING_LINK_SHARED => 'purple',
            self::ADVERTISEMENT_LINK_SHARED => 'indigo',
        };
    }
} 