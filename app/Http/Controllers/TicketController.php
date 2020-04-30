<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Sentinel;
use App\Http\Requests;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Ticket::all();
        return view('ticket.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Sentinel::findRoleByName('Client');
        $clients = $role->users()->with('roles')->get();
        $client = array();
        foreach ($clients as $key) {
            $client[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $departments = Department::all();
        $department = array();
        foreach ($departments as $key) {
            $department[$key->id] = $key->name;
        }
        return view('ticket.create', compact('client', 'department'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ticket = new Ticket();
        $ticket->user_id = $request->user_id;
        $ticket->department_id = $request->department_id;
        $ticket->subject = $request->subject;
        $ticket->priority = $request->priority;
        $ticket->body = $request->body;
        if ($request->hasFile('attachment')) {
            $file = array('attachment' => Input::file('attachment'));
            $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning('Validation error');
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $ticket->attachment = $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->move(public_path() . '/uploads',
                    $request->file('attachment')->getClientOriginalName());
            }

        }
        $ticket->save();
        //send email to client
        $client=User::where('id', $ticket->user_id)->first();
        $CompanyName = Setting::where('setting_key', 'company_name')->first()->setting_value;
        $portalAddress = Setting::where('setting_key', 'portal_address')->first()->setting_value;
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $status = '';
        if ($ticket->status == 'open') {
            $status = 'Open';
        } elseif ($ticket->status == 'closed') {
            $status = 'Closed';
        } elseif ($ticket->status == 'in_progress') {
            $status = 'In Progress';
        } elseif ($ticket->status == 'answered') {
            $status = 'Answered';
        }
        $body = Setting::where('setting_key', 'new_ticket_client_template')->first()->setting_value;
        $body = str_replace('{ticketRef}', '#' . $ticket->id, $body);
        $body = str_replace('{companyName}', $CompanyName, $body);
        $body = str_replace('{clientName}', $client->first_name.' '.$client->last_name, $body);
        $body = str_replace('{ticketStatus}', $status, $body);
        $body = str_replace('{ticketLink}', $portalAddress . "/ticket/" . $ticket->id . '/show', $body);
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $email = $client->email;
        Mail::raw($body, function ($message) use ($email,$ticket) {
            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                Setting::where('setting_key', 'company_name')->first()->setting_value);
            $message->to($email);
            $message->setContentType('text/html');
            $message->setSubject(Setting::where('setting_key',
                'new_ticket_client_subject')->first()->setting_value.'-'.$ticket->subject);

        });
        //notify admin
        $client=User::where('id', $ticket->user_id)->first();
        $CompanyName = Setting::where('setting_key', 'company_name')->first()->setting_value;
        $portalAddress = Setting::where('setting_key', 'portal_address')->first()->setting_value;
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $status = '';
        if ($ticket->status == 'open') {
            $status = 'Open';
        } elseif ($ticket->status == 'closed') {
            $status = 'Closed';
        } elseif ($ticket->status == 'in_progress') {
            $status = 'In Progress';
        } elseif ($ticket->status == 'answered') {
            $status = 'Answered';
        }
        $body = Setting::where('setting_key', 'new_ticket_staff_template')->first()->setting_value;
        $body = str_replace('{ticketRef}', '#' . $ticket->id, $body);
        $body = str_replace('{companyName}', $CompanyName, $body);
        $body = str_replace('{clientName}', $client->first_name.' '.$client->last_name, $body);
        $body = str_replace('{ticketStatus}', $status, $body);
        $body = str_replace('{ticketLink}', $portalAddress . "/ticket/" . $ticket->id . '/show', $body);
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $email = $client->email;
        Mail::raw($body, function ($message) use ($email,$ticket) {
            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                Setting::where('setting_key', 'company_name')->first()->setting_value);
            $message->to(Setting::where('setting_key', 'company_email')->first()->setting_value);
            $message->setContentType('text/html');
            $message->setSubject(Setting::where('setting_key',
                    'new_ticket_staff_subject')->first()->setting_value.'-'.$ticket->subject);

        });
        Flash::success("Successfully Saved");
        return redirect('ticket/data');
    }


    public function show($ticket)
    {
        return view('ticket.show', compact('ticket'));
    }


    public function edit($ticket)
    {
        $role = Sentinel::findRoleByName('Client');
        $clients = $role->users()->with('roles')->get();
        $client = array();
        foreach ($clients as $key) {
            $client[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $departments = Department::all();
        $department = array();
        foreach ($departments as $key) {
            $department[$key->id] = $key->name;
        }
        return view('ticket.edit', compact('client', 'department', 'ticket'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $ticket->user_id = $request->user_id;
        $ticket->department_id = $request->department_id;
        $ticket->subject = $request->subject;
        $ticket->priority = $request->priority;
        $ticket->body = $request->body;
        if ($request->hasFile('attachment')) {
            $file = array('attachment' => Input::file('attachment'));
            $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning('Validation error');
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $ticket->attachment = $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->move(public_path() . '/uploads',
                    $request->file('attachment')->getClientOriginalName());
            }

        }
        $ticket->save();
        Flash::success("Successfully Saved");
        return redirect('ticket/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        Ticket::destroy($id);
        TicketReply::where('ticket_id', $id)->delete();
        Flash::success("Successfully Deleted");
        return redirect('ticket/data');
    }

    /*
     * Store ticket reply
     */
    public function storeReply(Request $request, $id)
    {
        $reply = new TicketReply();
        $reply->user_id = $request->user_id;
        $reply->ticket_id = $request->ticket_id;
        $reply->body = $request->message;
        if ($request->hasFile('attachment')) {
            $file = array('attachment' => Input::file('attachment'));
            $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning('Validation error');
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $reply->attachment = $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->move(public_path() . '/uploads',
                    $request->file('attachment')->getClientOriginalName());
            }

        }
        //send email to client
        $ticket=Ticket::find($request->ticket_id);
        $client=User::where('id', $ticket->user_id)->first();
        $CompanyName = Setting::where('setting_key', 'company_name')->first()->setting_value;
        $portalAddress = Setting::where('setting_key', 'portal_address')->first()->setting_value;
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $status = '';
        if ($ticket->status == 'open') {
            $status = 'Open';
        } elseif ($ticket->status == 'closed') {
            $status = 'Closed';
        } elseif ($ticket->status == 'in_progress') {
            $status = 'In Progress';
        } elseif ($ticket->status == 'answered') {
            $status = 'Answered';
        }
        $body = Setting::where('setting_key', 'new_ticket_reply_template')->first()->setting_value;
        $body = str_replace('{ticketRef}', '#' . $ticket->id, $body);
        $body = str_replace('{companyName}', $CompanyName, $body);
        $body = str_replace('{ticketReply}', $reply->body, $body);
        $body = str_replace('{clientName}', $client->first_name.' '.$client->last_name, $body);
        $body = str_replace('{ticketStatus}', $status, $body);
        $body = str_replace('{ticketLink}', $portalAddress . "/ticket/" . $ticket->id . '/show', $body);
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $email = $client->email;
        Mail::raw($body, function ($message) use ($email,$ticket) {
            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                Setting::where('setting_key', 'company_name')->first()->setting_value);
            $message->to($email);
            $message->setContentType('text/html');
            $message->setSubject(Setting::where('setting_key',
                    'new_ticket_reply_subject')->first()->setting_value.'-'.$ticket->subject);

        });
        $reply->save();
        Flash::success("Successfully Saved");
        return redirect('ticket/' . $id . '/show');
    }

    public function deleteReply($id, $rid)
    {
        TicketReply::destroy($rid);
        Flash::success("Successfully Deleted");
        return redirect('ticket/' . $id . '/show');
    }
    public function status($id)
    {
        $ticket=Ticket::find($id);
        //notify client of ticket status change
        if($ticket->status!=$_REQUEST['s']){
            $client=User::where('id', $ticket->user_id)->first();
            $CompanyName = Setting::where('setting_key', 'company_name')->first()->setting_value;
            $portalAddress = Setting::where('setting_key', 'portal_address')->first()->setting_value;
            $companyLogo = '<a href="' . Setting::where('setting_key',
                    'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                        'company_logo')->first()->setting_value) . '" height="150"/></a>';
            $status = '';
            if ($_REQUEST['s'] == 'open') {
                $status = 'Open';
            } elseif ($_REQUEST['s']== 'closed') {
                $status = 'Closed';
            } elseif ($_REQUEST['s'] == 'in_progress') {
                $status = 'In Progress';
            } elseif ($_REQUEST['s'] == 'answered') {
                $status = 'Answered';
            }
            $body = Setting::where('setting_key', 'ticket_status_template')->first()->setting_value;
            $body = str_replace('{ticketRef}', '#' . $ticket->id, $body);
            $body = str_replace('{companyName}', $CompanyName, $body);
            $body = str_replace('{clientName}', $client->first_name.' '.$client->last_name, $body);
            $body = str_replace('{ticketStatus}', $status, $body);
            $body = str_replace('{ticketLink}', $portalAddress . "/ticket/" . $ticket->id . '/show', $body);
            $body = str_replace('{companyLogo}', $companyLogo, $body);
            $email = $client->email;
            Mail::raw($body, function ($message) use ($email,$ticket) {
                $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                    Setting::where('setting_key', 'company_name')->first()->setting_value);
                $message->to($email);
                $message->setContentType('text/html');
                $message->setSubject(Setting::where('setting_key',
                        'ticket_status_subject')->first()->setting_value.'-'.$ticket->subject);

            });
        }
        $ticket->status=$_REQUEST['s'];
        $ticket->save();
        Flash::success("Successfully Saved");
        return redirect('ticket/' . $id . '/show');
    }
    public function indexClient()
    {
        $data = Ticket::where('user_id',Sentinel::getUSer()->id)->get();
        return view('ticket.client.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createClient()
    {

        $departments = Department::all();
        $department = array();
        foreach ($departments as $key) {
            $department[$key->id] = $key->name;
        }
        return view('ticket.client.create', compact( 'department'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeClient(Request $request)
    {
        $ticket = new Ticket();
        $ticket->user_id = $request->user_id;
        $ticket->department_id = $request->department_id;
        $ticket->subject = $request->subject;
        $ticket->priority = $request->priority;
        $ticket->body = $request->body;
        if ($request->hasFile('attachment')) {
            $file = array('attachment' => Input::file('attachment'));
            $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning('Validation error');
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $ticket->attachment = $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->move(public_path() . '/uploads',
                    $request->file('attachment')->getClientOriginalName());
            }

        }
        $ticket->save();
        //send email to client
        $client=User::where('id', $ticket->user_id)->first();
        $CompanyName = Setting::where('setting_key', 'company_name')->first()->setting_value;
        $portalAddress = Setting::where('setting_key', 'portal_address')->first()->setting_value;
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $status = '';
        if ($ticket->status == 'open') {
            $status = 'Open';
        } elseif ($ticket->status == 'closed') {
            $status = 'Closed';
        } elseif ($ticket->status == 'in_progress') {
            $status = 'In Progress';
        } elseif ($ticket->status == 'answered') {
            $status = 'Answered';
        }
        $body = Setting::where('setting_key', 'new_ticket_client_template')->first()->setting_value;
        $body = str_replace('{ticketRef}', '#' . $ticket->id, $body);
        $body = str_replace('{companyName}', $CompanyName, $body);
        $body = str_replace('{clientName}', $client->first_name.' '.$client->last_name, $body);
        $body = str_replace('{ticketStatus}', $status, $body);
        $body = str_replace('{ticketLink}', $portalAddress . "/ticket/" . $ticket->id . '/show', $body);
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $email = $client->email;
        Mail::raw($body, function ($message) use ($email,$ticket) {
            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                Setting::where('setting_key', 'company_name')->first()->setting_value);
            $message->to($email);
            $message->setContentType('text/html');
            $message->setSubject(Setting::where('setting_key',
                    'new_ticket_client_subject')->first()->setting_value.'-'.$ticket->subject);

        });
        //notify admin
        $client=User::where('id', $ticket->user_id)->first();
        $CompanyName = Setting::where('setting_key', 'company_name')->first()->setting_value;
        $portalAddress = Setting::where('setting_key', 'portal_address')->first()->setting_value;
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $status = '';
        if ($ticket->status == 'open') {
            $status = 'Open';
        } elseif ($ticket->status == 'closed') {
            $status = 'Closed';
        } elseif ($ticket->status == 'in_progress') {
            $status = 'In Progress';
        } elseif ($ticket->status == 'answered') {
            $status = 'Answered';
        }
        $body = Setting::where('setting_key', 'new_ticket_staff_template')->first()->setting_value;
        $body = str_replace('{ticketRef}', '#' . $ticket->id, $body);
        $body = str_replace('{companyName}', $CompanyName, $body);
        $body = str_replace('{clientName}', $client->first_name.' '.$client->last_name, $body);
        $body = str_replace('{ticketStatus}', $status, $body);
        $body = str_replace('{ticketLink}', $portalAddress . "/ticket/" . $ticket->id . '/show', $body);
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $email = $client->email;
        Mail::raw($body, function ($message) use ($email,$ticket) {
            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                Setting::where('setting_key', 'company_name')->first()->setting_value);
            $message->to(Setting::where('setting_key', 'company_email')->first()->setting_value);
            $message->setContentType('text/html');
            $message->setSubject(Setting::where('setting_key',
                    'new_ticket_staff_subject')->first()->setting_value.'-'.$ticket->subject);

        });
        Flash::success("Successfully Saved");
        return redirect('ticket/client/data');
    }


    public function showClient($ticket)
    {
        return view('ticket.client.show', compact('ticket'));
    }


    public function editClient($ticket)
    {

        $departments = Department::all();
        $department = array();
        foreach ($departments as $key) {
            $department[$key->id] = $key->name;
        }
        return view('ticket.client.edit', compact( 'department', 'ticket'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateClient(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $ticket->department_id = $request->department_id;
        $ticket->subject = $request->subject;
        $ticket->priority = $request->priority;
        $ticket->body = $request->body;
        if ($request->hasFile('attachment')) {
            $file = array('attachment' => Input::file('attachment'));
            $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning('Validation error');
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $ticket->attachment = $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->move(public_path() . '/uploads',
                    $request->file('attachment')->getClientOriginalName());
            }

        }
        $ticket->save();
        Flash::success("Successfully Saved");
        return redirect('ticket/client/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteClient($id)
    {
        Ticket::destroy($id);
        TicketReply::where('ticket_id', $id)->delete();
        Flash::success("Successfully Deleted");
        return redirect('ticket/client/data');
    }

    /*
     * Store ticket reply
     */
    public function storeReplyClient(Request $request, $id)
    {
        $reply = new TicketReply();
        $reply->user_id = $request->user_id;
        $reply->ticket_id = $request->ticket_id;
        $reply->body = $request->message;
        if ($request->hasFile('attachment')) {
            $file = array('attachment' => Input::file('attachment'));
            $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning('Validation error');
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $reply->attachment = $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->move(public_path() . '/uploads',
                    $request->file('attachment')->getClientOriginalName());
            }

        }
        //send email to client
        $ticket=Ticket::find($request->ticket_id);
        $client=User::where('id', $ticket->user_id)->first();
        $CompanyName = Setting::where('setting_key', 'company_name')->first()->setting_value;
        $portalAddress = Setting::where('setting_key', 'portal_address')->first()->setting_value;
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $status = '';
        if ($ticket->status == 'open') {
            $status = 'Open';
        } elseif ($ticket->status == 'closed') {
            $status = 'Closed';
        } elseif ($ticket->status == 'in_progress') {
            $status = 'In Progress';
        } elseif ($ticket->status == 'answered') {
            $status = 'Answered';
        }
        $body = Setting::where('setting_key', 'new_ticket_reply_template')->first()->setting_value;
        $body = str_replace('{ticketRef}', '#' . $ticket->id, $body);
        $body = str_replace('{companyName}', $CompanyName, $body);
        $body = str_replace('{ticketReply}', $reply->body, $body);
        $body = str_replace('{clientName}', $client->first_name.' '.$client->last_name, $body);
        $body = str_replace('{ticketStatus}', $status, $body);
        $body = str_replace('{ticketLink}', $portalAddress . "/ticket/" . $ticket->id . '/show', $body);
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $email = $client->email;
        Mail::raw($body, function ($message) use ($email,$ticket) {
            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                Setting::where('setting_key', 'company_name')->first()->setting_value);
            $message->to($email);
            $message->setContentType('text/html');
            $message->setSubject(Setting::where('setting_key',
                    'new_ticket_reply_subject')->first()->setting_value.'-'.$ticket->subject);

        });
        $reply->save();
        Flash::success("Successfully Saved");
        return redirect('ticket/' . $id . '/client/show');
    }

    public function deleteReplyClient($id, $rid)
    {
        TicketReply::destroy($rid);
        Flash::success("Successfully Deleted");
        return redirect('ticket/' . $id . '/client/show');
    }
    public function statusClient($id)
    {
        $ticket=Ticket::find($id);
        //notify client of ticket status change
        if($ticket->status!=$_REQUEST['s']){
            $client=User::where('id', $ticket->user_id)->first();
            $CompanyName = Setting::where('setting_key', 'company_name')->first()->setting_value;
            $portalAddress = Setting::where('setting_key', 'portal_address')->first()->setting_value;
            $companyLogo = '<a href="' . Setting::where('setting_key',
                    'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                        'company_logo')->first()->setting_value) . '" height="150"/></a>';
            $status = '';
            if ($_REQUEST['s'] == 'open') {
                $status = 'Open';
            } elseif ($_REQUEST['s']== 'closed') {
                $status = 'Closed';
            } elseif ($_REQUEST['s'] == 'in_progress') {
                $status = 'In Progress';
            } elseif ($_REQUEST['s'] == 'answered') {
                $status = 'Answered';
            }
            $body = Setting::where('setting_key', 'ticket_status_template')->first()->setting_value;
            $body = str_replace('{ticketRef}', '#' . $ticket->id, $body);
            $body = str_replace('{companyName}', $CompanyName, $body);
            $body = str_replace('{clientName}', $client->first_name.' '.$client->last_name, $body);
            $body = str_replace('{ticketStatus}', $status, $body);
            $body = str_replace('{ticketLink}', $portalAddress . "/ticket/" . $ticket->id . '/show', $body);
            $body = str_replace('{companyLogo}', $companyLogo, $body);
            $email = $client->email;
            Mail::raw($body, function ($message) use ($email,$ticket) {
                $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                    Setting::where('setting_key', 'company_name')->first()->setting_value);
                $message->to($email);
                $message->setContentType('text/html');
                $message->setSubject(Setting::where('setting_key',
                        'ticket_status_subject')->first()->setting_value.'-'.$ticket->subject);

            });
        }
        $ticket->status=$_REQUEST['s'];
        $ticket->save();
        Flash::success("Successfully Saved");
        return redirect('ticket/' . $id . '/client/show');
    }
}
