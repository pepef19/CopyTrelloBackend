<?php


namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardList;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ListsController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->only(["title", "ordering", "board_id"]);

        $board = Board::find($data["board_id"]);

        if ($board === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "Board does not exist"])->setStatusCode($code);
        } else {
            $list = new BoardList($data);

            $list->save();

            return response()->json($list);
        }
    }

    public function findById($id)
    {
        $list = BoardList::where('id', $id)->first();

        if ($list === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "List does not exist"])->setStatusCode($code);
        }
        return response()->json($list);
    }

    public function findAllListsForBoard($board_id)
    {
        //falta hacer que solo se puedan encontrar las lists de el user loggeado
        $user = $this->getAuthenticatedUser();

        $list = BoardList::where('board_id', $board_id)->get();

        if (count($list) === 0) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "This board has no lists"])->setStatusCode($code);
        } else {
            return response()->json($list);
        }
    }

    public function findLatestListCreated(Request $request)
    {
        $board_id = $request->query()["board_id"];
        $list_order = BoardList::where("board_id", $board_id)->max('ordering');
        if (is_null($list_order)) {
            $list_order = 0;
        }
        return response()->json($list_order);
    }

    public function findNumberOfExistingListsForSelectedBoard(Request $request)
    {
        $board_id = $request->query()["board_id"];
        $existing_lists = BoardList::where("board_id", $board_id)->count('ordering');
        return response()->json($existing_lists);
    }

    //falta hacer la asociación al usuario
    public function update(Request $request, $id)
    {
        //$data = $request->only(["title", "ordering", "board_id"]);

        //$board = Board::find($data["board_id"]);

        $list = BoardList::where('id', $id)/*->where('board_id', $board->id)*/->first();

        if ($list === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "List does not exist"])->setStatusCode($code);
        }else{
            $dataFromTheListToUpdate = $request->all();

            $list->update($dataFromTheListToUpdate);

            return response()->json($list);
        }
    }
    //asociar las listas al user
    public function delete($id)
    {
        $list = BoardList::where('id', $id)->first();

        if($list === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "List does not exist"])->setStatusCode($code);
        }
        $list->delete();

        return response()->json("List deleted!");
    }
}
