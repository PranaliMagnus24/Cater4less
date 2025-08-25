<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThirdPartyCompany;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Scopes\RestaurantScope;
use Brian2694\Toastr\Facades\Toastr;
use App\Exports\ThirdPartyCompanyExport;
use Maatwebsite\Excel\Facades\Excel;

class ThirdPartyCompanyController extends Controller
{
    public function index()
    {
        return view('admin-views.third-party-company.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:191',
            'company_email' => 'required|email|max:191|unique:third_party_companies,company_email',
            'company_phone' => 'required|string|max:20|unique:third_party_companies,company_phone',
            'company_address' => 'required|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $company = new ThirdPartyCompany();
        $company->company_name = $request->company_name;
        $company->company_email = $request->company_email;
        $company->company_phone = $request->company_phone;
        $company->company_address = $request->company_address;

        if ($request->hasFile('image')) {
            $company->image = Helpers::upload(dir: 'company/', format: 'png', image: $request->file('image'));
        }

        $company->save();

        return response()->json([
            'success' => 1,
            'message' => translate('messages.company_added_successfully'),
            'id' => $company->id
        ]);
    }

    public function list(Request $request)
    {
        $key = explode(' ', $request->search);

        $companies = ThirdPartyCompany::when(isset($key), function ($q) use ($key) {
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('company_name', 'like', "%{$value}%")
                        ->orWhere('company_email', 'like', "%{$value}%")
                        ->orWhere('company_phone', 'like', "%{$value}%")
                        ->orWhere('company_address', 'like', "%{$value}%");
                }
            });
        })
            ->latest()
            ->paginate(config('default_pagination'));

        return view('admin-views.third-party-company.list', compact('companies'));
    }

    public function status(Request $request, $id)
    {
        $company = ThirdPartyCompany::findOrFail($id);
        $company->status = $company->status === 'active' ? 'inactive' : 'active';
        $company->save();

        return response()->json([
            'success' => true,
            'status' => $company->status,
            'message' => translate('messages.company_status_updated'),
        ]);
    }

    public function edit($id)
    {
        $company = ThirdPartyCompany::findOrFail($id);
        return view('admin-views.third-party-company.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = ThirdPartyCompany::findOrFail($id);

        // ✅ Validation
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email',
            'company_phone' => 'required|string|max:20',
            'company_address' => 'nullable|string|max:255',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // ✅ Update Basic Fields
        $company->company_name = $request->company_name;
        $company->company_email = $request->company_email;
        $company->company_phone = $request->company_phone;
        $company->company_address = $request->company_address;

        // ✅ Handle Image Upload
        if ($request->hasFile('image')) {
            // Old delete
            if ($company->image) {
                Helpers::check_and_delete('company/', $company->image);
            }
            // New upload
            $imageName = Helpers::upload('company/', 'png', $request->file('image'));
            $company->image = $imageName;
        }

        $company->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => translate('messages.company_updated_successfully')
            ]);
        }

        Toastr::success(translate('messages.company_updated_successfully'));
        return redirect()->route('admin.third-party-company.list');
    }

    public function delete(Request $request)
    {
        $company = ThirdPartyCompany::find($request->id);

        if (!$company) {
            Toastr::error(translate('messages.company_not_found'));
            return back();
        }
        if ($company->image) {
            Helpers::check_and_delete('company/', $company->image);
        }
        $company->delete();

        Toastr::success(translate('messages.company_deleted_successfully'));
        return back();
    }

    public function export(Request $request, $type)
    {
        $search = $request->search ?? null;

        if ($type === 'excel') {
            return Excel::download(new ThirdPartyCompanyExport($search), 'companies.xlsx');
        }

        if ($type === 'csv') {
            return Excel::download(new ThirdPartyCompanyExport($search), 'companies.csv');
        }

        return redirect()->back()->with('error', 'Invalid export type selected!');
    }

}
