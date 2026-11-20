<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  /* DomPDF: DejaVu Sans prints the ₹ sign */
  body{font-family:DejaVu Sans, sans-serif;font-size:9px;color:#1B2220}
  h1{font-size:14px;margin:0 0 2px}
  p{margin:0 0 10px;color:#5D6862}
  table{border-collapse:collapse;width:100%}
  th{background:#ECEEEA;text-align:left;font-size:8.5px;color:#5D6862;padding:5px;border-bottom:1px solid #D2D7D1}
  td{padding:4px 5px;border-bottom:1px solid #E5E8E3;vertical-align:top}
</style>
</head>
<body>
  <h1>{{ $title }}</h1>
  <p>Generated {{ now()->format('d M Y, H:i') }} by {{ auth()->user()->username ?? 'system' }} · {{ count($rows) }} rows</p>
  <table>
    <thead><tr>@foreach ($headings as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
    <tbody>
      @forelse ($rows as $r)
        <tr>@foreach ($r as $c)<td>{{ $c }}</td>@endforeach</tr>
      @empty
        <tr><td colspan="{{ count($headings) }}">No rows.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
