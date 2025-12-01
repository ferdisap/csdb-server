<?php

namespace App\Http\Controllers\Csdb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Csdb\Xml\UploadRequest as CsdbUploadRequest;
use App\Models\Csdb\CObject;
use App\Models\Csdb\Object\Xml;
use App\Models\Csdb\Trash;
use Illuminate\Http\Request;
use Ptdi\Mpub\Main\CObject as MpubCObject;

class CObjectController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->per_page ?? 10;
        $user = $request->user();
        $cobjects = $user->cobjects()->with("owner")->paginate($per_page);
        $cobjects = $cobjects->toArray();
        $data = $cobjects["data"];
        return response()->json([
            "csdb" => [
                "objects" => $data,
                "pagination" => [
                    "current_page" => $cobjects["current_page"], // 1,
                    "first_page_url" => $cobjects["first_page_url"], // "http://localhost:1001/csdb-object?page=1",
                    "from" => $cobjects["from"], // 1,
                    "last_page" => $cobjects["last_page"], // 1,
                    "last_page_url" => $cobjects["last_page_url"], // "http://localhost:1001/csdb-object?page=1",
                    // "links" => $cobjects["links"], // [{url: null, label: "&laquo; Previous", page: null, active: false},…],
                    "next_page_url" => $cobjects["next_page_url"], //null,
                    // "path" => $cobjects["path"], // "http://localhost:1001/csdb-object",
                    "per_page" => $cobjects["per_page"], //10,
                    "prev_page_url" => $cobjects["prev_page_url"], // null,
                    "to" => $cobjects["to"], // 1, total file in one page,
                    "total" => $cobjects["total"], // total all file in number           ,
                ]
            ]
        ]);
    }

    public function index_trash(Request $request)
    {
        $per_page = $request->per_page ?? 10;
        $user = $request->user();
        $cobjects = $user->trashes()->with("owner")->paginate($per_page);
        $cobjects = $cobjects->toArray();
        $data = $cobjects["data"];
        return response()->json([
            "csdb" => [
                "objects" => $data,
                "pagination" => [
                    "current_page" => $cobjects["current_page"], // 1,
                    "first_page_url" => $cobjects["first_page_url"], // "http://localhost:1001/csdb-object?page=1",
                    "from" => $cobjects["from"], // 1,
                    "last_page" => $cobjects["last_page"], // 1,
                    "last_page_url" => $cobjects["last_page_url"], // "http://localhost:1001/csdb-object?page=1",
                    // "links" => $cobjects["links"], // [{url: null, label: "&laquo; Previous", page: null, active: false},…],
                    "next_page_url" => $cobjects["next_page_url"], //null,
                    // "path" => $cobjects["path"], // "http://localhost:1001/csdb-object",
                    "per_page" => $cobjects["per_page"], //10,
                    "prev_page_url" => $cobjects["prev_page_url"], // null,
                    "to" => $cobjects["to"], // 1, total file in one page,
                    "total" => $cobjects["total"], // total all file in number           ,
                ]
            ]
        ]);
    }

    public function upload(CsdbUploadRequest $request)
    {
        $file = $request->validated('file');
        $csdbResourceOwner = $request->get('csdb_resource_owner'); // User model

        $mpubCObject = new MpubCObject();
        $mpubCObject->loadXML($file);
        $filename = $mpubCObject->getFilename();

        $is_duplicate = CObject::is_duplicate($filename, $mpubCObject->document->saveXML());
        $updateOrCreate = function () use ($csdbResourceOwner, $filename, $mpubCObject) {
            $xmlObject = CObject::where('filename', $filename)->first();
            if (!$xmlObject) {
                $xmlObject = CObject::create([
                    'owner_type' => $csdbResourceOwner::class,
                    'owner_id' => $csdbResourceOwner->id,
                    'filename' => $filename,
                    'path' => null,
                    'remarks' => '[]',
                ]);
            } else {
                $xmlObject->update([
                    'path' => null,
                    'remarks' => '[]',
                ]);
            }
            if ($xmlObject) {
                $xmlObject->post_to_disk($mpubCObject->document->saveXML());
                $xmlObject->makeHidden(['id', 'created_at', 'updated_at']);
                return response()->json([
                    "message" => "Uploading one CSDB object is success.",
                    "csdb" => [
                        "objects" => [$xmlObject]
                    ]
                ]);
            }
        };
        if (!$is_duplicate) {
            return $updateOrCreate();
        } else if ($is_duplicate === 409) {
            return $request->validated('force') ? $updateOrCreate() : response([
                "message" => "Conflicts while uploading file",
                "csdb" => [
                    "objects" => []
                ]
            ], $is_duplicate);
        } else if ($is_duplicate === 304) {
            return $request->validated('force') ? $updateOrCreate() : response([
                "message" => "Conflicts while uploading file",
                "csdb" => [
                    "objects" => []
                ]
            ], $is_duplicate);
        }
        abort(500);
    }

    public function delete(Request $request, CObject $csdbObject)
    {
        $validated = $request->validate([
            "deleted" => "required|boolean",
            "permanent" => "required|boolean",
        ]);

        if ($validated["deleted"]) {
            $csdbObject->delete();
            if (!$validated["permanent"]) {
                // create db record thrash csdb object
                if (!($trash = Trash::where('filename', $csdbObject->filename)->first())) {
                    $trash = Trash::create([
                        'owner_type' => $csdbObject->owner_type,
                        'owner_id' => $csdbObject->owner_id,
                        'filename' => $csdbObject->filename,
                        'path' => null,
                        'remarks' => $csdbObject->remarks,
                        "expired_at" => now()->addDay(30),
                    ]);
                }
                $trash->post_to_disk();
                $trash->makeHidden(['id', 'created_at', 'updated_at']);
            } else {
                $csdbObject->remove_from_disk();
            }
        }

        $csdbObject->makeHidden(['id', 'created_at', 'updated_at']);
        return response()->json([
            "message" => "Deleting one CSDB object is success.",
            "csdb" => [
                "objects" => [$csdbObject],
                "trashes" => [$trash ?? null],
            ]
        ]);
    }

    public function delete_trash(Request $request, Trash $trash)
    {
        $validated = $request->validate([
            "deleted" => "required|boolean",
        ]);

        if ($validated["deleted"]) {
            $trash->delete();
            $trash->remove_from_disk();
        }

        $trash->makeHidden(['id', 'created_at', 'updated_at']);
        return response()->json([
            "message" => "Deleting one CSDB object is success.",
            "csdb" => [
                "trashes" => [$trash],
            ]
        ]);
    }

    public function restore(Request $request, Trash $trash)
    {
        if (!($cobject = CObject::where('filename', $trash->filename)->first())) {
            $cobject = CObject::create([
                'owner_type' => $trash->owner_type,
                'owner_id' => $trash->owner_id,
                'filename' => $trash->filename,
                'path' => $trash->get_original_path(),
                'remarks' => $trash->remarks,
            ]);
            $trash->restore_to_disk();
        } else {
            return response([
                "message" => "Conflicts while restoring file",
                "csdb" => [
                    "objects" => [$cobject]
                ]
            ], 409);
        }
    }

    public function read(Request $request, CObject $cobject)
    {
        $code = $cobject->code;
        if (substr($code, 0, 3) === 'ICN') {
            // $mime = ...
        } else {
            // $filename
            [$mime, $file] = $cobject->read();
            return response()->json([
                "csdb" => [
                    "object" => $cobject,
                    "mime" => $mime,
                    "file" => $file,
                ]
            ]);
        }
    }

    public function read_trash(Request $request, Trash $cobject)
    {
        $code = $cobject->code;
        if (substr($code, 0, 3) === 'ICN') {
            // $mime = ...
        } else {
            // $filename
            [$mime, $file] = $cobject->read();
            return response()->json([
                "csdb" => [
                    "object" => $cobject,
                    "mime" => $mime,
                    "file" => $file,
                ]
            ]);
        }
    }
}
