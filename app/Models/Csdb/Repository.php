<?php

namespace App\Models\Csdb;

use Illuminate\Database\Eloquent\Model;

/**
 * saat di buat record nya, akan membuat folder
 * 1. storage/app/private/csdb/<repo_name>/.repo/merge
 * 2. storage/app/private/csdb/<repo_name>/.repo/dmc
 */
class Repository extends Model
{
  /** mengambil semua file yang compressed_path === '' (null) */
  public function files() {}
  /** mengambil semua merge */
  public function merges(){}
  
}
