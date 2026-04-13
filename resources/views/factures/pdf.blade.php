<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $facture->numero_facture ?? '#'.$facture->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; background: #fff; }
        .container { padding: 30px; max-width: 800px; margin: 0 auto; }

        /* En-tête */
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; vertical-align: top; text-align: right; }
        .company-logo { max-width: 120px; max-height: 80px; margin-bottom: 8px; }
        .company-name { font-size: 18px; font-weight: bold; color: #1a1a2e; margin-bottom: 4px; }
        .company-info { font-size: 11px; color: #555; line-height: 1.6; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #1a1a2e; margin-bottom: 8px; }
        .invoice-meta { font-size: 12px; color: #555; line-height: 1.8; }
        .invoice-meta strong { color: #333; }

        /* Séparateur */
        .divider { border: none; border-top: 2px solid #1a1a2e; margin: 20px 0; }

        /* Infos client */
        .client-section { margin-bottom: 25px; }
        .section-title { font-size: 13px; font-weight: bold; color: #1a1a2e; text-transform: uppercase;
                         letter-spacing: 0.5px; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .client-info { font-size: 12px; color: #444; line-height: 1.7; }

        /* Tableau produits */
        .products-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .products-table thead tr { background-color: #1a1a2e; color: #fff; }
        .products-table thead th { padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; }
        .products-table thead th.text-right { text-align: right; }
        .products-table tbody tr { border-bottom: 1px solid #eee; }
        .products-table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .products-table tbody td { padding: 9px 12px; font-size: 12px; }
        .products-table tbody td.text-right { text-align: right; }

        /* Totaux */
        .totals-section { width: 100%; display: table; margin-bottom: 25px; }
        .totals-spacer { display: table-cell; width: 55%; }
        .totals-box { display: table-cell; width: 45%; }
        .totals-row { display: table; width: 100%; border-bottom: 1px solid #eee; }
        .totals-label { display: table-cell; padding: 7px 12px; font-size: 12px; color: #555; }
        .totals-value { display: table-cell; padding: 7px 12px; font-size: 12px; text-align: right; font-weight: bold; }
        .totals-row.total-final { background-color: #1a1a2e; color: #fff; border-radius: 4px; }
        .totals-row.total-final .totals-label,
        .totals-row.total-final .totals-value { color: #fff; font-size: 14px; }
        .totals-row.reste { background-color: #fff3cd; }
        .totals-row.reste .totals-label,
        .totals-row.reste .totals-value { color: #856404; }

        /* Statut */
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px;
                        font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-payee { background-color: #d4edda; color: #155724; }
        .status-partielle { background-color: #fff3cd; color: #856404; }
        .status-impayee { background-color: #f8d7da; color: #721c24; }

        /* Conditions de paiement */
        .conditions-section { margin-top: 20px; padding: 12px; background-color: #f8f9fa;
                              border-left: 3px solid #1a1a2e; border-radius: 2px; }
        .conditions-section p { font-size: 11px; color: #555; line-height: 1.6; }

        /* Pied de page */
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999;
                  border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
<div class="container">

    <!-- En-tête : logo + infos entreprise à gauche, titre facture à droite -->
    <div class="header">
        <div class="header-left">
            @if(!empty($entrepriseProfile) && !empty($entrepriseProfile->logo_path))
                <img src="{{ storage_path('app/public/' . $entrepriseProfile->logo_path) }}"
                     alt="Logo" class="company-logo">
            @endif

            @if(!empty($entrepriseProfile))
                <div class="company-name">{{ $entrepriseProfile->nom }}</div>
                <div class="company-info">
                    @if($entrepriseProfile->adresse)
                        {{ $entrepriseProfile->adresse }}<br>
                    @endif
                    @if($entrepriseProfile->telephone)
                        Tél : {{ $entrepriseProfile->telephone }}<br>
                    @endif
                    @if($entrepriseProfile->email)
                        Email : {{ $entrepriseProfile->email }}<br>
                    @endif
                    @if($entrepriseProfile->numero_fiscal)
                        IFU : {{ $entrepriseProfile->numero_fiscal }}
                    @endif
                </div>
            @endif
        </div>

        <div class="header-right">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-meta">
                <strong>N° :</strong> {{ $facture->numero_facture ?? 'FAC-'.$facture->id }}<br>
                <strong>Date :</strong> {{ \Carbon\Carbon::parse($facture->date)->format('d/m/Y') }}<br>
                @if($facture->statut)
                    <strong>Statut :</strong>
                    @php
                        $statusClass = match($facture->statut) {
                            'payée' => 'status-payee',
                            'partiellement payée' => 'status-partielle',
                            default => 'status-impayee',
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $facture->statut }}</span>
                @endif
            </div>
        </div>
    </div>

    <hr class="divider">

    <!-- Informations client -->
    <div class="client-section">
        <div class="section-title">Facturé à</div>
        <div class="client-info">
            <strong>{{ $facture->client->nom }}</strong><br>
            @if($facture->client->email)
                {{ $facture->client->email }}<br>
            @endif
            @if($facture->client->telephone)
                Tél : {{ $facture->client->telephone }}<br>
            @endif
            @if($facture->client->adresse)
                {{ $facture->client->adresse }}
            @endif
        </div>
    </div>

    <!-- Tableau des produits -->
    <div class="section-title">Détail des prestations</div>
    <table class="products-table">
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Prix unitaire</th>
                <th class="text-right">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->produits as $produit)
                <tr>
                    <td>{{ $produit->nom }}</td>
                    <td class="text-right">{{ $produit->pivot->quantite }}</td>
                    <td class="text-right">{{ number_format($produit->pivot->prix, 0, ',', ' ') }} F</td>
                    <td class="text-right">{{ number_format($produit->pivot->prix * $produit->pivot->quantite, 0, ',', ' ') }} F</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totaux -->
    <div class="totals-section">
        <div class="totals-spacer"></div>
        <div class="totals-box">
            <div class="totals-row total-final">
                <div class="totals-label">Total HT</div>
                <div class="totals-value">{{ number_format($facture->total, 0, ',', ' ') }} F</div>
            </div>
            <div class="totals-row">
                <div class="totals-label">Montant payé</div>
                <div class="totals-value" style="color:#155724;">{{ number_format($facture->montant_paye, 0, ',', ' ') }} F</div>
            </div>
            @if($facture->reste_a_regler > 0)
            <div class="totals-row reste">
                <div class="totals-label">Reste à régler</div>
                <div class="totals-value">{{ number_format($facture->reste_a_regler, 0, ',', ' ') }} F</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Conditions de paiement -->
    @if(!empty($facture->conditions_paiement))
        <div class="conditions-section">
            <div class="section-title" style="margin-bottom:6px;">Conditions de paiement</div>
            <p>{{ $facture->conditions_paiement }}</p>
        </div>
    @endif

    <!-- Pied de page -->
    <div class="footer">
        @if(!empty($entrepriseProfile) && !empty($entrepriseProfile->nom))
            {{ $entrepriseProfile->nom }} —
        @endif
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

</div>
</body>
</html>
