<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ClientRequest;

class ClientController extends Controller
{

    public function __construct()
    {
        // laratrust package
        $this->middleware(['permission:clients-create'])->only('create');
        $this->middleware(['permission:clients-read'])->only('index');
        $this->middleware(['permission:clients-update'])->only('edit');
        $this->middleware(['permission:clients-delete'])->only('destroy');
    }

    public function index(Request $request)
    {

        $clients = Client::when($request->search, function ($q) use ($request) {

            return $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%');
        })->latest()->paginate(5);

        return view('dashboard.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('dashboard.clients.create');
    }

    public function store(ClientRequest $request)
    {
        try {
            $request_data = $request->all();
            // Because the second field of phone is not required we use filter to remove null values
            $request_data['phone'] = array_filter($request->phone);

            Client::create($request_data);

            session()->flash('success', __('site.added_successfully'));
            return redirect()->route('dashboard.clients.index');
        } catch (Exception $ex) {
            session()->flash('fail', __('site.fail'));
            return redirect()->route('dashboard.clients.index');
        }
    }

    public function edit(Client $client)
    {

        return view('dashboard.clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        try {
            $request_data = $request->all();
            $request_data['phone'] = array_filter($request->phone);

            $client->update($request_data);
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.clients.index');
        } catch (Exception $ex) {
            session()->flash('fail', __('site.fail'));
            return redirect()->route('dashboard.clients.index');
        }
    }

    public function destroy(Client $client)
    {
        try {
            $client->delete();
            session()->flash('success', __('site.deleted_successfully'));
            return redirect()->route('dashboard.clients.index');
        } catch (Exception $ex) {
            session()->flash('fail', __('site.fail'));
            return redirect()->route('dashboard.clients.index');
        }
    }
}
