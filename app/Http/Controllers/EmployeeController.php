<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index')->with('employees', $employees);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {

        $companies = Company::all();
        return view('employees.create')->with('companies', $companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required'
        ]);
        $employee = new Employee();
        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->phone = $request->input('phone');
        $company_id=$request->input('company_id');
        if($company_id==-1)
            $company_id=NULL;
        $employee->company_id = $company_id;
        $employee->email = $request->input('email');
        $employee->save();
        return redirect()->route('employees.index')->with('success', 'New Employee was succesfully created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::with('company')->find($id);
        if(!empty($employee))
            return view('employees.show')->with('employee', $employee);
        else return view('pages.403')->with('error_msg', 'Employee with that ID does not exist.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $companies = Company::all();
        $employee = Employee::find($id);
        if(!empty($employee))
            return view('employees.edit', compact('companies'))->with('employee', $employee);
        else return view('pages.403')->with('error_msg', 'Employee with that ID does not exist.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required'
        ]);
        $employee = Employee::find($id);
        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->phone = $request->input('phone');
        $employee->email = $request->input('email');
        $company_id=$request->input('company_id');
        if($company_id==-1)
            $company_id=NULL;
        $employee->company_id = $company_id;
        $employee->save();
        return redirect()->route('employees.index')->with('success', 'Employee was succesfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee was successfully deleted!');
    }
}
