<?php

namespace App\Enums;

enum ManifestStatus: string
{
    case PROCESSING = 'processing';
    case FINISHED = 'finished';
    case ERROR = 'error';
}
