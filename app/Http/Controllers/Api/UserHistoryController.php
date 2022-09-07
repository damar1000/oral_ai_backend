<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserHistoryResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UserHistoryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // Get All User History
    $userHistories = UserHistory::find(Auth::user()->id);
    return response()->json([
      'message' => 'Success retrieving data',
      'data' => $userHistories,
      'success' => true
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // Validate Input
    $validator = Validator::make($request->all(), [
      'user_id' => 'required',
      'file_path' => 'required|image|mimes:jpeg,jpg,png|max:2048'
    ]);

    // If Validation Failed
    if ($validator->fails()) {
      return response()->json([
        'message' => $validator->errors(),
        'data' => [],
        'success' => false
      ], 422);
    }

    // Store Image
    if ($request->hasFile('file_path')) {

      // Store image locally
      $image = $request->file('file_path');
      $image->storeAs('public/' . Auth::user()->id . '/history', $image->hashName());

      // Create User History
      $userHistory = UserHistory::create([
        'user_id' => Auth::user()->id,
        'file_path' => $image->hashName()
      ]);
    }

    return response()->json([
      'message' => 'Success storing data',
      'data' => new UserHistoryResource($userHistory),
      'success' => true
    ], 200);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\UserHistory  $userHistory
   * @return \Illuminate\Http\Response
   */
  public function show(UserHistory $userHistory)
  {
    // Get User History With Picture
    $data = UserHistory::find($userHistory->id);

    // Return Response
    $userHistoryResource = new UserHistoryResource($data);

    $img = Storage::disk('public')->get('/' . $userHistoryResource->user_id . '/history/' . $userHistoryResource->file_path);
    return response($img, 200)->header('Content-Type', 'image/jpeg');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\UserHistory  $userHistory
   * @return \Illuminate\Http\Response
   */
  public function edit(UserHistory $userHistory)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\UserHistory  $userHistory
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, UserHistory $userHistory)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\UserHistory  $userHistory
   * @return \Illuminate\Http\Response
   */
  public function destroy(UserHistory $userHistory)
  {
    // Delete User History
    $userHistory->delete();

    // Delete Image
    Storage::delete('public/' . Auth::user()->id . '/history/' . $userHistory->file_path);

    return response()->json([
      'message' => 'Success deleting data',
      'data' => [],
      'success' => true
    ]);
  }
}
