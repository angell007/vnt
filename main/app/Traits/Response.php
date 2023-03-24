<?php

namespace App\Traits;

use Illuminate\Http\Response as Respon;

trait Response
{

    public function createResponse($data)
    {

        $page = intval(request('page', 1));
        $limit = intval(request('limit', 5)) ?: 5; // TODO: we mut use max-value for limiter


        $total_rows = count($data);
        $total_pages = ceil($total_rows / $limit);
        $page = ($page > $total_pages ? $total_pages : $page);

        $this->response = [
            "success" =>  true,
            "page" => $page,
            "total_pages" => $total_pages,
            "total_records" =>  $total_rows,
            "per_page" =>  $limit
        ];

        return $this->response;
    }


    public function successPaginate($result, $status = 'success', $message = '', $code = 200)
    {
        return $this->sendResponse((object) $result, $status, $message, $code);
    }

    public function sendResponse($result, $status = 'success', $message = '', $code = 200, $paginate = [])
    {
        if (is_array($result) || $result instanceof \Illuminate\Support\Collection || $result instanceof \Illuminate\Database\Eloquent\Collection) {
            if (!count($paginate)) {
                $paginate = [
                    'page' => 1,
                    'total_pages' => 1,
                    'total_records' => count($result)
                ];
            }
        }

        $response = array_merge(
            [
                'status'    => $status,
                'message'   => $message,
                'data'      => $result
            ],
            $paginate
        );

        return response()->json($response, $code);
    }


    public function success($data, $code = Respon::HTTP_OK)
    {
        return response()->json([
            'status' => true,
            'code' => $code,
            'data' => $data,
            'err' => null
        ])->header('Content-Type', 'application/json');
    }


    public function validResponse($data, $code = Respon::HTTP_OK)
    {
        return response()->json(['data' => $data], $code);
    }


    public function error($message, $code)
    {
        return response()->json([
            'status' => false,
            'code' =>  $code,
            'data' =>  null,
            'err' => $message
        ])->header('Content-Type', 'application/json');
    }

    public function errorResponse($message, $code = 400)
    {
        return response()->json($message, $code)->header('Content-Type', 'application/json');
    }


    public function errorMessage($message, $code)
    {
        return response($message, $code)->header('Content-Type', 'application/json');
    }
}
