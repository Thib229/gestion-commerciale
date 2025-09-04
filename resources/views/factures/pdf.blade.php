<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $facture->id }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Facture #{{ $facture->id }}</h1>
    <p><strong>Date :</strong> {{ $facture->date }}</p>
    <p><strong>Client :</strong> {{ $facture->client->nom }} - {{ $facture->client->email }}</p>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->produits as $produit)
                <tr>
                    <td>{{ $produit->nom }}</td>
                    <td>{{ $produit->pivot->quantite }}</td>
                    <td>{{ number_format($produit->pivot->prix, 0, ',', ' ') }} F</td>
                    <td>{{ number_format($produit->pivot->prix * $produit->pivot->quantite, 0, ',', ' ') }} F</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top: 20px;">Total : {{ number_format($facture->total, 0, ',', ' ') }} F</h3>
</body>
</html>
