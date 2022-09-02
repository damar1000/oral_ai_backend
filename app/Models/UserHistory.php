<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function Termwind\ask;

class UserHistory extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'file_path',
  ];

  
}
