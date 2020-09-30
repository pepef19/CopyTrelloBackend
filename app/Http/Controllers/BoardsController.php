<?php


namespace App\Http\Controllers;


use App\Models\Board;
use App\Models\BoardList;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class BoardsController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->only(["title", "ordering"]);

        $user = $this->getAuthenticatedUser();

        $board = new Board($data);

        $user->boards()->save($board);

        return response()->json($board);
    }

    public function findById($id)
    {
        $user = $this->getAuthenticatedUser();

        $board = Board::where('id', $id)->where("user_id", $user->id)->first();

        if ($board === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "Board not accessible to this user"])->setStatusCode($code);
        } else {
            return response()->json($board);
        }
    }

    public function findBoardData($id)
    {
        $user = $this->getAuthenticatedUser();
        $board = Board::where('id', $id)->where("user_id", $user->id)->first();
        $lists = BoardList::with(["cards"])->where('board_id', $id)->orderBy('ordering', 'asc')->get();
        $dataResponse = [
            "board" => $board,
            "lists" => $lists
        ];
        return response()->json($dataResponse);
    }

    public function findLatestBoardCreated(Request $request)
    {
        $user_id = $request->query()["user_id"];
        $board_order = Board::where("user_id", $user_id)->max('ordering');
        if(is_null($board_order)){
            $board_order = 0;
        }
        return response()->json($board_order);
    }


    public function findAllBoardsForLoggedUser(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        //$_limit = $request->query('_limit'); revisar para que haga bien el querystring
        //$_start = $request->query('_start');

        $boards = $user->boards;

        return response()->json($boards);
    }

    public function update(Request $request, $id)
    {

        $user = $this->getAuthenticatedUser();

        $board = Board::where('id', $id)->where("user_id", $user->id)->first();

        if ($board === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "Acceso no permitido a este board"])->setStatusCode($code);
        }else{
            $dataFromTheBoardToUpdate = $request->all();

            $board->update($dataFromTheBoardToUpdate);

            return response()->json($board);
        }
    }

    public function delete($id)
    {
        $user = $this->getAuthenticatedUser();

        $board = Board::where('id', $id)->where("user_id", $user->id)->first();

        if ($board === null) {
            $code = Response::HTTP_NOT_FOUND;
            return response()->json(["error" => "Board not accessible to this user"])->setStatusCode($code);
        }else{
            $board->delete();

            return response()->json("Board deleted!");
        }
    }
}
