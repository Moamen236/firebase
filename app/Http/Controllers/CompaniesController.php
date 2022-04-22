<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = new Company();
        $company = $company->getAll();
        return $company;
    }

    /**
     * Display company resource.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     */
    public function show($id)
    {
        $company = new Company();
        $company = $company->find($id);
        return $company;
    }
    
    /**
     * get all payments by company id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payments($id)
    {
        $company = new Company();
        $company = $company->payments($id);
        return $company;
    }


    /**
     * get company by service
     * 
     * @param $service
     * @return array
     */
    public function findByService(Request $request)
    {
        $service = $request->service;
        $company = new Company();
        $companies = $company->findByService($service);
        $all_companies = [];
        foreach ($companies as $company) {
            $all_companies[] = [
                'id' => $company->id(),
                'name' => $company->data()['name'],
            ];
        }
        return response()->json([
            'companies' => $all_companies,
        ]);
    }
}
