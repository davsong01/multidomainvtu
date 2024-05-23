<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerLevel;
use Illuminate\Validation\Rule;

class CustomerLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $levels = CustomerLevel::withCount('customers')->orderBy('order', 'desc')->get();

        return view('admin.customerlevel.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customerlevel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'make_default' => 'nullable',
            'order' => [
                'required', Rule::unique('customer_levels')->where(function ($query) {
                    $query->where('merchant_id', request()->merchant_id);
                })
            ],
            // 'order' => 'required|unique:customer_levels,order',
            'upgrade_amount' => 'required',
        ]);

        if($request->make_default == 'yes'){
            $check = CustomerLevel::where('make_default', 'yes')->first();
            if(!empty($check)){
                return back()->with('error', 'A default Level already exist!');
            };
        }
        
        CustomerLevel::updateOrCreate([
            'name' => $request->name,
            'make_default' => $request->make_default,
            'order' => $request->order,
            'upgrade_amount' => $request->upgrade_amount,
            'merchant_id' => request()->merchant_id
        ]);

        return redirect(route('customerlevel.index',request()->array))->with('message', 'Created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerLevel $customerLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($merchant, $customerlevel)
    {
        $customerlevel = CustomerLevel::where('id', $customerlevel)->first();

        return view('admin.customerlevel.edit', compact('customerlevel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $merchant, $customerlevel)
    {
        $customerlevel = CustomerLevel::where('id', $customerlevel)->first();

        $this->validate($request, [
            'name' => 'required',
            'order' => [
                'required', Rule::unique('customer_levels')->where(function ($query) {
                    $query->where('merchant_id', request()->merchant_id);
                })->ignore($customerlevel->id)
            ],
            'order' => 'required|unique:customer_levels,order,' . $customerlevel->id,
            'upgrade_amount' => 'required',
            'make_default' => 'nullable',
        ]);

        if ($request->make_default == 'yes') {
            $check = CustomerLevel::where('make_default', 'yes')->where('id', '<>',$customerlevel->id)->first();
            if (!empty($check)) {
                return back()->with('error', 'A default Level already exist!');
            };
        }
        $customerlevel->update([
            'name' => $request->name,
            'make_default' => $request->make_default,
            'order' => $request->order,
            'upgrade_amount' => $request->upgrade_amount,
        ]);

        return redirect(route('customerlevel.index', request()->array))->with('message', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($merchant, $customerlevel)
    {
        $customerlevel = CustomerLevel::where('id', $customerlevel)->first();
    
        if ($customerlevel->make_default == 'yes') {
            return back()->with('error', 'Level cannot be deleted because it is the default level');
        }
        if ($customerlevel->customers->count() > 0) {
            return back()->with('error', 'Level cannot be deleted because it has customers');
        } else {
            $customerlevel->delete();
            return back()->with('message', 'Level deleted successfully');
        }
    }
}
