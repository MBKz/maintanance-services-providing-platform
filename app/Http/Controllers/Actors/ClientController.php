<?php


namespace App\Http\Controllers\Actors;

use App\Http\Controllers\Controller;
use App\Http\Interface\Actors\ClientInterface;
use App\Models\Client;

class ClientController extends Controller implements ClientInterface
{

    public function getAllClient() 
    {
        $client  = Client::with('user')->get();

        if ($client == null) {
            return response()->json([
                "message" => "Not Found Client"
            ], 422);
        }

        return response()->json([
            "success" => true,
            "message" => "Clients List",
            "data" => $client
        ]);
    }

}
