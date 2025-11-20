<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class BuscaController extends Controller
{
    /**
     * Busca global no sistema
     */
    public function index(Request $request)
    {
        $query = trim($request->get('q', ''));
        
        if (empty($query)) {
            return redirect()->route('dashboard')
                ->with('info', 'Digite algo para buscar');
        }

        $user = auth()->user();
        $results = [
            'tickets' => collect(),
            'users' => collect(),
            'messages' => collect(),
        ];

        // Busca em tickets (disponível para admin e suporte)
        if (in_array($user->role, ['admin', 'suporte', 'customer_success_manager'])) {
            // Busca por ID, email ou assunto
            $ticketIdsByDirect = Ticket::where(function($q) use ($query) {
                    if (is_numeric($query)) {
                        // Se for numérico, busca por ID exato primeiro
                        $q->where('id', $query)
                          ->orWhere('email', 'like', "%{$query}%")
                          ->orWhere('assunto', 'like', "%{$query}%");
                    } else {
                        // Se não for numérico, busca apenas em email e assunto
                        $q->where('email', 'like', "%{$query}%")
                          ->orWhere('assunto', 'like', "%{$query}%");
                    }
                })
                ->pluck('id');

            // Busca dentro das mensagens dos tickets
            $ticketIdsByMessage = Message::where('message', 'like', "%{$query}%")
                ->distinct()
                ->pluck('ticket_id')
                ->filter();
            
            // Combina ambos os resultados e remove duplicatas
            $allTicketIds = $ticketIdsByDirect->merge($ticketIdsByMessage)->unique();
            
            // Busca todos os tickets encontrados
            $results['tickets'] = Ticket::whereIn('id', $allTicketIds)
                ->orderBy('updated_at', 'desc')
                ->limit(50)
                ->get();
        }

        // Busca em usuários (apenas para admin)
        if ($user->role === 'admin') {
            $users = User::where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->limit(20)
                ->get();
            
            $results['users'] = $users;
        }

        $totalResults = $results['tickets']->count() + $results['users']->count();

        return view('busca.index', compact('results', 'query', 'totalResults'));
    }
}

