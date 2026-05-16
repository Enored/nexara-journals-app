<?php

namespace App\Enums;

enum SubmissionFileType: string
{
    case Manuscript = 'manuscript';
    case Supplementary = 'supplementary';
    case Revision = 'revision';
    case ReviewAttachment = 'review_attachment';
}
