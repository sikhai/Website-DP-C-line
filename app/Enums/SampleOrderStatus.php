<?php

namespace App\Enums;

enum SampleOrderStatus: string
{
    case PENDING = 'pending';
    case ORDERED = 'ordered';
    case RECEIVED = 'received';
}

enum SampleRequestStatus: string
{
    case STATUS_PENDING = 'pending';
    case STATUS_APPROVED = 'approved';
    case STATUS_PICKED_UP = 'picked_up';
    case STATUS_RETURNED = 'returned';
    case STATUS_REJECTED = 'rejected';
}
enum SampleTransactionType: string
{
    case TYPE_IN = 'in';
    case TYPE_OUT = 'out';
}
