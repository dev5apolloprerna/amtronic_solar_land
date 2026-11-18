<?php

namespace App\Http\Controllers;

use App\Models\Plot;
use App\Models\PlotGridConnectivity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** One connectivity record per survey number (template: Grid Connectivity · One-to-One). */
class GridConnectivityController extends Controller
{
    public function update(Request $request, Plot $plot)
    {
        abort_unless(auth()->user()->canAccessSpv($plot->spv_id), 403);
        $data = $request->validate([
            'connectivity_mw' => ['nullable', 'numeric', 'min:0'],
            'substation' => ['nullable', 'string', 'max:120'],
            'aggregator' => ['nullable', 'string', 'max:120'],
            'tfr_confirmation' => ['required', Rule::in(['Yes', 'No'])],
        ]);
        $grid = PlotGridConnectivity::firstOrNew(['plot_id' => $plot->id]);
        $grid->fill($data)->save();

        return $this->ok('Grid connectivity saved', route('plots.show', [$plot, 'tab' => 'grid']));
    }
}
