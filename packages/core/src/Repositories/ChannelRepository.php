<?php

declare(strict_types=1);

namespace Shopper\Core\Repositories;

class ChannelRepository extends Repository
{
    public function model(): string
    {
        return config('shopper.models.channel');
    }
}
