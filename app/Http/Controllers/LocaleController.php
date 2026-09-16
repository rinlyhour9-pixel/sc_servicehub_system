<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (in_array($locale, SetLocale::SUPPORTED, true)) {
            Cookie::queue('locale', $locale, 60 * 24 * 365);
        }

        return back();
    }
}
