<?php


namespace App\Http\Controllers;


use App\Models\BoardCard;
use App\Models\BoardList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CardsController extends Controller
{
    //faltaría hacer que solo se pudieran crear cards en las listas del usuario
    public function create(Request $request)
    {
        $data = $request->only(["description", "ordering", "list_id"]);

        $list = BoardList::find($data["list_id"]);

        if ($list === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "List does not exist"])->setStatusCode($code);
        }else{
            $card = new BoardCard($data);

            $card->save();

            return response()->json($card);
        }
    }

    public function findById($id)
    {
        $card = BoardCard::where('id', $id)->first();

        if($card === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "Card does not exist"])->setStatusCode($code);
        }
        return response()->json($card);
    }

    public function findAllCardsForList($list_id)
    {
        //falta hacer que solo se puedan encontrar las cards de el user loggeado

        $card = BoardCard::where('list_id', $list_id)->get();

        if(count($card) === 0) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "This list has no cards"])->setStatusCode($code);
        }else{
            return response()->json($card);
        }
    }

    public function findLatestCardCreated(Request $request)
    {
        $list_id = $request->query()["list_id"];
        $card_order = BoardCard::where("list_id", $list_id)->max('ordering');
        if(is_null($card_order)){
            $card_order = 0;
        }
        return response()->json($card_order);
    }

    //falta hacer la asociación
    public function update(Request $request, $id)
    {
        $card = BoardCard::where('id', $id)->first();

        if($card === null) {
            $code = Response::HTTP_NOT_FOUND;

            return response()->json(["error" => "Card does not exist"])->setStatusCode($code);
        }else{
            $dataFromTheBoardCardToUpdate = $request->all();

            $card->update($dataFromTheBoardCardToUpdate);

            return response()->json($card);
        }
    }

    //asociar las cards al user
    public function delete($id)
    {
        $card = BoardCard::where('id', $id)->first();

        if($card === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "Card does not exist"])->setStatusCode($code);
        }
        $card->delete();

        return response()->json("Card deleted!");
    }
}
