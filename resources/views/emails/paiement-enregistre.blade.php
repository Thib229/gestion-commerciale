<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px;
                     box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: #065f46; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 20px; }
        .body { padding: 28px 32px; color: #374151; }
        .body p { margin: 0 0 14px; line-height: 1.6; }
        .info-box { background: #f0fdf4; border-left: 4px solid #065f46; padding: 14px 18px;
                    border-radius: 4px; margin: 20px 0; }
        .info-box p { margin: 6px 0; font-size: 14px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-payee { background: #d1fae5; color: #065f46; }
        .badge-partielle { background: #fef3c7; color: #92400e; }
        .badge-impayee { background: #fee2e2; color: #991b1b; }
        .footer { background: #f9fafb; padding: 16px 32px; text-align: center;
                  font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>💰 Paiement enregistré</h1>
    </div>
    <div class="body">
        @php
            $facture = $paiement->facture;
            $user = $facture->user ?? null;
            $statut = $facture->statut ?? 'impayée';
            $badgeClass = match($statut) {
                'payée' => 'badge-payee',
                'partiellement payée' => 'badge-partielle',
                default => 'badge-impayee',
            };
        @endphp

        <p>Bonjour <strong>{{ $user->name ?? 'Utilisateur' }}</strong>,</p>
        <p>Un paiement a été enregistré sur l'une de vos factures.</p>

        <div class="info-box">
            <p><strong>N° Facture :</strong> {{ $facture->numero_facture ?? '#'.$facture->id }}</p>
            <p><strong>Client :</strong> {{ $facture->client->nom ?? '—' }}</p>
            <p><strong>Montant reçu :</strong> {{ number_format($paiement->montant, 0, ',', ' ') }} F CFA</p>
            <p><strong>Total facture :</strong> {{ number_format($facture->total, 0, ',', ' ') }} F CFA</p>
            <p><strong>Reste à régler :</strong> {{ number_format($facture->reste_a_regler, 0, ',', ' ') }} F CFA</p>
            <p><strong>Nouveau statut :</strong>
                <span class="badge {{ $badgeClass }}">{{ $statut }}</span>
            </p>
        </div>

        <p>Consultez votre tableau de bord pour plus de détails.</p>
    </div>
    <div class="footer">
        Cet email a été envoyé automatiquement. Merci de ne pas y répondre.
    </div>
</div>
</body>
</html>
