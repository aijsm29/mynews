<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Profile;

use App\Models\ProfileHistory;

use Carbon\Carbon;

class ProfileController extends Controller
{
    //
    public function add()
    {
        return view('admin.profile.create');
    }

    public function create(Request $request)
    {
         // 以下を追記
        // Validationを行う
        $this->validate($request,Profile::$rules);
        
        $profile = new Profile;
        $form = $request->all();
        
        //フォームから送信されてきた_tokenを削除する
        unset($form['_token']);
       
         //データベースに保存する
        $profile->fill($form);
        $profile->save();
        
          // admin/profile/createにリダイレクトする
        return redirect('admin/profile/create');
    }
        public function edit(Request $request)
    {
        $profile = Profile::find($request->id);
        if(empty($profile)) {
            abort(404);
        }
        return view('admin.profile.edit', ['profile_form' =>$profile]);
        }

    public function update(Request $request)
    { // Validationをかける
        $this->validate($request, Profile::$rules);
        // Profile Modelsからデータを取得する
        $profile = Profile::find($request->id);
        //送信されてきたフォームデータを格納する
        $profile_form = $request->all();
        unset($profile_form['_token']);
        
        //該当するデータを上書きして保存する
        $profile->fill($profile_form)->save();
        
        $history = new ProfileHistory();
        $history->profile_id = $profile->id;
        $history->edited_at = Carbon::now();
        $history->save();
        
        return redirect('admin/profile/edit?id=' . $profile->id);
    }
}
