<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Engineer;
use App\Models\Farmer;
use App\Notifications\replyingConsultation;
use App\Notifications\SendingConsultation;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ConsultationController extends Controller
{
    public function send(Request $request)
{
    $request->validate([
        'engineer_name' => 'required|string',
        'specialty' => 'required|string|in:Irrigation and Drainage Engineering,Agricultural mechanization,Soil engineering',
        'question' => 'required|string',
    ]);

    $engineer = Engineer::whereHas('user', function ($query) use ($request) {
        $query->where('name', $request->engineer_name)
              ->where('role', 'engineer');
    })
    ->where('specialty', $request->specialty)
    ->first();

    if (!$engineer) {
        return response()->json(['error' => 'Engineer with specified name and specialty not found.'], 404);
    }

    $farmer =Farmer::where('user_id', auth()->id())->firstOrFail();

    $consultation =Consultation::create([
        'farmer_id' => $farmer->id,
        'engineer_id' => $engineer->id,
        'question' => $request->question,
        'status' => 'pending',
    ]);
    Notification::send($engineer->user,new SendingConsultation($consultation->id,$farmer->user->name));
    return response()->json([
        'message' => 'تم إرسال الاستشارة بنجاح.',
        'consultation' => $consultation,
    ]);
}

public function reply(Request $request, $id)
{
    $request->validate([
        'answer' => 'required|string',
    ]);

    $consultation = Consultation::findOrFail($id);

    // تحقق أن المهندس الحالي هو صاحب هذه الاستشارة
    $engineer =Engineer::where('user_id', auth()->id())->firstOrFail();
    if ($consultation->engineer_id !== $engineer->id) {
        return response()->json(['error' => 'غير مسموح لك بالرد على هذه الاستشارة.'], 403);
    }

    $consultation->update([
        'answer' => $request->answer,
        'status' => 'answered', // مثلا، تم الرد عليها
    ]);
    $farmer=Farmer::findOrFail($consultation->farmer_id);
    
    Notification::send($farmer->user,new replyingConsultation($consultation->id,$engineer->user->name));

    return response()->json([
        'message' => 'تم الرد على الاستشارة بنجاح.',
        'consultation' => $consultation,
    ]);
}

public function farmerConsultations()
{
    $farmer =Farmer::where('user_id', auth()->id())->firstOrFail();

    $consultations =Consultation::where('farmer_id', $farmer->id)
        ->select('id', 'question', 'status', 'answer')
        ->latest()
        ->get();

    return response()->json($consultations);
}

public function engineerConsultations()
{
    $engineer =Engineer::where('user_id', auth()->id())->firstOrFail();

    $consultations =Consultation::where('engineer_id', $engineer->id)
        
        ->select('id', 'question', 'status')
        ->latest()
        ->get();

    return response()->json($consultations);
}

public function show($id){
    $consultation=Consultation::findOrFail($id);
    return response()->json([
        'data'=>$consultation
    ]);
}




}
