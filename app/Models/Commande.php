<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    // Constantes pour les statuts des commandes
    public const STATUT_EN_COURS = 'en cours';
    public const STATUT_TERMINÉ = 'terminé';
    public const STATUT_ANNULE = 'annulé';
    public const STATUT_PAYÉ = 'payé';

    protected $fillable = [
        'burger_id',
        'nom',
        'email',
        'quantite',
        'prix_total',
        'status',
        'date_paiement',
        'montant',
    ];

    // Définir la relation avec le modèle Burger
    public function burger()
    {
        return $this->belongsTo(Burger::class);
    }

    // Validation des statuts
    public static function statutsValides()
    {
        return [
            self::STATUT_EN_COURS,
            self::STATUT_TERMINÉ,
            self::STATUT_ANNULE,
            self::STATUT_PAYÉ,
        ];
    }
}
