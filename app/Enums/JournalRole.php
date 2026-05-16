<?php

namespace App\Enums;

enum JournalRole: string
{
    case Author = 'author';
    case Reviewer = 'reviewer';
    case Editor = 'editor';
    case Admin = 'admin';
}
