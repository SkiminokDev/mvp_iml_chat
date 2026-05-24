<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Список всех авторов с пагинацией и фильтрами
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $filterId = $request->get('filter_id');
        $filterName = $request->get('filter_name');

        $query = User::query();

        // Фильтр по ID
        if ($filterId) {
            $query->where('id', $filterId);
        }

        // Фильтр по названию (email)
        if ($filterName) {
            $query->where('email', 'like', '%' . $filterName . '%');
        }

        $authors = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.authors.index', compact('authors', 'perPage', 'filterId', 'filterName'));
    }

    /**
     * Страница сообщений конкретного автора
     */
    public function messages(Request $request, $authorId)
    {
        $author = User::findOrFail($authorId);
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 20);

        // Валидация параметров сортировки
        $allowedSortFields = ['created_at', 'id'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $messages = Message::query()
            ->where('user_id', $authorId)
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.authors.messages', compact('author', 'messages', 'sortBy', 'sortOrder', 'perPage'));
    }
}
