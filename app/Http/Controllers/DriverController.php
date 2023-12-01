<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $driver = Driver::with('shift')->get();
        return response()->json($driver, 200);
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
                'number' => 'required|max:255',
                'name' => '',
                'nik' => '',
                'no_sim' => '',
                'rfid' => '',
                'address' => '',
                'start_working' => '',
                'status' => '',
            ]);

            Driver::Create($validatedData);
            return response()->json([
                "status" => "success",
                "message" => "Driver added"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot add driver"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Driver $driver)
    {
        //
        return response()->json($driver->load('shift'), 200);
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
    public function update(Request $request, Driver $driver)
    {
        //
        try {
            $validatedData = $request->validate([
                'number' => 'required|max:255',
                'name' => '',
                'nik' => '',
                'no_sim' => '',
                'rfid' => '',
                'address' => '',
                'start_working' => '',
                'status' => '',

            ]);

            Driver::where('id', $driver->id)->update($validatedData);
            return response()->json([
                "status" => "success",
                "message" => "Edit id " . $driver->id . " success"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot edit driver"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driver $driver)
    {
        //
        try {
            Driver::where('id', $driver->id)->delete();
            return response()->json([
                "status" => "success",
                "message" => "Delete id " . $driver->id . " success"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot delete driver"
            ], 400);
        }
    }

    public function driverList(Request $request)
    {
        // dd($request);
        $columns = array(
            0 => 'drivers.id',
            1 => 'drivers.number',
            2 => 'drivers.name',
            3 => 'drivers.nik',
            4 => 'drivers.no_sim',
            5 => 'drivers.rfid',
            6 => 'drivers.address',
            7 => 'drivers.start_working',
            8 => 'drivers.status',
            9 => 'drivers.created_at',
            10 => 'drivers.updated_at',
        );
        $collection = DB::table('drivers');
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
                $nestedData[] = $row->nik;
                $nestedData[] = $row->no_sim;
                $nestedData[] = $row->rfid;
                $nestedData[] = $row->address;
                $nestedData[] = $row->start_working;
                $nestedData[] = $row->status;
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
