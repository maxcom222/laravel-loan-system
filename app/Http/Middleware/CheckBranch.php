<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\BranchUser;
use Closure;
use Laracasts\Flash\Flash;
use Sentinel;

class CheckBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('branch_id')) {
            //try to set the session
            if (count(Branch::all()) == 0) {
                //no branches, return
                Flash::warning("No branches set");
                return redirect('no_branch');
            } else {
                //we have branches
                if (count(BranchUser::where('user_id', Sentinel::getUser()->id)->get()) > 0) {
                    //try to set 1 branch as current
                    foreach (BranchUser::where('user_id', Sentinel::getUser()->id)->orderBy('created_at',
                        'desc')->get() as $key) {
                        if (!empty($key->branch)) {
                            //set session and exit
                            $request->session()->put('branch_id', $key->branch_id);
                            //Flash::success("Current Branch set to: ".$key->branch->name);
                            return $next($request);
                        }
                    }
                } else {
                    Flash::warning("No permission");
                    return redirect('no_branch');
                }
            }
            Flash::warning("No branches set");
            return redirect('no_branch');
        } elseif (!empty(Branch::find($request->session()->has('branch_id')))) {
            return $next($request);
        }else{
            Flash::warning("No permission");
            return redirect('no_branch');
        }
    }
}
