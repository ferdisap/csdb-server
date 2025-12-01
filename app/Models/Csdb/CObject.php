<?php

namespace App\Models\Csdb;

use App\Http\Resources\CObjectResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/** @deprecated, diganti dengan Repository */
class CObject extends Model
{
    protected $table = 'cobjects';

    protected $cast = [
        "remarks" => "json",
    ];

    protected $fillable = ["owner_type", "owner_id", "filename", "path", "remarks"];

    protected function filename(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtoupper($value)
        );
    }

    protected function path(): Attribute
    {
        return Attribute::make(
            // set: fn(mixed $value) => $value ? $value : $this->full_path(),
            set: fn(mixed $value) => $value ? $value : $this->get_path(),
        );
    }

    public function owner()
    {
        // return $this->morphedByMany(User::class, 'owner', null, 'owner_type', 'owner_id', 'Use');
        // return $this->morphMany(User::class, 'owner');
        // return $this->morphedByMany(User::class, 'owner', 'users', 'id', 'owner_id');
        return $this->morphTo();
    }

    public function read(): array
    {
        $code = $this->code;
        if (substr($code, 0, 3) === 'ICN') {
            // $mime = ...
            return [];
        } else {
            $filename = $this->path . "/" . strtoupper($code . ".xml");
            $file = $this->get_from_disk();
            return ["text/xml", $file];
        }
    }

    public function toArray()
    {
        return (new CObjectResource($this))->toArray(request());
    }

    public function post_to_disk(string $content,  string | null $path = null): bool
    {
        return Storage::disk('local')->put($path ?? $this->get_path() . "/" . $this->filename, $content);
    }

    public function remove_from_disk()
    {
        Storage::disk("local")->delete($this->path . "/" . $this->filename);
    }

    public function get_from_disk(): string
    {
        return Storage::disk('local')->get($this->get_path() . "/" . $this->filename);
    }

    public function get_path(): string
    {
        return ($this->path ?? "CObjects/" . str_replace("\\", "/", $this->owner_type) . "/" . $this->owner_id);
    }

    protected function default_path(): string
    {
        return "CObjects/" . str_replace("\\", "/", $this->owner_type) . "/" . $this->owner_id;
    }

    public function change_path(string $new_path): string
    {
        $def_path = parent::default_path();
        return str_replace($def_path, $def_path . "/" . $new_path, $this->path);
    }

    /**
     * @return http respose code 304 (not modified) atau 409 (conflict) atau 0
     */
    public static function is_duplicate(string $filename, mixed $content): int
    {
        // check on db
        if ($cobejct = self::where('filename', $filename)->first()) {
            // check on disk for text/xml
            $full_path = $cobejct->get_path() . DIRECTORY_SEPARATOR . $cobejct->filename;
            if (file_exists($full_path) && (mime_content_type($full_path) === 'text/xml')) {
                $file = $cobejct->get_from_disk();
                if ($file === $content) {
                    return 304;
                }
            }
            // check on disk for icn media
            else if (file_exists($full_path)) {
                return 409;
            }
            return 409;
        }
        return 0;
    }
}
