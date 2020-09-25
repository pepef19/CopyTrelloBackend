<?php


namespace App\Http\Controllers;


use App\Models\BoardCard;
use App\Models\CardActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CardActivityController
{
    //faltaría asociación
    public function create(Request $request)
    {
        $data = $request->all();

        $card = BoardCard::find($data["card_id"]);

        if($card === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "Card does not exist"])->setStatusCode($code);
        }else{
            $activity = new CardActivity($data);

            $activity->save();

            return response()->json($activity);
        }
    }

    public function findById($id)
    {
        $activity = CardActivity::where('id', $id)->first();

        if($activity === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "Activity does not exist"])->setStatusCode($code);
        }
        return response()->json($activity);
    }

    public function findLatestActivityCreated(Request $request)
    {
        $card_id = $request->query()["card_id"];
        $activity_order = CardActivity::where("card_id", $card_id)->max('ordering');
        if(is_null($activity_order)){
            $activity_order = 0;
        }
        return response()->json($activity_order);
    }

    public function findAllActivitiesForCard($card_id)
    {
        //faltaría hacer la asociación

        $activity = CardActivity::where('card_id', $card_id)->get();

        if(count($activity) === 0) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "This card has no activities"])->setStatusCode($code);
        }else{
            return response()->json($activity);
        }
    }

    //faltaría hacer la asociación
    public function update(Request $request, $id)
    {
        $activity = CardActivity::where('id', $id)->first();

        if($activity === null) {
            $code = Response::HTTP_NOT_FOUND;

            return response()->json(["error" => "Activity does not exist"])->setStatusCode($code);
        }else{
            $dataFromTheCardActivityToUpdate = $request->all();

            $activity->update($dataFromTheCardActivityToUpdate);

            return response()->json($activity);
        }
    }

    //faltaría hacer la asociación
    public function delete($id)
    {
        $activity = CardActivity::where('id', $id)->first();

        if($activity === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "Activity does not exist"])->setStatusCode($code);
        }
        $activity->delete();
        return response()->json("Activity deleted!");
    }
}
