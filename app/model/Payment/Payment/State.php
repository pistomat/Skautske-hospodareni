<?php

declare(strict_types=1);

namespace Model\Payment\Payment;

use Consistence\Enum\Enum;

class State extends Enum
{
    public const CANCELED  = 'canceled';
    public const COMPLETED = 'completed';
    public const PREPARING = 'preparing';
    public const SENT      = 'send';
}
