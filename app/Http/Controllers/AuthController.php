<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gestionnaire;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:gestionnaires',
            'mot_de_passe' => 'required|string|min:8', 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Hash the password before saving
        $gestionnaire = new Gestionnaire($request->all());
        $gestionnaire->mot_de_passe = Hash::make($request->mot_de_passe);
        $gestionnaire->save();
    
        return response()->json(['message' => 'Inscription réussie'], 201);
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'mot_de_passe');
        
        $gestionnaire = Gestionnaire::where('email', $credentials['email'])->first();
        
        if ($gestionnaire && Hash::check($credentials['mot_de_passe'], $gestionnaire->mot_de_passe)) {
            // Générer un token si nécessaire
            $token = 'generated_token'; // Remplace ceci par la logique de génération de token
    
            return response()->json(['message' => 'Authentification réussie', 'token' => $token]);
        }
    
        // Authentification échouée
        return response()->json(['message' => 'Identifiants invalides'], 401);
    }
    

    public function logout(Request $request)
    {
        // Assuming you're using Laravel Sanctum or similar for token management
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Déconnexion réussie'], 200);
    }
}
