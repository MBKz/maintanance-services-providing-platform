<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use App\Http\Interface\Helper\CityInterface;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CityController extends Controller implements CityInterface
{

    public function get_all()
    {
        $cities  = City::get();

        if ($cities == null) {
            return response()->json([
                "message" => "Not Found City"
            ], 422);
        }

        return response()->json([
            "success" => true,
            "message" => "Cities List",
            "data" => $cities
        ]);
    }



    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $city = City::create([
            'name' => $request->name,
        ]);
        return response()->json([
            "success" => true,
            "message" => "City created successfully.",
            "data" => $city
        ]);
    }

    public function show($id)
    {
        $city = City::find($id);

        if ($city == null) {
            return response()->json([
                "message" => "Not Found City"
            ], 422);
        }

        return response()->json([
            "success" => true,
            "message" => "City retrieved successfully.",
            "data" => $city
        ]);
    }


    public function update(Request $request, $id)
    {
        $city = City::find($id);

        if ($city == null) {
            return response()->json([
                "message" => "Not Found City"
            ], 422);
        }

        $validator = Validator::make($request->all, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        if ($request->name != null)  $city['name'] = $request->name;

        $city->update();

        return response()->json([
            "success" => true,
            "message" => "City updated successfully.",
            "data" => $city
        ]);
    }


    public function destroy($id)
    {
        $city = City::where('id', $id)->first();

        if ($city == null) {
            return response()->json([
                "message" => "Not Found City"
            ], 422);
        }
        $city->delete();

        return response()->json([
            "success" => true,
            "message" => "City deleted successfully ",
            "data" => $city
        ]);
    }
}