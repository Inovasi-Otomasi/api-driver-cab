<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $route = Route::all();
        return response()->json($route, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        try {
            $validatedData = $request->validate([
                'number' => 'integer|required',
                'code' => '',
                'start_point' => '',
                'end_point' => '',
                'complete_route' => '',

            ]);
            $validatedData['coordinates'] = json_encode($request->coordinates);
            Route::Create($validatedData);
            return response()->json([
                "status" => "success",
                "message" => "Route added"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot add route" . ", " . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Route $route)
    {
        //
        return response()->json($route, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Route $route)
    {
        //
        try {
            $validatedData = $request->validate([
                'number' => 'integer|required',
                'code' => 'max:255',
                'start_point' => 'max:255',
                'end_point' => 'max:255',
                'complete_route' => 'max:2048',
                'coordinates' => '',
            ]);
            Route::where('id', $route->id)->update($validatedData);
            return response()->json([
                "status" => "success",
                "message" => "Edit id " . $route->id . " success"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot edit route"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Route $route)
    {
        //
        try {
            Route::where('id', $route->id)->delete();
            return response()->json([
                "status" => "success",
                "message" => "Delete id " . $route->id . " success"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot delete route"
            ], 400);
        }
    }

    public function routeList(Request $request)
    {
        // dd($request);
        $columns = array(
            0 => 'id',
            1 => 'number',
            2 => 'code',
            3 => 'start_point',
            4 => 'end_point',
            5 => 'created_at',
            6 => 'updated_at',
        );
        $collection = DB::table('routes');
        // $collection = DB::table('shifts')->leftJoin('drivers', 'shifts.id', '=', 'drivers.shift_id');;
        // $collection = Driver::with('shift');
        $totalData = $collection->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $totalFiltered = $collection
                ->where(function ($query) use ($columns, $request) {
                    for ($i = 0; $i < sizeof($columns) - 1; $i++) {
                        $column_search = $request->input("columns.$i.search.value");
                        if (!empty($column_search)) {
                            $query->orWhere($columns[$i], 'LIKE', "%{$column_search}%");
                        }
                    }
                    return $query;
                })
                ->count();
            $table = $collection
                //added for single column search
                ->where(function ($query) use ($columns, $request) {
                    for ($i = 0; $i < sizeof($columns) - 1; $i++) {
                        $column_search = $request->input("columns.$i.search.value");
                        if (!empty($column_search)) {
                            $query->orWhere($columns[$i], 'LIKE', "%{$column_search}%");
                        }
                    }
                    return $query;
                })
                //
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            //added for single column search

            //
        } else {
            $search = $request->input('search.value');
            $totalFiltered = $collection->where(function ($query) use ($columns, $search) {
                foreach ($columns as $col) {
                    $query->orWhere($col, 'LIKE', "%{$search}%");
                }
                return $query;
            })
                //added for single column search
                ->where(function ($query) use ($columns, $request) {
                    for ($i = 0; $i < sizeof($columns) - 1; $i++) {
                        $column_search = $request->input("columns.$i.search.value");
                        if (!empty($column_search)) {
                            $query->orWhere($columns[$i], 'LIKE', "%{$column_search}%");
                        }
                    }
                    return $query;
                })
                //
                ->count();
            $table = $collection->where(function ($query) use ($columns, $search) {
                foreach ($columns as $col) {
                    $query->orWhere($col, 'LIKE', "%{$search}%");
                }
                return $query;
            })
                //added for single column search
                ->where(function ($query) use ($columns, $request) {
                    for ($i = 0; $i < sizeof($columns) - 1; $i++) {
                        $column_search = $request->input("columns.$i.search.value");
                        if (!empty($column_search)) {
                            $query->orWhere($columns[$i], 'LIKE', "%{$column_search}%");
                        }
                    }
                    return $query;
                })
                //
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        }

        $data = array();
        if (!empty($table)) {
            foreach ($table as $row) {
                $nestedData = [];
                $nestedData[] = $row->id;
                $nestedData[] = $row->number;
                $nestedData[] = $row->code;
                $nestedData[] = $row->start_point;
                $nestedData[] = $row->end_point;
                $nestedData[] = $row->created_at;
                $nestedData[] = $row->updated_at;
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );
        echo json_encode($json_data);
    }
}
