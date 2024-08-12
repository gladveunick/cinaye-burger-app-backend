<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Burger;

class BurgerController extends Controller
{
    //

    // Méthode pour récupérer la liste des burgers
    public function burgerList()
    {
        $burgers = Burger::all(); // Récupère tous les burgers
        return response()->json($burgers, 200);
    }
    
    public function index()
    {
        // Liste tous les burgers non archivés
        $burgers = Burger::where('archive', false)->get();
        return response()->json($burgers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric',
            'image' => 'required|image',
            'description' => 'required|string'
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $burger = Burger::create([
            'nom' => $request->nom,
            'prix' => $request->prix,
            'image' => $imageName,
            'description' => $request->description,
            'archive' => false,
        ]);

        return response()->json($burger, 201);
    }

    public function show($id)
    {
        $burger = Burger::find($id);

        if (!$burger || $burger->archive) {
            return response()->json(['message' => 'Burger non trouvé'], 404);
        }

        return response()->json($burger);
    }


    public function update(Request $request, string $id)
{
    try {
        // Trouver le burger ou lancer une exception si non trouvé
        $burger = Burger::findOrFail($id);

        // Validation conditionnelle
        $validated = $request->validate([
            'nom' => 'nullable|string|max:255',
            'prix' => 'nullable|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gérer le téléchargement de l'image
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validated['image'] = $imageName;
        }

        // Mise à jour des champs modifiés
        $burger->update(array_filter($validated));

        return response()->json($burger, 200);
    } catch (ModelNotFoundException $ex) {
        return response()->json(['error' => 'Burger not found'], 404);
    }
}

    
    

    

    public function archive($id)
    {
        $burger = Burger::find($id);

        if (!$burger) {
            return response()->json(['message' => 'Burger non trouvé'], 404);
        }

        $burger->archive = true;
        $burger->save();

        return response()->json(['message' => 'Burger archivé avec succès']);
    }

    public function restore($id)
    {
        $burger = Burger::find($id);

        if (!$burger) {
            return response()->json(['message' => 'Burger non trouvé'], 404);
        }

        $burger->archive = false;
        $burger->save();

        return response()->json(['message' => 'Burger restauré avec succès']);
    }
}
