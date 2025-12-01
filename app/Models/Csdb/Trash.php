<?php

namespace App\Models\Csdb;

use App\Http\Resources\CObjectResource;
use Illuminate\Support\Facades\Storage;

/** @deprecated, diganti dengan Repository */
class Trash extends CObject
{
    protected $table = 'cobjects_trashes';

    protected $fillable = ["owner_type", "owner_id", "filename", "path", "remarks", "expired_at"];

    public function get_path() :string
    {
        return ($this->path ?? "CObjects/" . str_replace("\\", "/", $this->owner_type) . "/". $this->owner_id . "/trash");
    }

    public function post_to_disk(string|null $content = null,  string | null $path = null) :bool
    {
        if(!$path){
            $def_path = parent::default_path();
            $current_path = $this->get_original_path();
            $path = str_replace($def_path, $def_path . "/trash", $current_path . "/" . $this->filename);
        }
        $oldPath = str_replace("/trash", "", $path);
        return Storage::disk('local')->move($oldPath, $path);
    }

    public function restore_to_disk()
    {
        $newpath = $this->get_original_path();
        return Storage::disk('local')->move($this->path, $newpath);
    }

    public function get_original_path()
    {
        return str_replace("/trash", "", $this->path);
    }

    public function toArray()
    {
        $arr = (new CObjectResource($this))->toArray(request());
        $arr["expired_at"] = $this->expired_at;
        return $arr;
    }
}
