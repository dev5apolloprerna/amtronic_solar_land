<?php

namespace App\Http\Controllers;

use App\Models\Park;
use App\Models\Party;
use App\Models\Spv;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/** Report list and the generic report screen: filters, search, pagination, Excel and PDF. */
class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index()
    {
        return view('reports.index', ['catalogue' => $this->reports->catalogue()]);
    }

    public function show(Request $request, string $report)
    {
        $cat = $this->reports->catalogue();
        abort_unless(isset($cat[$report]), 404);
        [$title, $desc, $filters, $dateLabel] = $cat[$report];
        [$headings, $rows, $sum] = $this->reports->run($report, $request);

        if ($export = $this->maybeExport('report', $title, $headings, $this->withTotals($rows, $sum, count($headings)))) {
            return $export;
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $paged = new LengthAwarePaginator($rows->forPage($page, 50)->values(), $rows->count(), 50, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return view('reports.show', [
            'key' => $report, 'title' => $title, 'desc' => $desc, 'filters' => $filters, 'dateLabel' => $dateLabel,
            'headings' => $headings, 'rows' => $paged, 'sum' => $sum, 'totals' => $this->totals($rows, $sum),
            'opts' => [
                'park' => Park::orderBy('park_code')->pluck('park_code', 'id')->all(),
                'spv' => Spv::visibleTo()->orderBy('spv_code')->pluck('spv_code', 'id')->all(),
                'party' => Party::orderBy('party_name')->pluck('party_name', 'id')->all(),
            ],
        ]);
    }

    private function totals($rows, array $sum): array
    {
        $t = [];
        foreach ($sum as $i) {
            $t[$i] = round($rows->sum(fn ($r) => (float) ($r[$i] ?? 0)), 4);
        }

        return $t;
    }

    private function withTotals($rows, array $sum, int $cols)
    {
        if (! $sum || $rows->isEmpty()) {
            return $rows;
        }
        $line = array_fill(0, $cols, '');
        $line[0] = 'TOTAL';
        foreach ($this->totals($rows, $sum) as $i => $v) {
            $line[$i] = $v;
        }

        return $rows->concat([$line]);
    }
}
