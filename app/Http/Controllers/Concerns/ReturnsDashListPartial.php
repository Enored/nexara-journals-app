<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\Request;

trait ReturnsDashListPartial
{
    public const DASH_LIST_PARTIAL_HEADER = 'X-Dash-List-Partial';

    /**
     * @param  array<string, mixed>  $data
     */
    protected function dashListResponse(Request $request, string $partialView, string $fullView, array $data): ViewContract
    {
        if ($request->header(self::DASH_LIST_PARTIAL_HEADER) === '1') {
            return view($partialView, $data);
        }

        return view($fullView, $data);
    }
}
