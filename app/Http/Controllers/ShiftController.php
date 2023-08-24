<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $shift = Shift::all();
        return response()->json($shift, 200);
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
                'name' => 'required|max:255',
                'shift_start' => 'required',
                'shift_end' => 'required',
            ]);
            Shift::Create($validatedData);
            return response()->json([
                "status" => "success",
                "message" => "Driver added"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot add shift"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Shift $shift)
    {
        //
        return response()->json($shift, 200);
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
    public function update(Request $request, Shift $shift)
    {
        //
        try {
            $validatedData = $request->validate([
                'number' => 'integer|required',
                'name' => 'required|max:255',
                'shift_start' => 'required',
                'shift_end' => 'required',
            ]);
            Shift::where('id', $shift->id)->update($validatedData);
            return response()->json([
                "status" => "success",
                "message" => "Edit id " . $shift->id . " success"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot edit shift"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shift $shift)
    {
        //
        try {
            Shift::where('id', $shift->id)->delete();
            return response()->json([
                "status" => "success",
                "message" => "Delete id " . $shift->id . " success"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot delete shift"
            ], 400);
        }
    }

    public function shiftList(Request $request)
    {
        // dd($request);
        $columns = array(
            0 => 'id',
            1 => 'number',
            2 => 'name',
            3 => 'shift_start',
            4 => 'shift_end',
            5 => 'created_at',
            6 => 'updated_at',
        );
        $collection = DB::table('shifts');
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
                $nestedData[] = $row->name;
                $nestedData[] = $row->shift_start;
                $nestedData[] = $row->shift_end;
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
