<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = Company::getCompany() ?? new Company();

        return view('company.edit', compact('company'));
    }

    public function update(StoreCompanyRequest $request)
    {
        $company = Company::getCompany();

        $data = $request->only([
            'name',
            'gst_number',
            'address',
            'phone',
            'email',
        ]);
        $data['invoice_terms_and_conditions'] = $request->input('invoice_terms_and_conditions');

        if (!$company) {
            $company = Company::create($data);
        } else {
            $company->update($data);
        }

        if ($request->hasFile('logo')) {
            $company->clearMediaCollection('logo');
            $company->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        }

        return redirect()->route('company.edit')
            ->with('success', 'Company settings updated successfully.');
    }
}
