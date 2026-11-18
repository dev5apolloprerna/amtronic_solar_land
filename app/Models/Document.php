<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, VisibleBySpv;

    protected string $auditModule = 'document';
    protected ?string $spvPath = 'plot';

    /** Short names used in forms => model class */
    public const LINKABLE = [
        'Plot' => Plot::class,
        'Lease' => Lease::class,
        'Sub-lease' => SubLease::class,
        'Compliance' => ComplianceRecord::class,
        'Payment' => RentPayment::class,
        'Party' => Party::class,
    ];

    protected $fillable = ['plot_id', 'documentable_type', 'documentable_id', 'doc_type', 'title', 'file_path', 'original_name', 'mime_type', 'size_kb',
        'version_no', 'is_original_scan', 'uploaded_by'];

    public function auditLabel(): string
    {
        return $this->title.' v'.$this->version_no;
    }

    public function documentable(): MorphTo { return $this->morphTo(); }
    public function plot(): BelongsTo { return $this->belongsTo(Plot::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function linkedLabel(): string
    {
        return array_search($this->documentable_type, self::LINKABLE, true) ?: class_basename($this->documentable_type);
    }
}
