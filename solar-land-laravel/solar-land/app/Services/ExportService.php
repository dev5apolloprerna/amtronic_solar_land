<?php

namespace App\Services;

use App\Exports\ArrayExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/** Excel (maatwebsite/excel) and PDF (barryvdh/laravel-dompdf) downloads for every list and report. */
class ExportService
{
    public function download(string $module, string $title, array $headings, iterable $rows, string $format)
    {
        $rows = collect($rows)->map(fn ($r) => array_values((array) $r))->all();
        app(AuditService::class)->export($module, count($rows), strtoupper($format));
        $file = Str::slug($title).'-'.now()->format('Ymd-His');

        if ($format === 'pdf') {
            return Pdf::setOption(['isFontSubsettingEnabled' => true, 'defaultFont' => 'DejaVu Sans'])
                ->loadView('exports.pdf', compact('title', 'headings', 'rows'))
                ->setPaper('a4', count($headings) > 7 ? 'landscape' : 'portrait')
                ->download($file.'.pdf');
        }

        return Excel::download(new ArrayExport($headings, $rows, $title), $file.'.xlsx');
    }
}
