<?php

namespace App\Models\Csdb;

use Illuminate\Database\Eloquent\Model;

/**
 * Kalau ada 'compressed_path', maka akan mengambil data dari sana, bukan dari column 'path'
 */
class File extends Model
{
  /**
   * mengambil dari merge table (merge_id)
   */
  public function merge(){}
}
