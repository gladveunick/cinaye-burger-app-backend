<!DOCTYPE html>
<html>
<head>
    <title>Facture</title>
</head>
<body>
    <h1>Facture</h1>
    <p>Commande ID: {{ $commande->id }}</p>
    <p>Nom: {{ $commande->nom }}</p>
    <p>Email: {{ $commande->email }}</p>
    <p>Quantité: {{ $commande->quantite }}</p>
    <p>Prix Total: {{ $commande->prix_total }} €</p>
    <p>Merci pour votre commande !</p>
</body>
</html>
