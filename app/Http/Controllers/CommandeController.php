<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\Burger;
use App\Notifications\CommandeTerminee;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Carbon;


class CommandeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'burger_id' => 'required|exists:burgers,id',
            'nom' => 'required|string|max:255',
            'email' => 'required|email',
            'quantite' => 'required|integer|min:1',
        ]);

        $burger = Burger::find($request->burger_id);

        $commande = Commande::create([
            'burger_id' => $request->burger_id,
            'nom' => $request->nom,
            'email' => $request->email,
            'quantite' => $request->quantite,
            'prix_total' => $burger->prix * $request->quantite,
            'status' => Commande::STATUT_EN_COURS,
        ]);

        return response()->json($commande, 201);
    }

    public function show($id)
    {
        $commande = Commande::find($id);

        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        return response()->json($commande);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', Commande::statutsValides()),
            'date_paiement' => 'nullable|date',
            'montant' => 'nullable|numeric',
        ]);

        $commande = Commande::find($id);

        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $commande->status = $request->status;

        if ($request->status === Commande::STATUT_PAYÉ) {
            $commande->date_paiement = now();
            $commande->montant = $request->montant;
        }

        $commande->save();

        return response()->json($commande);
    }

    public function annuler($id)
    {
        $commande = Commande::find($id);

        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $commande->status = Commande::STATUT_ANNULE;
        $commande->save();

        return response()->json(['message' => 'Commande annulée avec succès']);
    }

    public function terminer($id)
    {
        $commande = Commande::find($id);

        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $commande->status = Commande::STATUT_TERMINÉ;
        $commande->save();

         // Envoyer la notification au client
    Notification::route('mail', $commande->email)
    ->notify(new CommandeTerminee($commande));

        return response()->json(['message' => 'Commande terminée avec succès']);
    }

    public function payer($id)
    {
        $commande = Commande::find($id);

        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $commande->status = Commande::STATUT_PAYÉ;
        $commande->date_paiement = now();
        $commande->save();

        return response()->json(['message' => 'Commande marquée comme payée']);
    }

    public function index()
    {
        $commandes = Commande::all();
        return response()->json($commandes);
    }


    // Pour les statistiques

    public function statistiquesJournalieres()
    {
        // Obtenir la date actuelle
        $today = Carbon::now()->startOfDay();
        $tomorrow = Carbon::now()->endOfDay();
    
        // Filtrer les statistiques du jour
        $commandesEnCours = Commande::where('status', 'en cours')
            ->whereBetween('created_at', [$today, $tomorrow])
            ->count();
    
        $commandesValidees = Commande::where('status', 'terminé')
            ->whereBetween('created_at', [$today, $tomorrow])
            ->count();
    
        $commandesAnnulees = Commande::where('status', 'annulé')
            ->whereBetween('created_at', [$today, $tomorrow])
            ->count();
    
        $recettesJournalieres = Commande::where('status', 'payé')
            ->whereBetween('created_at', [$today, $tomorrow])
            ->sum('prix_total');
    
        return response()->json([
            'commandesEnCours' => $commandesEnCours,
            'commandesValidees' => $commandesValidees,
            'commandesAnnulees' => $commandesAnnulees,
            'recettesJournalieres' => $recettesJournalieres
        ]);
    }
    
    public function commandesEnCours()
    {
        // Obtenir la date actuelle
        $today = Carbon::now()->startOfDay();
        $tomorrow = Carbon::now()->endOfDay();
    
        // Filtrer les commandes en cours du jour
        $commandesEnCours = Commande::with('burger')
            ->where('status', 'en cours')
            ->whereBetween('created_at', [$today, $tomorrow])
            ->get();
    
        return response()->json($commandesEnCours);
    }
    

   
    public function commandesAnnuler()
{
    // Obtenir la date actuelle
    $today = Carbon::now()->startOfDay();
    $tomorrow = Carbon::now()->endOfDay();

    // Filtrer les commandes annulées du jour
    $commandesAnnuler = Commande::with('burger')
        ->where('status', 'annulé')
        ->whereBetween('created_at', [$today, $tomorrow])
        ->get();

    return response()->json($commandesAnnuler);
}

    
public function commandesValider()
{
    // Obtenir la date actuelle
    $today = Carbon::now()->startOfDay();
    $tomorrow = Carbon::now()->endOfDay();

    // Filtrer les commandes validées du jour
    $commandesValider = Commande::with('burger')
        ->where('status', 'payé')
        ->whereBetween('created_at', [$today, $tomorrow])
        ->get();

    return response()->json($commandesValider);
}


// Methode pour filtrer


public function filtrerCommandes(Request $request)
{
    $query = Commande::query();

    // Filtrer par burger_id
    if ($request->has('burger_id')) {
        $query->where('burger_id', $request->burger_id);
    }

    // Filtrer par date
    if ($request->has('date')) {
        $date = Carbon::parse($request->date)->startOfDay();
        $query->whereBetween('created_at', [$date, $date->copy()->endOfDay()]);
    }

    // Filtrer par état
    if ($request->has('status')) {
        $status = $request->status;
        if (in_array($status, Commande::statutsValides())) {
            $query->where('status', $status);
        }
    }

    // Filtrer par nom du client
    if ($request->has('nom')) {
        $query->where('nom', 'like', '%' . $request->nom . '%');
    }

    $commandes = $query->with('burger')->get();

    return response()->json($commandes);
}

// public function getMonthlyRevenue()
// {
//     $revenue = Commande::where('status', 'payé')
//         ->selectRaw('DATE_FORMAT(date_paiement, "%Y-%m") as month, SUM(prix_total) as revenue')
//         ->groupBy('month')
//         ->get();

//     return response()->json($revenue);
// }


public function getMonthlyRevenue()
{
    $revenue = Commande::where('status', 'payé')
        ->selectRaw('DATE_FORMAT(date_paiement, "%Y-%m") as month, SUM(prix_total) as revenue')
        ->groupBy('month')
        ->get();

    // Transformer les mois en abréviations
    $formattedRevenue = $revenue->map(function ($item) {
        $date = \DateTime::createFromFormat('Y-m', $item->month);
        $monthName = $date->format('M'); // 'Jan', 'Feb', etc.
        return [
            'month' => $monthName,
            'revenue' => $item->revenue
        ];
    });

    return response()->json($formattedRevenue);
}


}
