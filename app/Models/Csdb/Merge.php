<?php

namespace App\Models\Csdb;

use Illuminate\Database\Eloquent\Model;

class Merge extends Model
{
  /** mengambil dari path, bukan compressed_path */
  public function files(){}
  /** mengambil dari prev_merge_id */
  public function previous(){}
}
