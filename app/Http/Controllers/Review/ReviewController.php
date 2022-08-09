<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Notifications\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rate' => 'required',
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['error' => $validator->errors()->all()], 422);
        }

        $review = Review::create(['rate' => $request->rate , 'comment' => $request->comment]);
        $order = Order::query()->where('id',$request->order_id)->first();
        $order->update(['review_id' => $review->id]);
        $provider = ServiceProvider::query()->where('id',$order->proposal->service_provider_id)->first();

        $rate = round((($provider->rate * $provider->num_of_raters) + $request->rate) / ($provider->num_of_raters + 1) ,2);
        $provider->update(['rate' => $rate ,'num_of_raters' => $provider->num_of_raters+1]);

        $title = 'جودة و موثوقية العمل';
        $message = 'لقد تم تقييم عملك ذو المعرف #'.$order->id;
        $provider->notify(new SendPushNotification($title,$message,'order history'));
        $user= User::find($provider->user_id);
        $user->notifications()->create([
            'message' => $title,
            'body' => $message,
            'checked' => false,
            'date' => Carbon::now()->addHour(3)
        ]);

        return response()->json([
            "message" => "تم إضافة تقييم",
            "data" => $provider
        ]);
    }

    public function destroy($id){
        $review = Review::query()->where('id',$id)->first();
        $review->delete();
        return response(['message'=>'تم حذف التقييم'],200);
    }

}