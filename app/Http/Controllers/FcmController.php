<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FcmController extends Controller
{

    // protected $messaging;
    // public function __construct()
    // {
    //     $this->messaging = Firebase::messaging();
    // }

    // public function storeToken(Request $request)
    // {
    //     Log::info("store token", ['user_id' => auth()->id(), 'token' => $request->token]);

    //     $request->validate([
    //         'token' => 'required|string'
    //     ]);

    //     if (auth()->check()) {
    //         DeviceToken::query()->updateOrCreate([
    //             'token' => $request->token,
    //         ], [
    //             'user_id' => auth()->id()
    //         ]);
    //     }

    //     return response()->json(['status' => 'stored']);
    // }

    // public function send()
    // {

    //     $tokens = DeviceToken::pluck('token')->toArray();

    //     $this->sendToMany(
    //         $tokens,
    //         'Order Update',
    //         'Your order has been shipped'
    //     );

    //     return response()->json("test");
    // }

    // public function sendToToken($token, $title, $body, $data = [])
    // {

    //     $message = CloudMessage::new()->toToken($token)
    //         ->withNotification(Notification::create($title, $body))
    //         ->withData($data);


    //     return $this->messaging->send($message);
    // }

    // public function sendToMany(array $tokens, $title, $body)
    // {
    //     // dd($tokens);
    //     $message = CloudMessage::new()
    //         ->withNotification(Notification::create($title, $body));

    //     $response = $this->messaging->sendMulticast($message, $tokens);

    //     $invalidTokens = $response->invalidTokens();      // malformed
    //     $unknownTokens = $response->unknownTokens();      // NotRegistered

    //     // دمجهم مع بعض
    //     $tokensToDelete = array_merge($invalidTokens, $unknownTokens);

    //     if (!empty($tokensToDelete)) {
    //         DeviceToken::whereIn('token', $tokensToDelete)->delete();
    //     }
    //     return true;

    // }

    // public function sendToTopic($topic, $title, $body)
    // {
    //     $message = CloudMessage::new()->toTopic($topic)
    //         ->withNotification(Notification::create($title, $body));

    //     return $this->messaging->send($message);
    // }
}
