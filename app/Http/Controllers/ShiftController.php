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
                'name' => 'required|max:255',
                'remark' => '',
                'date' => 'required',
                'tap_in_time' => 'required',
                'tap_out_time' => 'required',
                // 'shift_start' => 'required',
                // 'shift_end' => 'required',
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
                'name' => '',
                'remark' => '',
                'date' => '',
                'tap_in_time' => '',
                'tap_out_time' => '',
            ]);
            Shift::where('id', $shift->id)->update($validatedData);
            return response()->json([
                "status" => "success",
                "message" => "Edit id " . $shift->id . " success"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot add shift" . ", " . $e->getMessage()
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
            1 => 'name',
            2 => 'date',
            3 => 'tap_in_time',
            4 => 'tap_out_time',
            5 => 'remark',

        );
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        // $collection = DB::table('shifts')
        // ->select($columns)->where([['date', '>=', $start_date], ['date', '<=' . $end_date]])
        // ->select($columns)
        // ->where([
        //     ['shifts.date', '>=' . $start_date],
        //     ['shifts.date', '<=' . $end_date],
        // ]);
        $collection = DB::table('shifts')
        ->where([
            ['shifts.date', '>=', $start_date],
            ['shifts.date', '<=',  $end_date],
        ]);
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
                $nestedData[] = $row->name;
                $nestedData[] = $row->date;
                $nestedData[] = $row->tap_in_time;
                $nestedData[] = $row->tap_out_time;
                $nestedData[] = $row->remark;

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
