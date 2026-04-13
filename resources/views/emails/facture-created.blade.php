<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px;
                     box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: #1a1a2e; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 20px; }
        .body { padding: 28px 32px; color: #374151; }
        .body p { margin: 0 0 14px; line-height: 1.6; }
        .info-box { background: #f9fafb; border-left: 4px solid #1a1a2e; padding: 14px 18px;
                    border-radius: 4px; margin: 20px 0; }
        .info-box p { margin: 6px 0; font-size: 14px; }
        .footer { background: #f9fafb; padding: 16px 32px; text-align: center;
                  font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>✅ Nouvelle facture créée</h1>
    </div>
    <div class="body">
        <p>Bonjour <strong>{{ $facture->user->name ?? 'Utilisateur' }}</strong>,</p>
        <p>Une nouvelle facture a été créée avec succès dans votre compte.</p>

        <div class="info-box">
            <p><strong>N° Facture :</strong> {{ $facture->numero_facture ?? '#'.$facture->id }}</p>
            <p><strong>Client :</strong> {{ $facture->client->nom ?? '—' }}</p>
            <p><strong>Total :</strong> {{ number_format($facture->total, 0, ',', ' ') }} F CFA</p>
            <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($facture->date)->format('d/m/Y') }}</p>
            <p><strong>Statut :</strong> {{ $facture->statut ?? 'impayée' }}</p>
        </div>

        <p>Vous pouvez consulter et gérer cette facture depuis votre tableau de bord.</p>
    </div>
    <div class="footer">
        Cet email a été envoyé automatiquement. Merci de ne pas y répondre.
    </div>
</div>
</body>
</html>
