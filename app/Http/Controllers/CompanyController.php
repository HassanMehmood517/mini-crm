<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $companies = Company::all();
        return view('companies.index')->with('companies', $companies);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'logo' => 'bail|nullable|image|dimensions:min_width=100,min_height=100',
            'website' => 'required',
        ]);
        $requestData = $request->all();
        $fileName = time().$request->file('logo')->getClientOriginalName();
        $path = $request->file('logo')->storeAs('images', $fileName, 'public');
        $requestData["logo"] = '/storage/'.$path;
        Company::create($requestData);

        return redirect()->route('companies.index')->with('success', 'The process was successfully completed.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Company $company)
    {
        return view('companies.show',compact('company', $company));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $company = Company::find($id);
        if(!empty($company))
            return view('companies.edit')->with('company', $company);
        else return view('pages.403')->with('error_msg', 'Company with that ID does not exist.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'logo' => 'nullable|image|mimes:jpeg, png, jpg|max:2048|dimensions:min_width=300,min_height=200',
        ]);
        $company = Company::find($id);
        if($request->hasFile('logo')){
            $filenameToStore=$this->upload_file($request);
        }
        $company->name = $request->input('name');
        $company->website = $request->input('website');
        $company->email = $request->input('email');
        if(isset($filenameToStore))
            $company->logo = $filenameToStore;
        $company->save();

        return redirect()->route('companies.index')->with('success', 'The process was successfully completed.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $company = Company::find($id);
        $company->delete();

        return redirect()->route('companies.index')->with('success', 'The company was successfully deleted.');

    }

    public function upload_file($request){
        $filenameWithExt=$request->file('logo')->getClientOriginalName();
        $filename=pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $extension=$request->file('logo')->getClientOriginalExtension();
        $filenameToStore=$filename.'_'.time().'.'.$extension;
        $path=$request->file('logo')->storeAs('public/companies_logo', $filenameToStore);
        return $filenameToStore;
    }
}
