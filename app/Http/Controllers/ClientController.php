<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $clients = Client::when($q, function($query) use ($q) {
                $query->where('name','like',"%$q%")
                      ->orWhere('contact_number','like',"%$q%")
                      ->orWhere('company_name','like',"%$q%");
            })
            ->orderByDesc('id')
            ->paginate(15);

        return view('clients.index', compact('clients','q'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
        Client::create($request->validated());
        return redirect()->route('clients.index')->with('success','Client created');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client->update($request->validated());
        return redirect()->route('clients.index')->with('success','Client updated');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success','Client deleted');
    }
}
