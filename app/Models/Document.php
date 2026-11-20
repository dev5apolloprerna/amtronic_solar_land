<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasCode;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** DOCUMENT_REPOSITORY */
class Document extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'DOC';
    public const CODE_PAD = 3;
    public const LINKS = ['Land', 'Survey', 'Farmer', 'Lease', 'Sublease', 'Compliance', 'Payment'];
    protected string $auditModule = 'doc';
    protected ?string $spvPath = 'land';
    protected $fillable = ['land_id', 'linked_entity_type', 'linked_entity_id', 'doc_type', 'doc_title', 'file_name', 'file_path', 'file_size_kb', 'mime_type',
        'version_no', 'is_original_scan', 'uploaded_by'];

    public function auditLabel(): string { return $this->code.' · '.$this->doc_title.' v'.$this->version_no; }
    public function land(): BelongsTo { return $this->belongsTo(Land::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }

    /** Template-style code of the linked record (LND / SRV001 / OWN001 / LSE001 …). */
    public function linkedCode(): string
    {
        if ($this->linked_entity_type === 'Land') {
            return (string) $this->land?->land_ref_no;
        }
        $map = ['Survey' => LandSurvey::class, 'Farmer' => LandOwner::class, 'Lease' => Lease::class, 'Sublease' => Sublease::class, 'Compliance' => Compliance::class, 'Payment' => Payment::class];
        $cls = $map[$this->linked_entity_type] ?? null;

        return $cls ? $cls::CODE_PREFIX.str_pad((string) $this->linked_entity_id, $cls::CODE_PAD, '0', STR_PAD_LEFT) : (string) $this->linked_entity_id;
    }
}
