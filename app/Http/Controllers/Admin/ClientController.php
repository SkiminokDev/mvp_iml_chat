<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Список всех клиентов с пагинацией и фильтрами
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $filterId = $request->get('filter_id');
        $filterName = $request->get('filter_name');
        $filterActive = $request->get('filter_active');

        $query = Client::query();

        // Фильтр по ID
        if ($filterId) {
            $query->where('id', $filterId);
        }

        // Фильтр по имени
        if ($filterName) {
            $query->where('name', 'like', '%' . $filterName . '%');
        }

        // Фильтр по статусу
        if ($filterActive !== null && $filterActive !== '') {
            $query->where('active', (bool) $filterActive);
        }

        $clients = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.clients.index', compact('clients', 'perPage', 'filterId', 'filterName', 'filterActive'));
    }

    /**
     * Детальная страница клиента
     */
    public function show($clientId)
    {
        $client = Client::findOrFail($clientId);
        
        // Получаем чаты клиента
        $chats = $client->chats()->latest()->paginate(20);

        return view('admin.clients.show', compact('client', 'chats'));
    }
}
