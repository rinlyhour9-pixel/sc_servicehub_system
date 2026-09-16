<?php
namespace App\Http\Controllers;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class BusinessSettingController extends Controller {
 public function edit(){return view('settings.business', ['setting'=>BusinessSetting::firstOrCreate([],['business_name'=>'ServiceHub'])]);}
 public function update(Request $request){$data=$request->validate(['business_name'=>['required','string','max:100'],'logo'=>['nullable','image','max:2048']]);$setting=BusinessSetting::firstOrCreate([],['business_name'=>'ServiceHub']);if($request->hasFile('logo')){if($setting->logo_path)Storage::disk('public')->delete($setting->logo_path);$data['logo_path']=$request->file('logo')->store('branding','public');}unset($data['logo']);$setting->update($data);return back()->with('status','Business identity updated.');}
}
