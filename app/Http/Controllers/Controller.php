<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Country;
use App\Models\User;
use App\Models\PayoutPreference;
use App\Http\Start\Helpers;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function add(Request $request)
    {
        if(!$_POST)
        {
            return view('admin.country.add');
        }
        else if($request->submit)
        {
            // Add Country Validation Rules
            $rules = array(
                    'short_name' => 'required|unique:country',
                    'long_name'  => 'required|unique:country',
                    'phone_code' => 'required',
                    'status' => 'required'
                    );

            // Add Country Validation Custom Names
            $niceNames = array(
                        'short_name' => 'Short Name',
                        'long_name'  => 'Long Name',
                        'phone_code' => 'Phone Code',
                        'status' => 'Status'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $country = new Country;

                $country->short_name = $request->short_name;
                $country->long_name  = $request->long_name;
                $country->iso3       = $request->iso3;
                $country->status   = $request->status;
                $country->phone_code = $request->phone_code;

                $country->save();

                $this->helper->flash_message('success', 'Added Successfully'); // Call flash message function

                return redirect()->route('country.view');
            }
        }
        else
        {
            return redirect()->route('country.view');
        }
    }

    /**
     * Update Country Details
     *
     * @param array $request    Input values
     * @return redirect     to Country View
     */
    public function update(Request $request)
    {
        if(!$_POST)
        {
            $data['result'] = Country::find($request->id);

            return view('admin.country.edit', $data);
        }
        else if($request->submit)
        {
            // Edit Country Validation Rules
            $rules = array(
                    'short_name' => 'required|unique:country,short_name,'.$request->id,
                    'long_name'  => 'required|unique:country,long_name,'.$request->id,
                    'phone_code' => 'required'
                    );

            // Edit Country Validation Custom Fields Name
            $niceNames = array(
                        'short_name' => 'Short Name',
                        'long_name'  => 'Long Name',
                        'phone_code' => 'Phone Code'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $country = Country::find($request->id);

                $country->short_name = $request->short_name;
                $country->long_name  = $request->long_name;
                $country->iso3       = $request->iso3;
                $country->status   = $request->status;
                $country->phone_code = $request->phone_code;

                $country->save();

                $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function

                return redirect()->route('country.view');
            }
        }
        else
        {
            return redirect()->route('country.view');
        }
    }

    /**
     * Delete Country
     *
     * @param array $request    Input values
     * @return redirect     to Country View
     */
    public function delete(Request $request)
    { 
        $country_cc = Country::where('id',$request->id)->first();

          if($country_cc) 
          {
            $country_code = $country_cc->short_name;
            
            $delete = User::where('country_id',$request->id)->count();
            $payout=PayoutPreference::where('country',$country_code)->count();
           
           
            if($delete)
            {
                flashMessage('error','This country can\'t be deleted, it\'s used by some users.');
               return redirect()->route('country.view');
            }
             if($payout)
            {
                flashMessage('error','This country can\'t be deleted, it\'s used by some users.');
               return redirect()->route('country.view');
            }
       
            $country_cc->delete();
            $this->helper->flash_message('success', 'Deleted Successfully'); 
            // Call flash message function
            return redirect()->route('country.view');
        }
        else{
            $this->helper->flash_message('danger', 'Deleted Already');
            return redirect()->route('country.view');
        }
    }
}
